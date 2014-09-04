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

namespace CalendArt;

use InvalidArgumentException;

use CalendArt\Adapter\AdapterInterface;

/**
 * Registers all the different adapters available and instanciated
 *
 * This should serve as a main entry point to the library
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
class Registry implements RegistryInterface
{
    private $adapters = [];

    /** {@inheritDoc} */
    public function add(AdapterInterface $adapter, $name)
    {
        $this->adapters[$name] = $adapter;

        return $this;
    }

    /** {@inheritDoc} */
    public function remove($name)
    {
        if (!isset($this->adapters[$name])) {
            throw new InvalidArgumentException(sprintf('The adapter "%s" does not seem to be registered within this registry', $name));
        }

        unset($this->adapters[$name]);

        return $this;
    }

    /** {@inheritDoc} */
    public function get($name)
    {
        if (!isset($this->adapters[$name])) {
            throw new InvalidArgumentException(sprintf('The adapter "%s" does not seem to be registered within this registry', $name));
        }

        return $this->adapters[$name];
    }
}

