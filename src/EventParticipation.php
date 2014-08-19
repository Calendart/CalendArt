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

namespace CalendArt;

use Datetime,
    InvalidArgumentException;

/**
 * Represents a participation of a user to an event
 *
 * Like all generic class, this class should be extended to the adapter's need
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class EventParticipation
{
    // status of the participation
    const STATUS_DECLINED  = -1;
    const STATUS_UNDECIDED = 0;
    const STATUS_ACCEPTED  = 1;

    // available roles
    const ROLE_PARTICIPANT = 0b01;
    const ROLE_MANAGER     = 0b10;

    /** @var Event */
    protected $event;

    /** @var User */
    protected $user;

    /** @var integer */
    protected $role = self::ROLE_PARTICIPANT;

    /** @var Datetime */
    protected $invitedAt;

    /** @var Datetime */
    protected $answeredAt = null;

    /** @var integer */
    protected $status = self::STATUS_UNDECIDED;

    public function __construct(Event $event, User $user, $role = self::ROLE_PARTICIPANT, $status = self::STATUS_UNDECIDED)
    {
        $this->user  = $user;
        $this->event = $event;

        $this->invitedAt = new Datetime;

        $this->setRole($role);
        $this->setStatus($status);

        $user->addEvent($event);
    }

    /** @return Datetime|null null if the user has not answered yet to the invitation */
    public function getAnsweredAt()
    {
        return $this->answeredAt;
    }

    /** @return boolean returns true if the user has answered this invitation */
    public function hasAnswered()
    {
        return null !== $this->answeredAt;
    }

    /** @return $this */
    public function setAnsweredAt(Datetime $date = null)
    {
        $this->answeredAt = $date ?: new Datetime;

        return $this;
    }

    /** @return Datetime */
    public function getInvitedAt()
    {
        return $this->invitedAt;
    }

    /** @return Event */
    public function getEvent()
    {
        return $this->event;
    }

    /** @return User */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get the current role of the user in this event
     *
     * @return integer mask that puts the rights of the user
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param integer $role
     *
     * @return $this
     */
    public function setRole($role)
    {
        if (!in_array($role, static::getAvailableRoles())) {
            throw new InvalidArgumentException(sprintf('Role not recognized ; Had "%s", expected one of "%s"', $role, implode('", "', static::getAvailableRoles())));
        }

        $this->role = $role;

        return $this;
    }

    /**
     * Fetch the available roles
     *
     * @return integer[]
     */
    public static function getAvailableRoles()
    {
        return [self::ROLE_PARTICIPANT, self::ROLE_MANAGER];
    }

    /**
     * Get the current status of this participation
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param integer $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        if (!in_array($status, static::getAvailableStatuses())) {
            throw new InvalidArgumentException(sprintf('Status not recognized ; Had "%s", expected one of "%s"', $status, implode('", "', static::getAvailableStatuses())));
        }

        $this->status = $status;

        return $this;
    }

    /**
     * Fetch the available statuses
     *
     * @return integer[]
     */
    public static function getAvailableStatuses()
    {
        return [self::STATUS_DECLINED, self::STATUS_UNDECIDED, self::STATUS_ACCEPTED];
    }
}

