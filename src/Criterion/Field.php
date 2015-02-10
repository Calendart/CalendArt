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
        if (!$this->isRecursive()) {
            return $this->getName();
        }

        $subfields = [];

        foreach ($this->criteria as $criterion) {
            $subfields[] = $criterion->build();
        }

        /*
         * Google field query is built like this :
         *
         * - If it is not a recursive field (we want everything in this field,
         *   or it does not have any sub-component), then it is simply a field
         *   name, which is handled in the top condition of this method
         *
         * - If it is a recursive field, but does not have any "root" (we are
         *   on the root of the tree, so we only have children), then it is
         *   simply a list of comma separated fields' name
         *
         * - Eventually, if it is not the root (hence the field have a name),
         *   it is formed as name(list,of,fields)
         */
        $str = '%s';

        if (null !== $this->getName()) {
            $str = sprintf('%s(%%s)', $this->getName());
        }

        return sprintf($str, implode(',', $subfields));
    }
}

