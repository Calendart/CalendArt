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
    CalendArt\Adapter\Google\Exception\ApiErrorException;

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

    public function __construct(Guzzle $client, Calendar $calendar)
    {
        $this->guzzle   = $client;
        $this->calendar = $calendar;
    }

    /** {@inheritDoc} */
    public function getList()
    {
        $nextPageToken = null;
        $list          = new ArrayCollection;

        do {
            $query = ['fields' => 'items(attendees(displayName,email,id),created,creator(displayName,email),description,end,etag,iCalUID,id,location,start,status,summary),nextPageToken,nextSyncToken'];

            if (null !== $nextPageToken) {
                $query['nextPageToken'] = $nextPageToken;
            } elseif (null !== $this->calendar->getSyncToken()) {
                $query['nextSyncToken'] = $this->calendar->getSyncToken();
            }

            $response = $this->guzzle->get(sprintf('/calendars/%s/events', $this->calendar->getId()), ['query' => $query]);

            if (200 > $response->getStatusCode() || 300 <= $response->getStatusCode()) {
                throw new ApiErrorException($response);
            }

            $result = $response->json();

            foreach ($result['items'] as $item) {
                $list[$item['id']] = Event::hydrate($item);
            }

            $nextPageToken = isset($result['nextPageToken']) ? $result['nextPageToken'] : null;
        } while (null !== $nextPageToken);

        $this->calendar->setSyncToken($result['nextSyncToken']);

        return $list;
    }

    /** {@inheritDoc} */
    public function get($identifier)
    {
        $response = $this->guzzle->get(sprintf('/calendars/%s/events/%s', $this->calendar->getId(), $identifier), ['query' => ['fields' => 'attendees,created,description,end,id,kind,location,organizer,recurrence,sequence,start,summary']]);

        if (200 > $response->getStatusCode() || 300 <= $response->getStatusCode()) {
            throw new ApiErrorException($response);
        }

        return Event::hydrate($response->json());
    }
}

