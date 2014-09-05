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

use Doctrine\Common\Collections\Collection,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * Represents a User
 *
 * This class should be extended by the different adapters to specify their
 * needs ; it should hydrate its descendants
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class User implements UserInterface
{
    /** @var string User's name */
    protected $name;

    /** @var string User's email */
    protected $email;

    /** @var Collection<Event> Collection of events the user is involved in */
    protected $events;

    public function __construct($name, $email)
    {
        $this->name  = $name;
        $this->email = $email;

        $this->events = new ArrayCollection;
    }

    /** {@inheritDoc} */
    public function getName()
    {
        return $this->name;
    }

    /** {@inheritDoc} */
    public function getEmail()
    {
        return $this->email;
    }

    /** {@inheritDoc} */
    public function getEvents()
    {
        return $this->events;
    }

    /** {@inheritDoc} */
    public function addEvent(Event $event)
    {
        if ($this->events->contains($event)) {
            return $this;
        }

        $this->events->add($event);

        return $this;
    }

    /** {@inheritDoc} */
    public function removeEvent(Event $event)
    {
        $this->events->removeElement($event);

        return $this;
    }
}

