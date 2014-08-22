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

use Datetime,
    DateTimeZone,
    InvalidArgumentException;

use CalendArt\Event as BaseEvent;

/**
 * Event model from a Google adapter point of view
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Event extends BaseEvent
{
    /** @var string Event's id */
    private $id;

    /** @var Datetime when was this event created */
    private $createdAt;

    /** @var Datetime last updated date */
    private $updatedAt;

    /** @var string where this event is located at */
    private $location;

    public function __construct(Calendar $calendar, User $owner, Datetime $createdAt, Datetime $start, Datetime $end, $id, $name)
    {
        parent::__construct($calendar, $owner, $name, $start, $end);

        $this->id        = $id;
        $this->createdAt = $createdAt;
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
}

