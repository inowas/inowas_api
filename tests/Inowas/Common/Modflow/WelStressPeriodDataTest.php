<?php

declare(strict_types=1);

namespace Tests\Inowas\Common\Modflow;

use Inowas\ModflowModel\Model\Packages\WelStressPeriodData;
use Inowas\ModflowModel\Model\Packages\WelStressPeriodGridCellValue;

class WelStressPeriodDataTest extends \PHPUnit_Framework_TestCase
{

    public function test_add_some_sp_grid_values(): void
    {
        $wellStressperiodData = WelStressPeriodData::create();
        $wellStressperiodData->addGridCellValue(WelStressPeriodGridCellValue::fromParams(0,2,3,4, 7));
        $wellStressperiodData->addGridCellValue(WelStressPeriodGridCellValue::fromParams(1,2,3,4, 5.6));
        $this->assertEquals('{"stress_period_data":{"0":[[2,3,4,7]],"1":[[2,3,4,5.6]]}}', \json_encode($wellStressperiodData));
    }

    public function test_add_values_in_the_same_cell_should_add_them(): void
    {
        $wellStressperiodData = WelStressPeriodData::create();
        $wellStressperiodData->addGridCellValue(WelStressPeriodGridCellValue::fromParams(0,2,3,4, 7));
        $wellStressperiodData->addGridCellValue(WelStressPeriodGridCellValue::fromParams(1,2,3,4, 5));
        $wellStressperiodData->addGridCellValue(WelStressPeriodGridCellValue::fromParams(1,2,3,4, 6));
        $this->assertEquals('{"stress_period_data":{"0":[[2,3,4,7]],"1":[[2,3,4,11]]}}', \json_encode($wellStressperiodData));
    }
}
