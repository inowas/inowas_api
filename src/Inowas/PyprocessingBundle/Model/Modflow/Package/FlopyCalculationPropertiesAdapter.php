<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ConstantHeadBoundary;
use AppBundle\Entity\GeneralHeadBoundary;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\RechargeBoundary;
use AppBundle\Entity\StreamBoundary;
use AppBundle\Entity\WellBoundary;
use Symfony\Component\Validator\Constraints as Assert;

class FlopyCalculationPropertiesAdapter
{
    /**
     * @var ModFlowModel
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
    public function __construct(ModFlowModel $modFlowModel){
        $this->model = $modFlowModel;
        $this->packages = array('mf', 'dis', 'bas', 'lpf', 'pcg', 'oc');
    }

    public function getPackages(){

        foreach ($this->model->getBoundaries() as $boundary) {
            if ($boundary instanceof StreamBoundary){
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