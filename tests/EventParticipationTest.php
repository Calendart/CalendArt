<?php

namespace Calendart\Tests;
use CalendArt\EventParticipation;

/**
 * Class EventParticipationTest
 * @package Calendart\Test
 * @author Manuel Raynaud <manu@raynaud.io>
 */
class EventParticipationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAvailableStatuses()
    {
        self::assertSame(
            [
                EventParticipation::STATUS_DECLINED,
                EventParticipation::STATUS_TENTATIVE,
                EventParticipation::STATUS_ACCEPTED
            ],
            EventParticipation::getAvailableStatuses()
        );
    }

    /**
     * @dataProvider statusProvider()
     */
    public function testSetValueWithCorrectValue($status)
    {
        $event = $this->prophesize("CalendArt\\AbstractEvent");
        $user = $this->prophesize("CalendArt\\User");
        $participation = new EventParticipation($event->reveal(), $user->reveal());
        $participation->setStatus($status);
    }

    public function statusProvider()
    {
        return [
            [EventParticipation::STATUS_DECLINED],
            [EventParticipation::STATUS_TENTATIVE],
            [EventParticipation::STATUS_ACCEPTED]
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetStatusWithInvalidValueShouldThrowAnException()
    {
        $event = $this->prophesize("CalendArt\\AbstractEvent");
        $user = $this->prophesize("CalendArt\\User");
        $participation = new EventParticipation($event->reveal(), $user->reveal());
        $participation->setStatus(null);
    }

}
