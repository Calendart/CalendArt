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

use CalendArt\AbstractEvent as BaseAbstractEvent,
    CalendArt\EventParticipation as BaseEventParticipation;

/**
 * Event model from a Google adapter point of view
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
abstract class AbstractEvent extends BaseAbstractEvent
{
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_TENTATIVE = 'tentative';
    const STATUS_CONFIRMED = 'confirmed';

    /** @var string Event's id */
    protected $id;

    /** @var string **/
    protected $status;

    /** @var string Event's etag */
    private $etag;

    /** @var User[] All the fetched and hydrated users, with an id as a key **/
    protected static $users = [];

    /** @var array $raw The raw response stored for this object */
    protected $raw;

    public function __construct(Calendar $calendar, $status = self::STATUS_TENTATIVE)
    {
        $this->status   = $status;
        $this->calendar = $calendar;

        $this->participations = new ArrayCollection;

        $calendar->getEvents()->add($this);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        if (!in_array($status, [self::STATUS_CANCELLED, self::STATUS_TENTATIVE, self::STATUS_CONFIRMED])) {
            throw new InvalidArgumentException('Status not recognized');
        }

        $this->status = $status;
    }

    public function getEtag()
    {
        return $this->etag;
    }

    public function getRaw()
    {
        return $this->raw;
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
        if (!isset($data['id'], $data['status'], $data['etag'])) {
            throw new InvalidArgumentException(sprintf('Missing at least one of the mandatory properties "id", "etag", "status" ; got ["%s"]', implode('", "', array_keys($data))));
        }

        $event = new static($calendar, $data['status']);

        $event->id   = $data['id'];
        $event->etag = $data['etag'];
        $event->raw  = $data;

        return $event;
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

