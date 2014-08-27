<?php
/**
 * This file is part of the CalendArt package
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 *
 * @copyright Wisembly
 * @license   http://www.opensource.org/licenses/MIT-License MIT License
 */

namespace CalendArt\Adapter;

use CalendArt\Adapter\Exception\CriteriaNotFoundException;

use IteratorAggregate;

/**
 * Represents a criteria for a query. Can be recursive
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
abstract class AbstractCriteria implements IteratorAggregate
{
    protected $name;

    /** @var self[] Collection of criterias if it is a recursive criteria */
    protected $criterias = [];

    public function __construct($name, array $criterias = [])
    {
        $this->name = $name;

        array_walk($criterias, function (self $criteria) {
            $this->addCriteria($criteria);
        });
    }

    public final function __clone()
    {
        $criterias = [];

        foreach ($this->criterias as $criteria) {
            $criterias[] = clone $criteria;
        }

        $this->criterias = $criterias;
    }

    /**
     * Get the criteria's name
     *
     * @return string Criteria's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add a subcriteria
     *
     * @param self $criteria criteria to add
     * @return $this
     */
    public function addCriteria(self $criteria)
    {
        $this->criterias[] = $criteria;

        return $this;
    }

    /**
     * Get a specific subcriteria
     *
     * @param string $name Name of the criteria to find. If it is already a Criteria, its name will be used
     *
     * @return static The found criteria
     * @throws CriteriaNotFoundException if the criteria is not found
     */
    protected function getCriteria($name)
    {
        if ($name instanceof self) {
            $name = $name->getName();
        }

        foreach ($this->criterias as $criteria) {
            if ($name !== $criteria->getName()) {
                continue;
            }

            return $criteria;
        }

        $list = array_map(function (self $criteria) { return $criteria->getName(); }, $this->criterias);

        throw new CriteriaNotFoundException($list, $name);
    }

    /**
     * Checks if this criteria is a recursive criteria
     *
     * @return boolean true if it is a recursive criteria, false otherwise
     */
    public function isRecursive()
    {
        return !empty($this->criterias);
    }

    /** {@inheritDoc} */
    public function getIterator()
    {
        return new ArrayIterator($this->criterias);
    }

    /**
     * Merge two criteria together
     *
     * @return static
     */
    public function merge(self $criteria)
    {
        if (!$criteria instanceof static) {
            throw new InvalidArgumentException(sprintf('Can\'t merge two different collections. Expected a child of `%s`, got `%s`', __CLASS__, get_class($criteria)));
        }

        if ($this->getName() !== $criteria->getName()) {
            throw new InvalidArgumentException(sprintf('Can\'t merge two different criterias. Had `%s` and `%s`', $this->getName(), $criteria->getName()));
        }

        // none of them are actually recursive... let's return a clone of this current object
        if (!$this->isRecursive() && !$criteria->isRecursive()) {
            return clone $this;
        }

        // is the current collection less specific than the merged collection ? Return the current collection
        if (!$this->isRecursive() && $criteria->isRecursive()) {
            return clone $this;
        }

        // is the collection to be merged less specific than the current collection ? Return the collection to be merged
        if ($this->isRecursive() && !$criteria->isRecursive()) {
            return clone $criteria;
        }

        $merge = clone $this;

        /*
         * Here is where the fun begins...
         *
         * The idea is, at this point, this criteria and the one that we want to
         * merge are both recursive, which means that they are not complete.
         *
         * The part where it is becoming funny (or cranky, it depends on the
         * point of view...) is if the current criteria already has one of the
         * sub-criterias. If that is the case, then we'll need to merge the
         * sub-criteria of the criteria we want to merge ; it it is not within
         * our current criteria, then we just need to add its clone.
         *
         * That's a lot of criterias in the same text, I know.
         */
        foreach ($criteria->criterias as $subCriteria) {
            try {
                $merge->getCriteria($subCriteria)->merge($subCriteria);
            } catch (CriteriaNotFoundException $e) {
                $merge->addCriteria(clone $subCriteria);
            }
        }

        return $merge;

    }

    /**
     * Build the criteria to be understandable by the adapter
     *
     * @return mixed Built query
     */
    abstract public function build();

}

