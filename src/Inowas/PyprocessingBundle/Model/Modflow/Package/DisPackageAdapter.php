<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\StressPeriod;
use Doctrine\Common\Collections\ArrayCollection;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy1DArray;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy2DArray;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy3DArray;

class DisPackageAdapter
{

    /**
     * @var ModFlowModel
     */
    protected $model;

    public function __construct(ModFlowModel $modFlowModel){
        $this->model = $modFlowModel;
    }

    /**
     * @return int
     */
    public function getNlay(){
        if (! $this->model->hasSoilModel()){
            return 0;
        }

        return $this->model->getSoilModel()->getNumberOfGeologicalLayers();
    }

    /**
     * @return int
     */
    public function getNRow(){
        if (! $this->model->getGridSize() instanceof GridSize){
            return 0;
        }

        return $this->model->getGridSize()->getNumberOfRows();
    }

    /**
     * @return int
     */
    public function getNCol(){
        if (! $this->model->getGridSize() instanceof GridSize){
            return 0;
        }

        return $this->model->getGridSize()->getNumberOfColumns();
    }

    /**
     * @return int
     */
    public function getNper(){
        return count($this->model->getCalculationProperties()["stress_periods"]);
    }

    /**
     * @return Flopy1DArray|null
     */
    public function getDelr(){

        if (! $this->model->getBoundingBox() instanceof BoundingBox){
            return null;
        }

        $deltaX = $this->model->getBoundingBox()->getDXInMeters();

        if ($deltaX == null){
            return null;
        }

        $nCol = $this->getNCol();

        if ($nCol == 0){
            return null;
        }

        $nRow = $this->getNRow();

        if ($nRow == 0){
            return null;
        }

        $delR = $deltaX/$nCol;

        return Flopy1DArray::fromValue($delR, $nRow);
    }

    /**
     * @return Flopy1DArray|null
     */
    public function getDelc(){

        if (! $this->model->getBoundingBox() instanceof BoundingBox){
            return null;
        }

        $deltaY = $this->model->getBoundingBox()->getDYInMeters();

        if ($deltaY == null){
            return null;
        }

        $nCol = $this->getNCol();

        if ($nCol == 0){
            return null;
        }

        $nRow = $this->getNRow();

        if ($nRow == 0){
            return null;
        }

        $delC = $deltaY/$nRow;

        return Flopy1DArray::fromValue($delC, $nCol);
    }

    /**
     * @return Flopy1DArray
     */
    public function getLaycbd(){
        return Flopy1DArray::fromValue(0, $this->getNlay());
    }

    /**
     * @return Flopy2DArray|null
     */
    public function getTop(){

        if (! $this->model->hasSoilModel()){
            return null;
        }

        if ($this->model->getSoilModel()->getSortedGeologicalLayers() === null){
            return null;
        }

        if ($this->model->getSoilModel()->getSortedGeologicalLayers()->count() == 0){
            return null;
        }

        $topLayer = $this->model->getSoilModel()->getSortedGeologicalLayers()->first();

        return Flopy2DArray::fromValue($topLayer->getTopElevation(), $this->getNRow(), $this->getNCol());
    }

    /**
     * @return Flopy3DArray
     */
    public function getBotm(){

        if (! $this->model->hasSoilModel()){
            return null;
        }

        if ($this->model->getSoilModel()->getSortedGeologicalLayers() === null){
            return null;
        }

        if ($this->model->getSoilModel()->getSortedGeologicalLayers()->count() == 0){
            return null;
        }

        $layers = $this->model->getSoilModel()->getSortedGeologicalLayers();

        $bottomElevations = array();
        for ($i=0; $i<count($layers); $i++){
            $bottomElevations[] = $layers[$i]->getBottomElevation();
        }

        return Flopy3DArray::fromValue($bottomElevations, $this->getNlay(), $this->getNRow(), $this->getNCol());
    }

    public function getPerlen(){

        $stressPeriods = $this->model->getSortedStressPeriods();

        if ($stressPeriods == null){
            return null;
        }

        $perlen = array();

        for ($i = 0; $i<count($stressPeriods); $i++){

            /** @var StressPeriod $sp */
            $sp = $stressPeriods[$i];

            $perlen[] = $sp->getLengthInDays();
        }

        return Flopy1DArray::fromValue($perlen, $this->getNper());
    }

    public function getNstp(){

        $stressPeriods = $this->model->getSortedStressPeriods();

        if ($stressPeriods == null){
            return null;
        }

        $nstp = array();

        for ($i = 0; $i<count($stressPeriods); $i++){

            /** @var StressPeriod $sp */
            $sp = $stressPeriods[$i];

            $nstp[] = $sp->getNumberOfTimeSteps();
        }

        return Flopy1DArray::fromValue($nstp, $this->getNper());
    }

    public function getTsmult(){

        $stressPeriods = $this->model->getSortedStressPeriods();

        if ($stressPeriods == null){
            return null;
        }

        $tsmult = array();

        for ($i = 0; $i<count($stressPeriods); $i++){

            /** @var StressPeriod $sp */
            $sp = $stressPeriods[$i];

            $tsmult[] = $sp->getTimeStepMultiplier();
        }

        return Flopy1DArray::fromValue($tsmult, $this->getNper());
    }

    public function getSteady(){

        $stressPeriods = $this->model->getSortedStressPeriods();

        if ($stressPeriods == null){
            return null;
        }

        $steady = array();

        for ($i = 0; $i<count($stressPeriods); $i++){

            /** @var StressPeriod $sp */
            $sp = $stressPeriods[$i];

            $steady[] = $sp->isSteady();
        }

        return Flopy1DArray::fromValue($steady, $this->getNper());
    }

    public function getItmuni(){
        return 4;
    }

    public function getLenuni(){
        return 2;
    }

    public function getExtension(){
        return 'dis';
    }

    public function getUnitnumber(){
        return 11;
    }

    public function getXul(){
        if (! $this->model->getBoundingBox() instanceof BoundingBox){
            return 0.0;
        }

        if ($this->model->getBoundingBox()->getSrid() != 4326){
            return 0.0;
        }

        return $this->model->getBoundingBox()->getXMin();
    }

    public function getYul(){
        if (! $this->model->getBoundingBox() instanceof BoundingBox){
            return 0.0;
        }

        if ($this->model->getBoundingBox()->getSrid() != 4326){
            return 0.0;
        }

        return $this->model->getBoundingBox()->getYMax();
    }

    public function getRotation(){
        return 0.0;
    }

    public function getProj4Str(){
        return 'EPSG:4326';
    }

    public function getStartDateTime(){

        /** @var ArrayCollection $stressPeriods */
        $stressPeriods = $this->model->getSortedStressPeriods();

        if ($stressPeriods == null){
            return null;
        }

        /** @var StressPeriod $sp */
        $sp = $stressPeriods[0];

        return \DateTimeImmutable::createFromMutable($sp->getDateTimeBegin());
    }
}