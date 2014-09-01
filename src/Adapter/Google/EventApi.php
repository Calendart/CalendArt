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

use CalendArt\Adapter\EventApiInterface,
    CalendArt\Adapter\Google\Exception\ApiErrorException,

    CalendArt\Adapter\AbstractCriterion,
    CalendArt\Adapter\Google\Criterion\Field,
    CalendArt\Adapter\Google\Criterion\Query;

/**
 * Google Adapter for the Calendars
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class EventApi implements EventApiInterface
{
    /** @var Guzzle Guzzle Http Client to use */
    private $guzzle;

    /** @var Calendar */
    private $calendar;

    protected static $query = ['fields' => 'attendees(displayName,email,organizer,responseStatus),created,creator(displayName,email),description,end,id,location,start,status,summary,updated'];

    public function __construct(Guzzle $client, Calendar $calendar)
    {
        $this->guzzle   = $client;
        $this->calendar = $calendar;
    }

    /** {@inheritDoc} */
    public function getList(AbstractCriterion $criterion = null)
    {
        $nextPageToken = null;
        $query         = static::$query;
        $list          = new ArrayCollection;

        if (null !== $this->calendar->getSyncToken()) {
            $query['nextSyncToken'] = $this->calendar->getSyncToken();
        }

        $query['fields'] = sprintf('items(%s),nextSyncToken,nextPageToken', $query['fields']);

        do {
            if (null !== $nextPageToken) {
                $query['nextPageToken'] = $nextPageToken;
            }

            $response = $this->guzzle->get(sprintf('calendars/%s/events', $this->calendar->getId()), ['query' => $query]);

            if (200 > $response->getStatusCode() || 300 <= $response->getStatusCode()) {
                throw new ApiErrorException($response);
            }

            $result = $response->json();

            foreach ($result['items'] as $item) {
                $list[$item['id']] = Event::hydrate($this->calendar, $item);
            }

            $nextPageToken = isset($result['nextPageToken']) ? $result['nextPageToken'] : null;
        } while (null !== $nextPageToken);

        $this->calendar->setSyncToken($result['nextSyncToken']);

        return $list;
    }

    /** {@inheritDoc} */
    public function get($identifier, AbstractCriterion $criterion = null)
    {
        $response = $this->guzzle->get(sprintf('calendars/%s/events/%s', $this->calendar->getId(), $identifier), ['query' => static::$query]);

        if (200 > $response->getStatusCode() || 300 <= $response->getStatusCode()) {
            throw new ApiErrorException($response);
        }

        return Event::hydrate($this->calendar, $response->json());
    }
}

