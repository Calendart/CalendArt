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

use CalendArt\AbstractEvent,
    CalendArt\AbstractCalendar;

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
     * @return Collection<AbstractEvent>
     */
    public function getList(AbstractCriterion $criterion = null);

    /**
     * Returns the specific information for a given event
     *
     * @param mixed $identifier Identifier of the event to fetch
     *
     * @return AbstractEvent
     */
    public function get($identifier, AbstractCriterion $criterion = null);

    /**
     * Get the associated calendar for this api
     *
     * @return AbstractCalendar
     */
    public function getCalendar();
}

