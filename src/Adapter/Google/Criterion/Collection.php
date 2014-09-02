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

namespace CalendArt\Adapter\Google\Criterion;

use CalendArt\Adapter\AbstractCriterion,

    CalendArt\Adapter\Google\Criterion\Collection\Element,
    CalendArt\Adapter\Google\Criterion\Collection\Reducible,
    CalendArt\Adapter\Google\Criterion\Collection\Irreducible;

/**
 * Represents a Collection criterion
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Collection extends AbstractCriterion
{
    public function __construct(array $criteria, $name = null)
    {
        parent::__construct($name, $criteria);
    }

    public function build($root = true)
    {
        $render = [];

        foreach ($this->criteria as $criterion) {
            if ($criterion instanceof static) {
                $render[$criterion->getName()] = new Irreducible($criterion->build(false));
                continue;
            }

            $render[] = new Reducible($criterion->build());
        }

        if (!$root && 1 === count($render) && reset($render) instanceof Reducible) {
            $render = $render[0]->value;
        } else {
            array_walk($render, function (&$item) { $item = $item->value; });
        }

        if ($root && null !== $this->getName()) {
            $render = [$this->getName() => $render];
        }

        return $render;
    }
}

