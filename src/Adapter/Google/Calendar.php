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

use CalendArt\Calendar as BaseCalendar;

/**
 * Calendar model from a Google adapter point of view
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Calendar extends BaseCalendar
{
    /** @var string Calendar's id */
    protected $id;

    /** @var DateTimeZone Calendar's timeZone */
    protected $timeZone;

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

    /**
     * Hydrate a new object from an array of data extracted from a returned json
     *
     * @param array $data JSON interpreted data returned by the calendar's api
     *
     * @throws InvalidArgumentException The data is not valid
     * @return static Calendar instance
     */
    public static function hydrate(array $data)
    {
        if (!isset($data['id'], $data['summary'], $data['timeZone'])) {
            throw new InvalidArgumentException(sprintf('Missing at least one of the mandatory properties "id", "summary" or "timeZone" ; got ["%s"]', implode('", "', array_keys($data))));
        }

        $calendar = new static($data['id'], $data['summary'], new DateTimeZone($data['timeZone']));

        if (isset($data['description'])) {
            $calendar->description = $data['description'];
        }

        return $calendar;
    }
}

