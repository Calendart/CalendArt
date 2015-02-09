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

use InvalidArgumentException;

use CalendArt\User as BaseUser;

/**
 * Represents a User
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class User extends BaseUser
{
    /** @var string user's identifier */
    private $id;

    /** @var array $raw The raw response stored for this object */
    protected $raw;

    public function __construct($name, $email, $id = null)
    {
        parent::__construct($name, (array) $email);

        $this->id = $id;
    }

    public function getId()
    {
        return $this->id ?: sha1($this->getEmail() . $this->getName());
    }

    /**
     * {@inheritDoc}
     *
     * This adds also the handling of multiple emails, as a google account may
     * have more than one email
     *
     * @param boolean $all Fetch _all_ the emails ?
     * @return array|string
     */
    public function getEmail($all = false)
    {
        if (!$all && is_array($this->email)) {
            return reset($this->email) ?: null;
        }

        return parent::getEmail();
    }

    public function getRaw()
    {
        return $this->raw;
    }

    public static function hydrate(array $data)
    {
        if (!isset($data['displayName'])) {
            $data['displayName'] = null;
        }

        if (!isset($data['email'])) {
            $data['email'] = null;
        }

        $user = new static($data['displayName'], $data['email']);

        if (isset($data['id'])) {
            $user->id = $data['id'];
        }

        $user->raw = $data;

        return $user;
    }
}

