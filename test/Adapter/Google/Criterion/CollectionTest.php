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

namespace CalendArt\Adapter;

use ReflectionMethod;

use PHPUnit_Framework_TestCase;

use CalendArt\Adapter\Google\Criterion\Collection;

class CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testBuildSimpleCollection()
    {
        $criteria = [$this->getCriterion('foo')];

        $collection = new Collection($criteria);
        $collection = $collection->build();

        $this->assertSame('something built', $collection);
    }

    public function testBuildMultipleCollection()
    {
        $criteria = [$this->getCriterion('foo'),
                     $this->getCriterion('bar')];

        $collection = new Collection($criteria);
        $collection = $collection->build();

        $this->assertInternalType('array', $collection);
        $this->assertCount(2, $collection);

        $this->assertArrayHasKey(0, $collection);
        $this->assertSame('something built', $collection[0]);

        $this->assertArrayHasKey(1, $collection);
        $this->assertSame('something built', $collection[1]);
    }

    public function testDeepCollection()
    {
        $criteria = [$this->getCriterion('fubar')];

        $collection = new Collection([new Collection($criteria, 'foo')]);
        $collection = $collection->build();

        $this->assertInternalType('array', $collection);
        $this->assertCount(1, $collection);
        $this->assertArrayHasKey('foo', $collection);
        $this->assertSame('something built', $collection['foo']);
    }

    private function getCriterion($name, array $criteria = [])
    {
        $mock = $this->getMockForAbstractClass('CalendArt\\Adapter\\AbstractCriterion', [$name, $criteria]);

        $mock->expects(static::any())
             ->method('build')
             ->willReturn('something built');

        return $mock;
    }
}

