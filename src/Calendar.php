<?php
/**
 * This file is part of the Calendar package
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 *
 * @copyright Wisembly
 * @license   http://www.opensource.org/licenses/MIT-License MIT License
 */

namespace Calendar;

/**
 * Represents a Calendar
 *
 * Like all generic objects, this object should be extended by the adapter
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Calendar
{
    /** @var string Calendar's name */
    private $name;

    /** @var string Calendar's description */
    private $description = '';

    public function __construct($name)
    {
        $this->name = $name;
    }

    /** @return string */
    public function getName()
    {
        return $this->name;
    }

    /** @return $this */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /** @return string */
    public function getDescription()
    {
        return $this->description;
    }

    /** @return $this */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}

