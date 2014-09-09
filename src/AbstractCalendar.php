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

use Doctrine\Common\Collections\Collection,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * Represents a Calendar
 *
 * Like all generic objects, this object should be extended by the adapter
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
abstract class AbstractCalendar
{
    /** @var string Calendar's name */
    protected $name;

    /** @var string Calendar's description */
    protected $description = '';

    /** @var Collection<AbstractEvent> Collection of events */
    protected $events;

    /** @var Collection<UserPermission> Collection of permissions accorded to this calendar */
    protected $permissions;

    public function __construct($name)
    {
        $this->name = $name;

        $this->events      = new ArrayCollection;
        $this->permissions = new ArrayCollection;
    }

    /** @return mixed */
    abstract public function getId();

    /** @return string */
    public function getName()
    {
        return $this->name;
    }

    /** @return $this */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /** @return string */
    public function getDescription()
    {
        return $this->description;
    }

    /** @return $this */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /** @return Collection<AbstractEvent> */
    public function getEvents()
    {
        return $this->events;
    }

    /** @return $this */
    public function addEvent(AbstractEvent $event)
    {
        if ($this->events->contains($event)) {
            return $this;
        }

        $this->events->add($event);

        return $this;
    }

    /** @return $this */
    public function detachEvent(AbstractEvent $event)
    {
        $this->events->removeElement($event);

        return $this;
    }

    /** @return Collection<UserPermission> */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /** @return $this */
    public function addPermission(UserPermission $permission)
    {
        if ($this->permissions->contains($permission)) {
            return;
        }

        $this->permissions->add($permission);

        return $this;
    }

    /** @return $this */
    public function removePermission($permission)
    {
        $this->permissions->removeElement($permission);

        return $this;
    }
}

