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

use Datetime;

use Doctrine\Common\Collections\Collection,
    Doctrine\Common\Collections\ArrayCollection;

use CalendArt\Adapter\Google\User,
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

    /** @var string Event's etag */
    private $etag;

    /** @var string Where the event is supposed to happen */
    public $location;

    /** @var string Event's visibility */
    private $visibility = self::VISIBILITY_DEFAULT;

    /** @var boolean Can this event overlap other events on the same time frame ? */
    public $overlap = false;

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

    public function hasOverlap()
    {
        return true === $this->overlap;
    }

    public function setOverlap($overlap)
    {
        $this->overlap = (bool) $overlap;

        return $this;
    }

    /**
     * Build the participant collection from a set of User
     *
     * @param array Participant data
     *
     * @return Collection<EventParticipation>
     */
    protected static function buildParticipations(array $data)
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

        if (!isset(static::$userList[$id])) {
            static::$userList[$id] = User::hydrate($data);
        }

        return static::$userList[$id];
    }

    /**
     * Build a Date object based on given data
     *
     * @param array $data Date data
     *
     * @return Datetime
     */
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
}

