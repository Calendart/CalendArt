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

use Doctrine\Common\Collections\Collection,
    Doctrine\Common\Collections\ArrayCollection;

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
    protected $name;

    /** @var string Calendar's description */
    protected $description = '';

    /** @var Collection<Event> Collection of events */
    protected $events;

    public function __construct($name)
    {
        $this->name = $name;

        $this->events = new ArrayCollection;
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

    /** @return Collection<Event> */
    public function getEvents()
    {
        return $this->events;
    }

    /** @return $this */
    public function addEvent(Event $event)
    {
        if ($this->events->contains($event)) {
            return $this;
        }

        $this->events->add($event);

        return $this;
    }

    /** @return $this */
    public function detachEvent(Event $event)
    {
        $this->events->removeElement($event);

        return $this;
    }
}

