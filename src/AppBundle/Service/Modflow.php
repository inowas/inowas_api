<?php

namespace AppBundle\Service;


use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\GeologicalUnit;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\Property;
use AppBundle\Entity\SoilModel;
use AppBundle\Model\Interpolation\PointValue;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\RasterFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Modflow
{

    const PROP_TOP_ELEVATION = 'et';
    const PROP_BOTTOM_ELEVATION = 'eb';


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
     * @param $type
     */
    public function interpolateLayerByUnitProperty(GeologicalLayer $layer, $property, $type)
    {
        $geologicalUnits = $layer->getGeologicalUnits();

        if ($property == self::PROP_BOTTOM_ELEVATION) {
            $this->interpolation->setType($type);
            $this->interpolation->setBoundingBox($this->modflowModel->getBoundingBox());
            $this->interpolation->setGridSize($this->modflowModel->getGridSize());

            /** @var GeologicalUnit $geologicalUnit */
            foreach ($geologicalUnits as $geologicalUnit) {
                $this->interpolation->addPoint(new PointValue(
                        $geologicalUnit->getPoint()->getX(),
                        $geologicalUnit->getPoint()->getY(),
                        $geologicalUnit->getBottomElevation()
                ));
            }
        }

        if ($property == self::PROP_TOP_ELEVATION) {
            $this->interpolation->setType($type);
            $this->interpolation->setBoundingBox($this->modflowModel->getBoundingBox());
            $this->interpolation->setGridSize($this->modflowModel->getGridSize());

            /** @var GeologicalUnit $geologicalUnit */
            foreach ($geologicalUnits as $geologicalUnit) {
                $this->interpolation->addPoint(new PointValue(
                    $geologicalUnit->getPoint()->getX(),
                    $geologicalUnit->getPoint()->getY(),
                    $geologicalUnit->getTopElevation()
                ));
            }
        }

        $this->interpolation->interpolate();

        $raster = RasterFactory::create();
        $raster->setGridSize($this->modflowModel->getGridSize());
        $raster->setBoundingBox($this->modflowModel->getBoundingBox());
        $raster->setData($this->interpolation->getData());

        $value = PropertyValueFactory::create()
            ->setRaster($raster);

        $propertyType = $this->em->getRepository('AppBundle:PropertyType')
            ->findOneBy(array(
                'abbreviation' => $property
            ));

        if (!$propertyType) {
            throw new NotFoundHttpException(sprintf('PropertyType with abbreviation "%s" not found.', $property));
        }

        $property = $layer->getPropertyWithPropertyType($propertyType);

        if (is_null($property)) {
            $property = PropertyFactory::create()
                ->setPropertyType($propertyType);
        }

        $property->addValue(PropertyValueFactory::create()->setRaster($raster));
        $layer->addProperty($property);
        
        $this->em->persist($layer);
        $this->em->persist($property);
        $this->em->flush();
    }
}