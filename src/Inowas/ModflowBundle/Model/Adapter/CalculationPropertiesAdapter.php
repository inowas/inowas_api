<?php

namespace Inowas\ModflowBundle\Model\Adapter;

use Inowas\ModflowBundle\Model\Boundary\ConstantHeadBoundary;
use Inowas\ModflowBundle\Model\Boundary\GeneralHeadBoundary;
use Inowas\ModflowBundle\Model\Boundary\RechargeBoundary;
use Inowas\ModflowBundle\Model\Boundary\RiverBoundary;
use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\ModflowModel;

class CalculationPropertiesAdapter
{
    /**
     * @var ModflowModel
     */
    protected $model;

    /**
     * @var array
     */
    protected $packages;

    /**
     * DisPackageAdapter constructor.
     * @param ModFlowModel $modFlowModel
     */
    public function __construct(ModflowModel $modFlowModel){
        $this->model = $modFlowModel;
        $this->packages = array('mf', 'dis', 'bas', 'lpf', 'pcg', 'oc');
    }

    public function getPackages(){

        foreach ($this->model->getBoundaries() as $boundary) {
            if ($boundary instanceof RiverBoundary){
                $this->addPackage('riv');
            }

            if ($boundary instanceof WellBoundary){
                $this->addPackage('wel');
            }

            if ($boundary instanceof RechargeBoundary){
                $this->addPackage('rch');
            }

            if ($boundary instanceof ConstantHeadBoundary){
                $this->addPackage('chd');
            }

            if ($boundary instanceof GeneralHeadBoundary){
                $this->addPackage('ghb');
            }
        }

        return $this->packages;
    }

    /**
     * @param string $package
     */
    protected function addPackage(string $package)
    {
        if (! in_array($package, $this->packages) ){
            $this->packages[] = $package;
        }
    }
}
