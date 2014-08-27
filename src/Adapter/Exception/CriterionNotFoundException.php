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

namespace CalendArt\Adapter\Exception;

use RuntimeException;

/**
 * Used when a criterion is not found within another criterion
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class CriterionNotFoundException extends RuntimeException
{
    /** @var string[] list of available criterions */
    private $available;

    /** @var string Name of the not found criterion */
    private $name;

    public function __construct(array $list, $name)
    {
        $this->name      = $name;
        $this->available = $list;

        parent::__construct(sprintf('The criterion `%s` was not found. Available criterions are the following : [`%s`]', $name, implode('`, `', $list)), 404);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAvailableCriterions()
    {
        return $this->available;
    }
}

