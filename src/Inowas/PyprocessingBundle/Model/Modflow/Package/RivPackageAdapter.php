<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\PropertyTimeValue;
use AppBundle\Entity\StreamBoundary;
use AppBundle\Model\PropertyType;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\RivStressPeriod;

class RivPackageAdapter
{

    /** @var ModFlowModel $model */
    private $model;

    /**
     * RivPackageAdapter constructor.
     * @param ModFlowModel $model
     */
    public function __construct(ModFlowModel $model)
    {
        $this->model = $model;
    }

    /**
     * @return int
     */
    public function getIpakcb(): int
    {
        return 0;
    }

    /**
     * @return mixed
     */
    public function getStressPeriodData()
    {
        $stress_period_data = array();
        $rivers = array();
        $boundaries = $this->model->getBoundaries();

        foreach ($boundaries as $boundary){
            if ($boundary instanceof StreamBoundary){
                $rivers[] = $boundary;
            }
        }

        /** @var StreamBoundary $river */
        foreach ($rivers as $river){

            $rbot = $river->getPropertyByPropertyType(PropertyType::fromAbbreviation(PropertyType::BOTTOM_ELEVATION))
                ->getValues()->first()->getValue();
            $cond = $river->getPropertyByPropertyType(PropertyType::fromAbbreviation(PropertyType::RIVERBED_CONDUCTANCE))
                ->getValues()->first()->getValue();
            $riverStages = $river->getPropertyByPropertyType(PropertyType::fromAbbreviation(PropertyType::RIVER_STAGE))
                ->getValues();

            /** @var PropertyTimeValue $riverStage */
            foreach ($riverStages as $riverStage){
                $spd = array();
                $stage = $riverStage->getValue();

                $activeCells = $river->getActiveCells()->toArray();
                foreach ($activeCells as $nRow => $rows){
                    foreach ($rows as $nCol => $value){
                        if ($value == true){
                            $spd[] = RivStressPeriod::create(0, $nRow, $nCol, $stage, $cond, $rbot);
                        }
                    }
                }
                $stress_period_data[] = $spd;
            }
        }

        return $stress_period_data;
    }

    /**
     * @return null
     */
    public function getDtype()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getOptions()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getNaux()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return 'riv';
    }

    /**
     * @return int
     */
    public function getUnitnumber(): int
    {
        return 18;
    }



}