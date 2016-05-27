<?php

namespace AppBundle\Service;


use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\GeologicalUnit;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\Property;
use AppBundle\Entity\SoilModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Modflow
{

    const PROP_TOP_ELEVATION = 'te';
    const PROP_BOTTOM_ELEVATION = 'be';


    /** @var EntityManager $em */
    protected $em;

    /** @var  Interpolation */
    protected $interpolation;

    /** @var  integer */
    protected $numberOfGeologicalUnits;

    /** @var  ModFlowModel $modflowModel */
    protected $modflowModel;

    /** @var  GeologicalLayer $layer */
    protected $layer;

    /** @var  SoilModel $soilModel */
    protected $soilModel;

    public function __construct(EntityManager $em, Interpolation $interpolation)
    {
        $this->em = $em;
        $this->interpolation = $interpolation;
    }

    public function loadModflowModelById($id)
    {
        $modflowModel = $this->em
            ->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$modflowModel) {
            throw new NotFoundHttpException(printf('ModflowModel with id= %s not found', $id));
        }

        $this->modflowModel = $modflowModel;
    }

    /**
     * @return ModFlowModel
     */
    public function getModflowModel()
    {
        return $this->modflowModel;
    }



    public function loadSoilModelModelById($id)
    {
        $soilModel = $this->em
            ->getRepository('AppBundle:SoilModel')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$soilModel) {
            throw new NotFoundHttpException(printf('SoilModel with id= %s not found', $id));
        }

        $this->soilModel = $soilModel;
    }

    /**
     * @return SoilModel
     */
    public function getSoilModel()
    {
        return $this->soilModel;
    }

    public function loadLayerById($id)
    {
        $layer = $this->em
            ->getRepository('AppBundle:GeologicalLayer')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$layer) {
            throw new NotFoundHttpException(printf('GeologicalLayer with id= %s not found', $id));
        }

        return $layer;
    }

    public function loadPropertyTypesFromLayerUnits($layerId)
    {
        $layer = $this->loadLayerById($layerId);

        $propertyTypes = new ArrayCollection();
        $units = $layer->getGeologicalUnits();

        /** @var GeologicalUnit $unit */
        foreach ($units as $unit)
        {
            $properties = $unit->getProperties();
            /** @var Property $property */
            foreach ($properties as $property)
            {
                $propertyType = $property->getPropertyType();
                if (!is_null($propertyType)) {
                    if (!$propertyTypes->contains($property->getPropertyType())) {
                        $propertyTypes->add($property->getPropertyType());
                    }
                }
            }
        }

        return $propertyTypes;
    }

    /**
     * @param GeologicalLayer $layer
     * @param $property
     */
    public function interpolateLayerByUnitProperty(GeologicalLayer $layer, $property)
    {
        $geologicalUnits = $layer->getGeologicalUnits();

        if ($property == self::PROP_BOTTOM_ELEVATION)
        {
            $this->interpolation->setType(Interpolation::TYPE_KRIGING);

        }

    }


}