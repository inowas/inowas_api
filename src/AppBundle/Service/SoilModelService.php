<?php

namespace AppBundle\Service;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\GeologicalUnit;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyType;
use AppBundle\Entity\SoilModel;
use AppBundle\Model\Interpolation\PointValue;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\RasterFactory;
use AppBundle\Process\InterpolationParameter;
use AppBundle\Process\InterpolationResult;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SoilModelService
{
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

    /**
     * @param ModFlowModel $modflowModel
     * @return SoilModelService
     */
    public function setModflowModel($modflowModel)
    {
        $this->modflowModel = $modflowModel;
        return $this;
    }

    /**
     * @return SoilModel
     */
    public function getSoilModel()
    {
        return $this->soilModel;
    }

    /**
     * @param SoilModel $soilModel
     * @return SoilModelService
     */
    public function setSoilModel($soilModel)
    {
        $this->soilModel = $soilModel;
        return $this;
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
        return $soilModel;
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

    /**
     * @param string $layer |Layer $layer
     * @return ArrayCollection
     */
    public function getAllPropertyTypesFromLayer($layer)
    {
        if (!$layer instanceof GeologicalLayer) {
            $layer = $this->loadLayerById($layer);
        }

        $propertyTypes = new ArrayCollection();
        $units = $layer->getGeologicalUnits();

        /** @var GeologicalUnit $unit */
        foreach ($units as $unit) {
            $properties = $unit->getProperties();
            /** @var Property $property */
            foreach ($properties as $property) {
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
     * @param $propertyTypeAbbreviation
     * @param array $algorithms
     * @return mixed
     */
    public function interpolateLayerByProperty(GeologicalLayer $layer, $propertyTypeAbbreviation, array $algorithms)
    {
        $propertyType = $this->em->getRepository('AppBundle:PropertyType')
            ->findOneBy(array(
                'abbreviation' => $propertyTypeAbbreviation
            ));

        if (!$propertyType) {
            throw new NotFoundHttpException(sprintf('PropertyType with abbreviation "%s" not found.', $propertyTypeAbbreviation));
        }

        $units = $layer->getGeologicalUnits();

        $gridSize = $this->modflowModel->getGridSize();
        $boundingBox = $this->modflowModel->getBoundingBox();
        $pointValues = array();

        /** @var GeologicalUnit $unit */
        foreach ($units as $unit) {

            /** @var Property $property */
            foreach ($unit->getProperties() as $property) {
                if ($property->getPropertyType() == $propertyType) {
                    $value = $unit->getFirstPropertyValue($property);

                    if (!is_null($value)) {
                        $pointValues[] = new PointValue($unit->getPoint(), $value);
                        break;
                    }
                }
            }
        }

        $interpolationParameter = new InterpolationParameter($gridSize, $boundingBox, $pointValues, $algorithms);
        $result = $this->interpolation->interpolate($interpolationParameter);
        
        if ($result instanceof InterpolationResult)
        {
            $propertyValue = PropertyValueFactory::create()
                ->setRaster(RasterFactory::create()
                    ->setGridSize($result->getGridSize())
                    ->setBoundingBox($result->getBoundingBox())
                    ->setData($result->getData())
                    ->setDescription($result->getAlgorithm())
                );

            $layer->addValue($propertyType, $propertyValue);
            $this->em->persist($layer);
            $this->em->flush();
        }
    }
}