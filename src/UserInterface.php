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

namespace CalendArt;

/**
 * Represent a User to be used with the users of the calendars
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
interface UserInterface
{
    /**
     * Get the user's email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Get the user's name
     *
     * @return string
     */
    public function getName();

    /**
     * Get the user's events
     *
     * @return Collection<Event>
     */
    public function getEvents();

    /**
     * Add an event to the User
     *
     * @return $this
     */
    public function addEvent();

    /**
     * Remove an event to the User
     *
     * @return $this
     */
    public function removeEvent();
}

