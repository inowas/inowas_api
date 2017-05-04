<?php

namespace Tests\Inowas\Modflow\Model\Packages;


use Inowas\ModflowModel\Model\Packages\ChdStressPeriodData;
use Inowas\ModflowModel\Model\Packages\ChdStressPeriodGridCellValue;

class AbstractStressPeriodDataTest extends \PHPUnit_Framework_TestCase
{

    /** @var  ChdStressPeriodData */
    protected $chdStressPeriodData;

    public function setUp(): void
    {
        $this->chdStressPeriodData = ChdStressPeriodData::create();
    }

    public function test_serialize_stressperiod_data(): void
    {
        $value = ChdStressPeriodGridCellValue::fromParams(0,0,0,0,1,1);
        $spData = $this->chdStressPeriodData->addGridCellValue($value);
        $json = json_encode($spData);
        $this->assertJson($json);
        $this->assertEquals('{"stress_period_data":{"0":[[0,0,0,1,1]]}}', $json);
    }

    public function test_remove_successive_stressperiod_data(): void
    {

        $values = [
            ChdStressPeriodGridCellValue::fromParams(0,0,0,0,1,1),
            ChdStressPeriodGridCellValue::fromParams(0,0,1,1,1,1),
            ChdStressPeriodGridCellValue::fromParams(1,0,0,0,1,1),
            ChdStressPeriodGridCellValue::fromParams(1,0,1,1,1,1),
            ChdStressPeriodGridCellValue::fromParams(2,0,0,0,2,2)
        ];

        foreach ($values as $value){
            $this->chdStressPeriodData = $this->chdStressPeriodData->addGridCellValue($value);
        }

        $json = json_encode($this->chdStressPeriodData);
        $this->assertJson($json);
        $this->assertEquals('{"stress_period_data":{"0":[[0,0,0,1,1],[0,1,1,1,1]],"2":[[0,0,0,2,2]]}}', $json);
    }
}
