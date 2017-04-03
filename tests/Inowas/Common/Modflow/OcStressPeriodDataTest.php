<?php

namespace Tests\Inowas\Common\Modflow;


use Inowas\Common\Modflow\OcStressPeriod;
use Inowas\Common\Modflow\OcStressPeriodData;

class OcStressPeriodDataTest extends \PHPUnit_Framework_TestCase
{

    public function test_correct_output(): void
    {
        $ocStressPeriodData = OcStressPeriodData::create();
        $ocStressPeriodData->addStressPeriod(OcStressPeriod::fromParams(0,1,['save head']));

        $obj = json_decode(json_encode($ocStressPeriodData));
        $this->assertTrue(is_array($obj->stress_period_data));
        $sp = $obj->stress_period_data[0];
        $this->assertObjectHasAttribute('stressPeriod', $sp);
        $this->assertObjectHasAttribute('timeStep', $sp);
        $this->assertObjectHasAttribute('type', $sp);

        $this->assertEquals(0, $sp->stressPeriod);
        $this->assertEquals(1, $sp->timeStep);
        $this->assertTrue(is_array($sp->type));
        $this->assertEquals(['save head'], $sp->type);
    }
}
