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

use CalendArt\Adapter\AbstractCriterion;

/**
 * Represents a Query criterion
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Query extends AbstractCriterion
{
    public function build()
    {
        $query = [];

        foreach ($this->criteria as $criterion) {
            $query[$criterion->getName()] = $criterion->build();
        }

        return $query;
    }
}

