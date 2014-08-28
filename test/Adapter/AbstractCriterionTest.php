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

use CalendArt\Adapter\Exception\CriterionNotFoundException;

class AbstractCriterionTest extends PHPUnit_Framework_TestCase
{
    private $stub;

    public function setUp()
    {
        $this->stub = $this->getMockForAbstractClass('CalendArt\\Adapter\\AbstractCriterion',
                                                     ['foo', [$this->getMockForAbstractClass('CalendArt\\Adapter\\AbstractCriterion', ['bar']),
                                                              $this->getMockForAbstractClass('CalendArt\\Adapter\\AbstractCriterion', ['baz'])]]);
    }

    public function testConstructor()
    {
        $this->assertSame('foo', $this->stub->getName());
        $this->assertCount(2, iterator_to_array($this->stub));
    }

    public function testClone()
    {
        $clone = iterator_to_array(clone $this->stub);

        $this->assertCount(2, $clone);
        $this->assertContainsOnlyInstancesOf('CalendArt\\Adapter\\AbstractCriterion', $clone);
    }

    /** @dataProvider getProvider */
    public function testGetCriterion($criterion)
    {
        $refl = new ReflectionMethod('CalendArt\\Adapter\\AbstractCriterion', 'getCriterion');
        $refl->setAccessible(true);
        $criterion = $refl->invoke($this->stub, $criterion);

        $this->assertInstanceOf('CalendArt\\Adapter\\AbstractCriterion', $criterion);
    }

    /** @dataProvider getProvider */
    public function testDeleteCriterion($criterion)
    {
        $this->assertCount(2, iterator_to_array($this->stub));

        $refl = new ReflectionMethod('CalendArt\\Adapter\\AbstractCriterion', 'deleteCriterion');
        $refl->setAccessible(true);
        $refl->invoke($this->stub, $criterion);

        $this->assertCount(1, iterator_to_array($this->stub));
    }

    public function getProvider()
    {
        return [['bar'],
                [$this->getMockForAbstractClass('CalendArt\\Adapter\\AbstractCriterion', ['bar'])]];
    }

    /**
     * @dataProvider methodProvider
     *
     * @expectedException        CalendArt\Adapter\Exception\CriterionNotFoundException
     * @expectedExceptionMessage The criterion `fubar` was not found. Available criterions are the following : [`bar`, `baz`]
     */
    public function testWrongCriterion($method)
    {
        $refl = new ReflectionMethod('CalendArt\\Adapter\\AbstractCriterion', $method . 'Criterion');
        $refl->setAccessible(true);
        $refl->invoke($this->stub, 'fubar');
    }

    public function methodProvider()
    {
        return [['get'], ['delete']];
    }
}

