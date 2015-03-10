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

namespace CalendArt\Adapter\Calendar;

use Doctrine\Common\Collections\Collection;

use CalendArt\AbstractCalendar;

/**
 * Add an entry point to fetch acls for an AbstractCalendar
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
interface AclInterface
{
    /**
     * Returns the permissions for a calendar (and replace all old permissions)
     *
     * @return Collection<UserPermission>
     */
    public function getPermissions(AbstractCalendar $calendar);
}
