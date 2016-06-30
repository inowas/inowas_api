<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * SoilModel
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="soil_model")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SoilModelRepository")
 */
class SoilModel extends AbstractModel
{
    /**
     * @var ArrayCollection
     * @JMS\Type("ArrayCollection<AppBundle\Entity\GeologicalLayer>")
     * @JMS\Groups({"details", "modeldetails", "soilmodeldetails"})
     */
    private $geologicalLayers;

    /**
     * @var ArrayCollection
     * @JMS\Groups({"soilmodeldetails"})
     */
    private $geologicalPoints;

    /**
     * @var ArrayCollection
     */
    private $geologicalUnits;

    /**
     * @var Area
     * @JMS\Type("AppBundle\Entity\Area")
     * @JMS\Groups({"modeldetails", "soilmodeldetails"})
     */
    private $area;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->geologicalLayers = new ArrayCollection();
        $this->geologicalPoints = new ArrayCollection();
        $this->geologicalUnits = new ArrayCollection();
    }

    /**
     * @param Area $area
     * @return $this
     */
    public function setArea(Area $area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @return Area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @return ArrayCollection
     */
    public function getGeologicalLayers()
    {
        return $this->geologicalLayers;
    }

    /**
     * @return ArrayCollection
     */
    public function getSortedGeologicalLayers()
    {
        if (null != $this->geologicalLayers){

            $criteria = Criteria::create()->orderBy(array("order" => Criteria::ASC));
            $layers = $this->geologicalLayers->matching($criteria);
            return $layers;
        }

        return $this->geologicalLayers;
    }

    /**
     * @param GeologicalLayer $geologicalLayer
     * @return $this
     */
    public function addGeologicalLayer(GeologicalLayer $geologicalLayer)
    {
        if (!$this->geologicalLayers->contains($geologicalLayer)) {
            if (null === $geologicalLayer->getOrder()) {
                $geologicalLayer->setOrder($this->geologicalLayers->count());
            }
            $this->geologicalLayers[] = $geologicalLayer;
        }

        return $this;
    }

    /**
     * @param GeologicalLayer $geologicalLayer
     */
    public function removeGeologicalLayer(GeologicalLayer $geologicalLayer)
    {
        if ($this->geologicalLayers->contains($geologicalLayer))
        {
            $this->geologicalLayers->removeElement($geologicalLayer);
        }
    }

    /**
     * @return bool
     */
    public function hasGeologicalLayers(){
        if (null == $this->getGeologicalLayers() || $this->getGeologicalLayers()->count() == 0) {
            return false;
        }

        return true;
    }

    /**
     * @param $layerNumber
     * @return null
     */
    public function getLayerByNumber($layerNumber){
        if ($this->hasGeologicalLayers()){

            /** @var GeologicalLayer $geologicalLayer */
            foreach ($this->getGeologicalLayers() as $geologicalLayer){
                if ($geologicalLayer->getOrder() == $layerNumber){
                    return $geologicalLayer;
                }
            }
        }

        return null;
    }

    /**
     * @return ArrayCollection
     */
    public function getGeologicalPoints()
    {
        return $this->geologicalPoints;
    }

    /**
     * @param GeologicalPoint $geologicalPoint
     * @return $this
     */
    public function addGeologicalPoint(GeologicalPoint $geologicalPoint)
    {
        if (!$this->geologicalPoints->contains($geologicalPoint)) {
            $this->geologicalPoints[] = $geologicalPoint;
        }

        return $this;
    }

    /**
     * @param GeologicalPoint $geologicalPoint
     */
    public function removeGeologicalPoint(GeologicalPoint $geologicalPoint)
    {
        if ($this->geologicalPoints->contains($geologicalPoint)) {
            $this->geologicalPoints->removeElement($geologicalPoint);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getGeologicalUnits()
    {
        return $this->geologicalUnits;
    }

    /**
     * @param GeologicalUnit $geologicalUnit
     * @return $this
     */
    public function addGeologicalUnit(GeologicalUnit $geologicalUnit)
    {
        if (!$this->geologicalUnits->contains($geologicalUnit)) {
            $this->geologicalUnits[] = $geologicalUnit;
        }

        return $this;
    }

    /**
     * @param GeologicalUnit $geologicalUnit
     */
    public function removeGeologicalUnit(GeologicalUnit $geologicalUnit)
    {
        if ($this->geologicalUnits->contains($geologicalUnit)) {
            $this->geologicalUnits->removeElement($geologicalUnit);
        }
    }

    /**
     * on PrePersist event all
     * +layers
     * +units
     * +points
     * have to be written to the soilModelObjects-Array
     *
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        if ($this->geologicalPoints && $this->geologicalPoints->count() > 0 ) {
            /** @var GeologicalPoint $geologicalPoint */
            foreach ($this->geologicalPoints as $geologicalPoint) {
                $this->addModelObject($geologicalPoint);
                foreach ($geologicalPoint->getGeologicalUnits() as $geologicalUnit) {
                    $this->addGeologicalUnit($geologicalUnit);
                }
            }
        }

        if ($this->geologicalLayers && $this->geologicalLayers->count() > 0 ) {
            foreach ($this->geologicalLayers as $geologicalLayer) {
                $this->addModelObject($geologicalLayer);
            }
        }

        if ($this->geologicalUnits && $this->geologicalUnits->count() > 0 ) {
            foreach ($this->geologicalUnits as $geologicalUnit) {
                $this->addModelObject($geologicalUnit);
            }
        }

        if (!is_null($this->area)) {
            $this->addModelObject($this->area);
        }
    }

    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        foreach ($this->getModelObjects() as $soilModelObject)
        {
            if ($soilModelObject instanceof GeologicalLayer)
            {
                if (is_null($this->geologicalLayers)){
                    $this->geologicalLayers = new ArrayCollection();
                }

                $this->addGeologicalLayer($soilModelObject);
                $this->removeModelObject($soilModelObject);
            }

            if ($soilModelObject instanceof GeologicalPoint)
            {
                if (is_null($this->geologicalPoints)){
                    $this->geologicalPoints = new ArrayCollection();
                }
                $this->addGeologicalPoint($soilModelObject);
                $this->removeModelObject($soilModelObject);
            }

            if ($soilModelObject instanceof GeologicalUnit)
            {
                if (is_null($this->geologicalUnits)){
                    $this->geologicalUnits = new ArrayCollection();
                }
                $this->addGeologicalUnit($soilModelObject);
                $this->removeModelObject($soilModelObject);
            }

            if ($soilModelObject instanceof Area)
            {
                $this->setArea($soilModelObject);
                $this->removeModelObject($soilModelObject);
            }
        }
    }
}
