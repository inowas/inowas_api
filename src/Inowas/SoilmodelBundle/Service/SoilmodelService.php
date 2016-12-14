<?php

namespace Inowas\SoilmodelBundle\Service;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\ORM\EntityManager;
use Inowas\PyprocessingBundle\Model\Interpolation\InterpolationConfiguration;
use Inowas\PyprocessingBundle\Model\Interpolation\InterpolationResult;
use Inowas\PyprocessingBundle\Service\Interpolation;
use Inowas\SoilmodelBundle\Model\BoreHole;
use Inowas\SoilmodelBundle\Model\Layer;
use Inowas\SoilmodelBundle\Model\PointValue;
use Inowas\SoilmodelBundle\Model\Property;
use Inowas\SoilmodelBundle\Model\PropertyType;
use Inowas\SoilmodelBundle\Model\PropertyValue;
use Inowas\SoilmodelBundle\Model\Soilmodel;

class SoilmodelService
{
    /** @var  EntityManager $em */
    private $em;

    /** @var  Interpolation */
    protected $interpolation;

    /** @var  SoilModel $soilModel */
    protected $soilModel;

    public function __construct(EntityManager $em, Interpolation $interpolation)
    {
        $this->em = $em;
        $this->interpolation = $interpolation;
    }

    /**
     * @param SoilModel $soilModel
     */
    public function setSoilModel(Soilmodel $soilModel)
    {
        $this->soilModel = $soilModel;
    }

    /**
     * @param Layer $layer
     * @return array
     */
    public function getPropertyTypes(Layer $layer){

        $order = $layer->getOrder();
        $propertyTypes = [];

        /**
         * @var BoreHole $borehole
         */
        foreach ($this->soilModel->getBoreHoles() as $borehole)
        {
            $boreholeLayer = $borehole->getLayerByNumber($order);
            $propertyTypes = array_merge($propertyTypes, $boreholeLayer->getPropertyTypes());
        }

        return array_unique($propertyTypes);
    }

    /**
     * @param Layer $layer
     * @param PropertyType $propertyType
     * @param array $algorithms
     */
    public function interpolateLayerByProperty(Layer $layer, PropertyType $propertyType, array $algorithms)
    {
        $order = $layer->getOrder();
        $pointValues = array();

        /**
         * @var BoreHole $boreHole
         */
        foreach ($this->soilModel->getBoreHoles() as $boreHole){

            $point = $boreHole->getPoint();
            if (! $point instanceof Point){
                continue;
            }

            $boreholeLayer = $boreHole->getLayerByNumber($order);
            if (! $boreholeLayer instanceof Layer){
                continue;
            }

            $property = $boreholeLayer->findPropertyByType($propertyType);

            if (! $property instanceof Property){
                continue;
            }

            $value = $property->getValue()->getValue();
            if (is_null($value)){
                continue;
            }

            $pointValues[] = new PointValue($point, $value);
        }

        $gridSize = $this->soilModel->getGridSize();
        $boundingBox = $this->soilModel->getBoundingBox();

        $interpolationParameter = new InterpolationConfiguration($gridSize, $boundingBox, $pointValues, $algorithms);
        $result = $this->interpolation->interpolate($interpolationParameter);

        if ($result instanceof InterpolationResult)
        {
            $data = $result->getData();
            $layer->addOrReplaceProperty(new Property($propertyType, PropertyValue::fromValue($data)));
            $this->em->persist($layer);
            $this->em->flush();
        }
    }
}