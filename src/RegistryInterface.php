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
 * Represent a Registry to be used to register all used adapters
 *
 * @author Baptiste Clavié <baptiste@wisembly.com>
 */
interface RegistryInterface
{
    /**
     * Get an adapter based on its name
     *
     * @param string $name Name to fetch
     *
     * @throws InvalidArgumentException Adapter not registered
     * @return AdapterInterface
     */
    public function get($name);

    /**
     * Add a new adapter to the stack
     *
     * @param string $name Name to give to the adapter
     *
     * @return $this
     */
    public function add(AdapterInterface $adapter, $name);

    /**
     * Add a new adapter to the stack
     *
     * @param string $name Name of the adapter to remove
     *
     * @throws InvalidArgumentException Adapter not registered
     * @return $this
     */
    public function remove($name);
}
