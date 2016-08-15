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

/**
 * Represents an Attachment in a Event
 *
 * Like all generic objects, this object should be extended by the adapter
 */
interface Attachment
{
    /** @return mixed */
    public function getId();

    /** @return string */
    public function getName();

    /** @return string */
    public function getContents();
}
