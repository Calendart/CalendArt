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

namespace CalendArt\Adapter\Google\Criterion\Collection;

/**
 * Represents an element of a built collection criterion
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
abstract class Element
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }
}

