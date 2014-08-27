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

namespace CalendArt\Adapter\Google\Criteria;

use CalendArt\Adapter\AbstractCriteria;

/**
 * Represents a Field criteria
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Field extends AbstractCriteria
{
    /** {@inheritDoc} */
    protected function _merge(AbstractCriteria $criteria)
    {
        $return = clone $this;

        foreach ($criteria as $subCriteria) {
            if ($return->hasCriteria($subCriteria)) {
                continue;
            }

            $return->addCriteria(clone $subCriteria);
        }

        return $return;
    }

    /** {@inheritDoc} */
    public function build()
    {
        $criteria = $this->getName();

        if ($this->isRecursive()) {
            $subfields = [];

            foreach ($this->criterias as $criteria) {
                $subfields[] = $criteria->build();
            }

            $criteria .= sprintf('(%s)', implode(',', $subfields));
        }

        return $criteria;
    }
}

