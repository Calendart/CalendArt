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

namespace CalendArt\Adapter\Google\Event;

use Datetime,
    InvalidArgumentException;

use Doctrine\Common\Collections\Collection,
    Doctrine\Common\Collections\ArrayCollection;

use CalendArt\Adapter\Google\User,
    CalendArt\Adapter\Google\Calendar,
    CalendArt\Adapter\Google\AbstractEvent,
    CalendArt\Adapter\Google\EventParticipation;

/**
 * Base event class for google events
 *
 * The one that are complete, you know.
 *
 * @author Baptiste Clavie <baptiste@wisembly.com>
 */
class BasicEvent extends AbstractEvent
{
    const VISIBILITY_DEFAULT      = 'default';
    const VISIBILITY_PUBLIC       = 'public';
    const VISIBILITY_PRIVATE      = 'private';
    const VISIBILITY_CONFIDENTIAL = 'confidential';

    /** @var Datetime */
    private $createdAt;

    /** @var Datetime */
    private $updatedAt;

    /** @var string Where the event is supposed to happen */
    public $location;

    /** @var boolean Can this event be stacked with other events on the same time frame ? */
    private $stackable = false;

    /** @var string Event's visibility */
    private $visibility = self::VISIBILITY_DEFAULT;

    /** @return Datetime */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /** @return Datetime */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getEtag()
    {
        return $this->etag;
    }

    public function getVisibility()
    {
        return $this->visibility;
    }

    public function setVisibility($visibility)
    {
        if (!in_array($visibility, static::getVisibilties())) {
            throw new InvalidArgumentException(sprintf('Wrong visibility ; had "%s", expected one of ["%s"]', $visibility, implode('", "', static::getVisibilties())));
        }

        $this->visibility = $visibility;

        return $this;
    }

    protected static function getVisibilties()
    {
        return [static::VISIBILITY_DEFAULT, static::VISIBILITY_PUBLIC, static::VISIBILITY_PRIVATE, static::VISIBILITY_CONFIDENTIAL];
    }

    /**
     * Determine if this event can be stacked with other events
     *
     * @return Boolean
     */
    public function isStackable()
    {
        return true === $this->stackable;
    }

    public function setStackable($stackable)
    {
        $this->stackable = (bool) $stackable;

        return $this;
    }

    /**
     * Hydrate a new object from an array of data extracted from a returned json
     *
     * @param array $data JSON interpreted data returned by the event's api
     *
     * @throws InvalidArgumentException The data is not valid
     * @return static Event instance
     */
    public static function hydrate(Calendar $calendar, array $data)
    {
        if (!isset($data['creator'], $data['created'], $data['start'])) {
            throw new InvalidArgumentException(sprintf('Missing at least one of the following mandatory properties : "creator", "created" or "start" ; got ["%s"]', implode('", "', array_keys($data))));
        }

        $end = null;

        if (!isset($data['endTimeUnspecified']) || false === $data['endTimeUnspecified']) {
            if (!isset($data['end'])) {
                throw new InvalidArgumentException(sprintf('Missing at least the following mandatory property "end" ; got ["%s"]', implode('", "', array_keys($data))));
            }

            if (!is_array($data['end']))  {
                throw new InvalidArgumentException('The start and the end dates should be an array');
            }

            $end = static::buildDate($data['end']);
        }

        if (!is_array($data['start']))  {
            throw new InvalidArgumentException('The start and the end dates should be an array');
        }

        $event = parent::hydrate($calendar, $data);

        $event->end       = $end;
        $event->createdAt = new Datetime($data['created']);
        $event->updatedAt = new Datetime($data['updated']);
        $event->start     = static::buildDate($data['start']);

        if (isset($data['summary'])) {
            $event->name = $data['summary'];
        }

        if (isset($data['location'])) {
            $event->location = $data['location'];
        }

        if (isset($data['description'])) {
            $event->description = $data['description'];
        }

        if (isset($data['attendees'])) {
            $event->participations = static::buildParticipations($event, $data['attendees']);
        }

        if (isset($data['transparent'])) {
            $event->stackable = true === $data['transparent'];
        }

        static::buildUser($data['creator'])->addEvent($event);

        return $event;
    }

    /**
     * Build the participant collection from a set of User
     *
     * @param array Participant data
     *
     * @return Collection<EventParticipation>
     */
    protected static function buildParticipations(self $event, array $data)
    {
        $participations = new ArrayCollection;

        foreach ($data as $attendee) {
            // if it is a resource, we don't really care about this "attendee"
            if (isset($attendee['resource']) && true === $attendee['resource']) {
                continue;
            }

            $user = static::buildUser($attendee);
            $role = EventParticipation::ROLE_PARTICIPANT;

            if (isset($attendee['organizer']) && true === $attendee['organizer']) {
                $role |= EventParticipation::ROLE_MANAGER;
            }

            $participation = new EventParticipation($event, $user, $role, EventParticipation::translateStatus($attendee['responseStatus']));

            $user->addEvent($event);
            $participations->add($participation);
        }

        return $participations;
    }

    /**
     * Build a User object based on given data
     *
     * @param array $data User data
     *
     * @return User
     */
    protected static function buildUser(array $data)
    {
        if (isset($data['id'])) {
            $id = $data['id'];
        } else {
            $parts = [];

            if (isset($data['email'])) {
                $parts[] = $data['email'];
            }

            if (isset($data['displayName'])) {
                $parts[] = $data['displayName'];
            }

            $id = sha1(implode('', $parts));
        }

        if (!isset(static::$users[$id])) {
            static::$users[$id] = User::hydrate($data);
        }

        return static::$users[$id];
    }
}

