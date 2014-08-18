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
 * Represents a User
 *
 * This class should be extended by the different adapters to specify their
 * needs ; it should hydrate its descendants
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class User
{
    /** @var string User's name */
    protected $name;

    /** @var string User's email */
    protected $email;

    public function __construct($name, $email)
    {
        $this->name  = $name;
        $this->email = $email;
    }

    /** @return string */
    public function getName()
    {
        return $this->name;
    }

    /** @return string */
    public function getEmail()
    {
        return $this->email;
    }
}

