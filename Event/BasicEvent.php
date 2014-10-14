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

use CalendArt\Adapter\Google\AbstractEvent;

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
}

