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
 * Represents a Field criterion
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Field extends AbstractCriterion
{
    /** {@inheritDoc} */
    public function build()
    {
        $criterion = $this->getName();

        if ($this->isRecursive()) {
            $subfields = [];

            foreach ($this->criteria as $criterion) {
                $subfields[] = $criterion->build();
            }

            $criterion .= sprintf('(%s)', implode(',', $subfields));
        }

        return $criterion;
    }
}

