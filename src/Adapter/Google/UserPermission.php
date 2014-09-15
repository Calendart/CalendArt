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

use CalendArt\UserPermission as Base;

/**
 * Represents a UserPermission from a Google point of view
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class UserPermission extends Base
{
    const OWNER = 0b100;

    public static function hydrate(Calendar $calendar, User $user, $role)
    {
        return new static($calendar, $user, self::translateRole($role));
    }

    private static function translateRole($role)
    {
        $flags = parent::NOPE;

        switch ($role) {
            case 'owner':
                $flags |= self::OWNER;

            case 'writer':
                $flags |= parent::WRITE;

            case 'reader':
            case 'freeBusyReader':
                $flags |= parent::READ;

            case 'none': break;

            default:
                throw new InvalidArgumentException(sprintf('Role "%s" not recognized', $role));
        }

        return $flag;
    }
}

