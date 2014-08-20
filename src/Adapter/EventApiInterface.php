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

use CalendArt\Event,
    CalendArt\Calendar;

/**
 * Handle the dialog with the adapter's api for its events
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
interface EventApiInterface
{
    /**
     * Get all the events available on the selected calendar
     *
     * @return Collection<Event>
     */
    public function getList(Calendar $calendar);

    /**
     * Returns the specific information for a given event
     *
     * @param Calendar $calendar   Calendar to look into
     * @param mixed    $identifier Identifier of the event to fetch
     *
     * @return Event
     */
    public function get(Calendar $calendar, $identifier);
}

