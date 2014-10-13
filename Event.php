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

namespace CalendArt\Adapter\Google;

use Doctrine\Common\Collections\Collection,
    Doctrine\Common\Collections\ArrayCollection;

use Datetime,
    DateTimeZone,
    InvalidArgumentException;

use CalendArt\AbstractEvent,
    CalendArt\EventParticipation as BaseEventParticipation;

/**
 * Event model from a Google adapter point of view
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Event extends AbstractEvent
{
    /** @var string Event's id */
    private $id;

    /** @var Datetime when was this event created */
    private $createdAt;

    /** @var Datetime last updated date */
    private $updatedAt;

    /** @var string where this event is located at */
    public $location;

    /** @var User[] All the fetched and hydrated users, with an id as a key **/
    protected static $users = [];

    public function __construct(Calendar $calendar, User $owner, Datetime $createdAt, Datetime $start, Datetime $end, $id, $name)
    {
        parent::__construct($calendar, $owner, $name, $start, $end);

        $this->id        = $id;
        $this->createdAt = $createdAt;
    }

    public function getId()
    {
        return $this->id;
    }

    /** @return Datetime */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /** @return $this */
    public function setUpdatedAt(Datetime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /** @return boolean */
    public function wasUpdated()
    {
        return null !== $this->updatedAt;
    }

    /** @return Datetime */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /** @return $this */
    public function addParticipation(BaseEventParticipation $participation)
    {
        if (!$participation instanceof EventParticipation) {
            throw new InvalidArgumentException('Only a Google EventParticipation may be added as an attendee to a Google Event');
        }

        return parent::addParticipation($participation);
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
        if (!isset($data['id'], $data['summary'], $data['creator'], $data['created'], $data['start'], $data['end'])) {
            throw new InvalidArgumentException(sprintf('Missing at least one of the mandatory properties "id", "summary", "creator", "created", "start" or "end" ; got ["%s"]', implode('", "', array_keys($data))));
        }

        if (!is_array($data['end']) || !is_array($data['start'])) {
            throw new InvalidArgumentException('The start and the end dates should be an array');
        }

        $owner = self::buildUser($data['creator']);
        $end      = self::buildDate($data['end']);
        $start    = self::buildDate($data['start']);
        $created  = new Datetime($data['created']);
        $userList = [$owner->getId() => $owner];

        $event = new static($calendar, $owner, $created, $start, $end, $data['id'], $data['summary']);

        if (isset($data['updated'])) {
            $event->setUpdatedAt(new Datetime($data['updated']));
        }

        if (isset($data['location'])) {
            $event->location = $data['location'];
        }

        if (isset($data['description'])) {
            $event->description = $data['description'];
        }

        if (isset($data['attendees'])) {
            $event->participations = static::buildParticipations($data['attendees']);
        }

            foreach ($data['attendees'] as $attendee) {
                // if it is a resource, we don't really care about this "attendee"
                if (isset($attendee['resource']) && true === $attendee['resource']) {
                    continue;
                }

                if (!isset($userList[$id = self::buildAttendeeId($attendee)])) {
                    $userList[$id] = User::hydrate($attendee);
                }

                $role = EventParticipation::ROLE_PARTICIPANT;

                if (isset($attendee['organizer']) && true === $attendee['organizer']) {
                    $role |= EventParticipation::ROLE_MANAGER;
                }

                $participation = new EventParticipation($event, $userList[$id], $role, EventParticipation::translateStatus($attendee['responseStatus']));

                $userList[$id]->addEvent($event);
                $event->addParticipation($participation);
            }
        }

        return $event;
    }

    protected static function buildDate(array $data)
    {
        if (!isset($data['date']) && !isset($data['dateTime'])) {
            throw new InvalidArgumentException(sprintf('This date seems to be malformed. Expected a `date` or `dateTime` key ; had [`%s`]', implode('`, `', array_keys($data))));
        }

        $date = new Datetime(isset($data['date']) ? $data['date'] : $data['dateTime']);

        if (isset($data['timeZone'])) {
            $date->setTimezone(new DateTimezone($data['timeZone']));
        }

        return $date;
    }

    private static function buildParticipations(array $data)
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

            static::$userList[$id]->addEvent($event);
            $participations->addParticipation($participation);
        }

        return $participations;
    }

    private static function buildUser(array $data)
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

        if (!isset(static::$userList[$id])) {
            static::$userList[$id] = User::hydrate($data);
        }

        return static::$userList[$id];
    }
}

