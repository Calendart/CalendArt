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

use CalendArt\Util\OAuth2Token,

    CalendArt\Adapter\Google\Calendar,
    CalendArt\Adapter\Google\CalendarApi,

    CalendArt\Adapter\AdapterInterface,
    CalendArt\Adapter\EventApiInterface,
    CalendArt\Adapter\CalendarApiInterface;

/**
 * Google Adapter - He knows how to dialog with google's calendars !
 *
 * This requires to have an OAuth2 token established with the following scopes :
 *  - email
 *  - profile
 *  - https://www.googleapis.com/auth/calendar
 *
 * But, as this currently only support reading from the apis, you can may use
 * the following instead of the last one (the full calendar scope) :
 *  - https://www.googleapis.com/auth/calendar.readonly
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class GoogleAdapter implements AdapterInterface
{
    /** @var Client Guzzle client to use when requesting things from google */
    private $guzzle;

    /** @var CalendarApi CalendarApi to use */
    private $calendarApi;

    public function __construct(OAuth2Token $token)
    {
        $this->guzzle = new Guzzle(['base_url' => 'https://www.googleapis.com/calendar/v3/',
                                    'defaults' => ['headers' => ['Authorization' => sprintf('%s %s', $token->type, $token->token)]]]);
    }

    /** {@inheritDoc} */
    public function getCalendarApi()
    {
        if (null === $this->calendarApi)
        {
            $this->calendarApi = new CalendarApi($this->guzzle);
        }

        return $this->calendarApi;
    }

    /** {@inheritDoc} */
    public function getEventApi(Calendar $calendar)
    {
    }
}

