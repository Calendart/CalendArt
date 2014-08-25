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
        return self::STATUS_UNDECIDED === $this->status;
    }

    /** {@inheritDoc} */
    public static function getAvailableStatuses()
    {
        return array_merge(parent::getAvailableStatuses(), [self::STATUS_UNDECIDED]);
    }

    /**
     * Translates the google attendee's status into one we know
     *
     * @throws InvalidArgumentException Status not recognized
     * @return integer The proper status
     */
    public static function translateStatus($status)
    {
        switch ($status) {
            case 'needsAction':
                return self::STATUS_UNDECIDED;

            case 'declined':
                return parent::STATUS_DECLINED;

            case 'tentative':
                return parent::STATUS_TENTATIVE;

            case 'accepted':
                return parent::STATUS_ACCEPTED;

            default:
                throw new InvalidArgumentException(sprintf('Unrecognized status to translate. Expected on of ["needsAction", "declined", "tentative", "accepted"], got "%s"', $status));
        }
    }
}

