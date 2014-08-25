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

    public function getId()
    {
        return $this->id;
    }

    public static function hydrate(array $data)
    {
        if (!isset($data['displayName'], $data['email'])) {
            throw new InvalidArgumentException(sprintf('Missing at least one of the mandatory properties "displayName", "email" ; got ["%s"]', implode('", "', array_keys($data))));
        }

        $user = new static($data['displayName'], $data['email']);

        if (isset($data['id'])) {
            $user->id = $data['id'];
        }

        return $user;
    }
}

