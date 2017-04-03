<?php

namespace Inowas\Common\Modflow;


class WelStressPeriodDataTest extends \PHPUnit_Framework_TestCase
{

    public function test_add_some_sp_grid_values(): void
    {
        $wellStressperiodData = WelStressPeriodData::create();
        $wellStressperiodData->addGridCellValue(StressPeriodGridCellValue::fromParams(0,2,3,4, 7));
        $wellStressperiodData->addGridCellValue(StressPeriodGridCellValue::fromParams(1,2,3,4, 5.6));
        $this->assertEquals('{"stress_periods_data":[[[2,3,4,7]],[[2,3,4,5.6]]]}', \json_encode($wellStressperiodData));
    }

    public function test_add_values_in_the_same_cell_should_add_them(): void
    {
        $wellStressperiodData = WelStressPeriodData::create();
        $wellStressperiodData->addGridCellValue(StressPeriodGridCellValue::fromParams(0,2,3,4, 7));
        $wellStressperiodData->addGridCellValue(StressPeriodGridCellValue::fromParams(1,2,3,4, 5));
        $wellStressperiodData->addGridCellValue(StressPeriodGridCellValue::fromParams(1,2,3,4, 6));
        $this->assertEquals('{"stress_periods_data":[[[2,3,4,7]],[[2,3,4,11]]]}', \json_encode($wellStressperiodData));
    }
}
