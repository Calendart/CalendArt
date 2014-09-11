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

use DateTimeZone,
    InvalidArgumentException;

use CalendArt\AbstractCalendar;

/**
 * Calendar model from a Google adapter point of view
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Calendar extends AbstractCalendar
{
    /** @var string Calendar's id */
    protected $id;

    /** @var DateTimeZone Calendar's timeZone */
    protected $timeZone;

    /** @var When fetching a list, fetch only starting a certain token */
    protected $nextSyncToken = null;

    public function __construct($id, $name, DateTimeZone $timeZone)
    {
        $this->id       = $id;
        $this->timeZone = $timeZone;

        parent::__construct($name);
    }

    /** @return string */
    public function getId()
    {
        return $this->id;
    }

    /** @return DateTimeZone */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /** @return $this */
    public function setSyncToken($token)
    {
        $this->nextSyncToken = $token;

        return $this;
    }

    /** @return string next token to sync the data with */
    public function getSyncToken()
    {
        return $this->nextSyncToken;
    }

    /**
     * Hydrate a new object from an array of data extracted from a returned json
     *
     * @param array $data JSON interpreted data returned by the calendar's api
     *
     * @throws InvalidArgumentException The data is not valid
     * @return static Calendar instance
     */
    public static function hydrate(array $data, User $user = null)
    {
        if (!isset($data['id'], $data['summary'], $data['timeZone'])) {
            throw new InvalidArgumentException(sprintf('Missing at least one of the mandatory properties "id", "summary" or "timeZone" ; got ["%s"]', implode('", "', array_keys($data))));
        }

        $calendar = new static($data['id'], $data['summary'], new DateTimeZone($data['timeZone']));

        if (isset($data['description'])) {
            $calendar->description = $data['description'];
        }

        if (null !== $user && isset($data['accessRole'])) {
            $calendar->addPermission(UserPermission::hydrate($calendar, $user, $data['accessRole']));
        }

        return $calendar;
    }
}

