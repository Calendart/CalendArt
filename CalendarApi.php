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

use GuzzleHttp\Client as Guzzle;

use Doctrine\Common\Collections\ArrayCollection;

use CalendArt\Adapter\Google\Calendar,
    CalendArt\Adapter\Google\Exception\ApiErrorException,

    CalendArt\Adapter\AbstractCriterion,
    CalendArt\Adapter\Google\Criterion\Field,
    CalendArt\Adapter\Google\Criterion\Collection,

    Calendar\AbstractCalendar,
    CalendArt\Adapter\CalendarApiInterface;

/**
 * Google Adapter for the Calendars
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class CalendarApi implements CalendarApiInterface
{
    /** @var Guzzle Guzzle Http Client to use */
    private $guzzle;

    /** @var GoogleAdapter Google Adapter used */
    private $adapter;

    public function __construct(Guzzle $client, GoogleAdapter $adapter)
    {
        $this->guzzle  = $client;
        $this->adapter = $adapter;

        $this->criteria = [new Field('id'),
                           new Field('summary'),
                           new Field('timeZone'),
                           new Field('description')];
    }

    /** {@inheritDoc} */
    public function getList(AbstractCriterion $criterion = null)
    {
        $items = new Field('items', $this->criteria);
        $items->addCriterion(new Field('accessRole'));

        $query = new Collection([new Collection([$items, new Field('nextPageToken'), new Field('nextSyncToken')], 'fields')]);

        if (null !== $criterion) {
            $query = $query->merge($criterion);
        }

        $response = $this->guzzle->get('users/me/calendarList', ['query' => $query->build()]);

        if (200 > $response->getStatusCode() || 300 <= $response->getStatusCode()) {
            throw new ApiErrorException($response);
        }

        $result = $response->json();
        $list   = new ArrayCollection;

        foreach ($result['items'] as $item) {
            $list[$item['id']] = Calendar::hydrate($item, $this->adapter->getUser());
        }

        return $list;
    }

    /** {@inheritDoc} */
    public function get($identifier, AbstractCriterion $criterion = null)
    {
        $query = new Collection([new Collection([new Field(null, $this->criteria)], 'fields')]);

        if (null !== $criterion) {
            $query = $query->merge($criterion);
        }

        $response = $this->guzzle->get(sprintf('calendars/%s', $identifier), ['query' => $query->build()]);

        if (200 > $response->getStatusCode() || 300 <= $response->getStatusCode()) {
            throw new ApiErrorException($response);
        }

        return Calendar::hydrate($response->json(), $this->adapter->getUser());
    }

    public function getPermissions(AbstractCalendar $calendar, AbstractCriterion $criterion = null)
    {
    }
}

