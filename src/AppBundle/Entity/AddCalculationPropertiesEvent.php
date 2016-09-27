<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationProperties;

/**
 * @ORM\Entity()
 */
class AddCalculationPropertiesEvent extends AddEvent
{
    /**
     * @var FlopyCalculationProperties
     *
     * @ORM\Column(name="calculation_properties", type="flopy_calculation_properties")
     */
    private $calculationProperties;


    public function __construct(FlopyCalculationProperties $calculationProperties)
    {
        parent::__construct();
        $this->calculationProperties = $calculationProperties;
    }

    /**
     * @param ModFlowModel $model
     */
    public function applyTo(ModFlowModel $model){
        $model->setCalculationProperties($this->calculationProperties);
    }
}
