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

use CalendArt\Adapter\Exception\CriterionNotFoundException;

use ArrayIterator,
    IteratorAggregate,

    InvalidArgumentException;

/**
 * Represents a criterion for a query. Can be recursive
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
abstract class AbstractCriterion implements IteratorAggregate
{
    protected $name;

    /** @var self[] Collection of criteria if it is a recursive criterion */
    protected $criteria = [];

    public function __construct($name, array $criteria = [])
    {
        $this->name = $name;

        array_walk($criteria, function (self $criterion) {
            $this->addCriterion($criterion);
        });
    }

    public final function __clone()
    {
        $criteria = [];

        foreach ($this->criteria as $criterion) {
            $criteria[$criterion->getName()] = clone $criterion;
        }

        $this->criteria = $criteria;
    }

    /**
     * Get the criterion's name
     *
     * @return string Criterion's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add a subcriterion
     *
     * @param self $criterion criterion to add
     * @return $this
     */
    public function addCriterion(self $criterion)
    {
        $this->criteria[$criterion->getName()] = $criterion;

        return $this;
    }

    /**
     * Get a specific subcriterion
     *
     * @param string $name Name of the criterion to find. If it is already a Criterion, its name will be used
     *
     * @return static The found criterion
     * @throws CriterionNotFoundException if the criterion is not found
     */
    protected function getCriterion($name)
    {
        if ($name instanceof self) {
            $name = $name->getName();
        }

        if (!isset($this->criteria[$name])) {
            throw new CriterionNotFoundException(array_keys($this->criteria), $name);
        }

        return $this->criteria[$name];
    }

    /**
     * Delete a criterion from the criteria stack
     *
     * @param string|self $name Name of the criterion to delete
     *
     * @return $this
     * @throws CriterionNotFoundException If the criterion is not found
     */
    protected function deleteCriterion($name)
    {
        if ($name instanceof self) {
            $name = $name->getName();
        }

        if (!isset($this->criteria[$name])) {
            throw new CriterionNotFoundException(array_keys($this->criteria), $name);
        }

        unset($this->criteria[$name]);

        return $this;
    }

    /**
     * Checks if this criterion is a recursive criterion
     *
     * @return boolean true if it is a recursive criterion, false otherwise
     */
    public function isRecursive()
    {
        return !empty($this->criteria);
    }

    /** {@inheritDoc} */
    public function getIterator()
    {
        return new ArrayIterator($this->criteria);
    }

    /**
     * Merge two criterion together
     *
     * @return static
     */
    public function merge(self $criterion)
    {
        if (!$criterion instanceof static) {
            throw new InvalidArgumentException(sprintf('Can\'t merge two different collections. Expected a child of `%s`, got `%s`', __CLASS__, get_class($criterion)));
        }

        if ($this->getName() !== $criterion->getName()) {
            throw new InvalidArgumentException(sprintf('Can\'t merge two different criteria. Had `%s` and `%s`', $this->getName(), $criterion->getName()));
        }

        // none of them are actually recursive... let's return a clone of this current object
        if (!$this->isRecursive() && !$criterion->isRecursive()) {
            return clone $this;
        }

        // is the current collection less specific than the merged collection ? Return the current collection
        if (!$this->isRecursive() && $criterion->isRecursive()) {
            return clone $this;
        }

        // is the collection to be merged less specific than the current collection ? Return the collection to be merged
        if ($this->isRecursive() && !$criterion->isRecursive()) {
            return clone $criterion;
        }

        $merge = clone $this;

        /*
         * Here is where the fun begins...
         *
         * The idea is, at this point, this criterion and the one that we want to
         * merge are both recursive.
         *
         * The part where it is becoming funny (or cranky, it depends on the
         * point of view...) is if the current criterion already has one of the
         * sub-criteria. If that is the case, then we'll need to merge the
         * sub-criterion of the criterion we want to merge ; if it is not within
         * our current criterion, then we just need to add its clone.
         *
         * That's a lot of criteria in the same text, I know.
         */
        foreach ($criterion->criteria as $subCriterion) {
            try {
                $tmp = $merge->getCriterion($subCriterion);

                $merge->deleteCriterion($tmp);
                $merge->addCriterion($tmp->merge($subCriterion));
            } catch (CriterionNotFoundException $e) {
                $merge->addCriterion(clone $subCriterion);
            }
        }

        return $merge;

    }

    /**
     * Build the criterion to be understandable by the adapter
     *
     * @return mixed Built query
     */
    abstract public function build();

}

