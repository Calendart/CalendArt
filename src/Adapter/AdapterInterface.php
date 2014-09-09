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

use CalendArt\AbstractCalendar;

/**
 * Handle the dialog with an Adapter
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
interface AdapterInterface
{
    /**
     * Get the Calendar API to use for this adapter
     *
     * @return CalendarApiInterface
     */
    public function getCalendarApi();

    /**
     * Get the Event API to use for this adapter (scoped to a particular calendar)
     *
     * @param AbstractCalendar $calendar Calendar to scope this api to
     *
     * @return EventApiInterface
     */
    public function getEventApi(AbstractCalendar $calendar);
}

