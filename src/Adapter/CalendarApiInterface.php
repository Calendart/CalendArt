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

namespace CalendArt\Adapter;

use Doctrine\Common\Collections\Collection;

use CalendArt\AbstractCalendar;

/**
 * Handle the dialog with the adapter's api for its calendars
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
interface CalendarApiInterface
{
    /**
     * Get all the calendars available with the current connexion
     *
     * @return Collection<AbstractCalendar>
     */
    public function getList(AbstractCriterion $criterion = null);

    /**
     * Returns the specific information for a given calendar
     *
     * @param mixed $identifier Identifier of the calendar to fetch
     *
     * @return AbstractCalendar
     */
    public function get($identifier, AbstractCriterion $criterion = null);
}

