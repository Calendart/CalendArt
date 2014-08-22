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
    InvalidArgumentException;

use CalendArt\EventParticipation as BaseEventParticipation;

/**
 * Represents a participation of a user to an event
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class EventParticipation extends BaseEventParticipation
{
    const STATUS_UNDECIDED = null;

    /** {@inheritDoc} */
    public function hasAnswered()
    {
        return STATUS_UNDECIDED === $this->status;
    }

    /** {@inheritDoc} */
    public static function getAvailableStatuses()
    {
        return array_merge(parent::getAvailableStatuses(), [STATUS_UNDECIDED]);
    }
}

