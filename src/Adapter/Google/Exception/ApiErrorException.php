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

namespace CalendArt\Adapter\Google\Exception;

use ErrorException;

use GuzzleHttp\Message\Response;

/**
 * Whenever the Api returns an unexpected result
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class ApiErrorException extends ErrorException
{
    public function __construct(Response $response)
    {
        parent::__construct(sprintf('The request failed and returned an invalid status code ("%d") : %s', $response->getStatusCode(), $response->json()['error']['message']), $response->getStatusCode());
    }
}

