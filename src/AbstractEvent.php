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

use Datetime,
    InvalidArgumentException;

use Doctrine\Common\Collections\Collection,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * Represents an Event in a Calendar
 *
 * Like all generic objects, this object should be extended by the adapter
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
abstract class AbstractEvent
{
    /** @var Datetime Start date of this event */
    protected $start;

    /** @var Datetime End date of this event */
    protected $end;

    /** @var string name of this event */
    protected $name;

    /** @var string Description of this event */
    protected $description;

    /** @var User owner of this event */
    protected $owner;

    /** @var AbstractCalendar Calendar associated to this event */
    protected $calendar;

    /** @var Collection<EventParticipation> Participations registered to this event */
    protected $participations;

    /** @var array $raw The raw response stored for this object */
    protected $raw;

    public function __construct(AbstractCalendar $calendar, User $owner, $name, Datetime $start, Datetime $end)
    {
        $this->name     = $name;
        $this->owner    = $owner;
        $this->calendar = $calendar;

        $this->participations = new ArrayCollection;

        if ($start > $end) {
            throw new InvalidArgumentException('An event cannot start after it was ended');
        }

        $this->end   = $end;
        $this->start = $start;

        $owner->addEvent($this);
        $calendar->getEvents()->add($this);
    }

    /** @return mixed */
    abstract public function getId();

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

    /** @return User */
    public function getOwner()
    {
        return $this->owner;
    }

    /** @return Datetime */
    public function getStart()
    {
        return $this->start;
    }

    /** @return $this */
    public function setStart(Datetime $start)
    {
        if ($this->end < $start) {
            throw new InvalidArgumentException('An event cannot start after it was ended');
        }

        $this->start = $start;

        return $this;
    }

    /** @return Datetime */
    public function getEnd()
    {
        return $this->end;
    }

    /** @return $this */
    public function setEnd(Datetime $end)
    {
        if ($this->start > $end) {
            throw new InvalidArgumentException('An event cannot end before it was started');
        }

        $this->end = $end;

        return $this;
    }

    /**
     * Checks if this event has already ended
     *
     * @return Boolean
     */
    public function hasEnded(Datetime $current = null)
    {
        return $this->end < ($current ?: new Datetime);
    }

    /**
     * Checks if this event has already started
     *
     * @return Boolean
     */
    public function hasStarted(Datetime $current = null)
    {
        return $this->start <= ($current ?: new Datetime);
    }

    /**
     * Checks if this event is currently running
     *
     * @return Boolean
     */
    public function isRunning(Datetime $current = null)
    {
        $current = $current ?: new Datetime;

        return $this->hasStarted($current) && !$this->hasEnded($current);
    }

    /** @return AbstractCalendar */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * Detach this event from the associated Calendar
     *
     * @return $this
     */
    public function detach()
    {
        $this->calendar->getEvents()->removeEvent($this);
        $this->calendar = null;

        return $this;
    }

    /** @return Collection<EventParticipation> */
    public function getParticipations()
    {
        return $this->participations;
    }

    /** @return $this */
    public function addParticipation(EventParticipation $participation)
    {
        $email = $participation->getUser()->getEmail();

        $callback = function ($key, EventParticipation $participation) use ($email) {
            return $email === $participation->getUser()->getEmail();
        };

        if ($this->participations->exists($callback)) {
            return;
        }

        $this->participations->add($participation);

        return $this;
    }

    /** @return $this */
    public function removeParticipation(EventParticipation $participation)
    {
        $this->participations->removeElement($participation);

        return $this;
    }

    public function setRaw(array $raw)
    {
        $this->raw = $raw;
    }

    public function getRaw()
    {
        return $this->raw;
    }
}

