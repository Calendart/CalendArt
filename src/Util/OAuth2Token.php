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

namespace CalendArt\Adapter\Google\Util;

use Datetime,
    InvalidArgumentException;

/**
 * Stores the information about a OAuth2 Token
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class OAuth2Token
{
    /** @var string Access Token returned by the OAuth2 mechanism */
    private $token;

    /** @var string Type of the token returned by the OAuth2 mechanism */
    private $type;

    /** @var Datetime Date of expiration of the token returned by the OAuth2 mechanism */
    private $expiresAt;

    /**
     * @param string  $token      Access token returned by the OAuth2 mechanism
     * @param string  $type       Type of the access token returned by the OAuth2 mechanism
     * @param integer $expiration Number of seconds before the token is considered invalid
     */
    public function __construct($token, $type, $expiration)
    {
        $expiration = (int) $expiration;

        if ($expiration <= 0) {
            throw new InvalidArgumentException('This token seems to have already expired !');
        }

        $this->type      = $type;
        $this->token     = $token;
        $this->expiresAt = new Datetime(sprintf('now + %d seconds', $expiration));
    }

    /** The property should not be modified, hence the private accessibility on them */
    public function __get($prop)
    {
        static $list = ['token', 'type', 'expiresAt'];

        if (!in_array($prop, $list)) {
            throw new InvalidArgumentException(sprintf('Unknown property "%s" for the Session object. Only the following are availables : ["%s"]', $prop, implode('", "', $list)));
        }

        return $this->$prop;
    }

    public function isExpired()
    {
        return $this <= $this->expiresAt;
    }
}

