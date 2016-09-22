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
use Symfony\Component\Validator\Constraints as Assert;

class DisPackageAdapter
{
    /**
     * @var ModFlowModel
     */
    protected $model;

    /**
     * DisPackageAdapter constructor.
     * @param ModFlowModel $modFlowModel
     */
    public function __construct(ModFlowModel $modFlowModel){
        $this->model = $modFlowModel;
    }

    /**
     * @return int
     * @Assert\GreaterThan(0)
     */
    public function getNlay(){
        if (! $this->model->hasSoilModel()){
            return 0;
        }

        return $this->model->getSoilModel()->getNumberOfGeologicalLayers();
    }

    /**
     * @return int
     * @Assert\GreaterThan(0)
     */
    public function getNRow(){
        if (! $this->model->getGridSize() instanceof GridSize){
            return 0;
        }

        return $this->model->getGridSize()->getNumberOfRows();
    }

    /**
     * @return int
     * @Assert\GreaterThan(0)
     */
    public function getNCol(){
        if (! $this->model->getGridSize() instanceof GridSize){
            return 0;
        }

        return $this->model->getGridSize()->getNumberOfColumns();
    }

    /**
     * @return int
     * @Assert\GreaterThan(0)
     */
    public function getNper(){
        return count($this->model->getStressPeriods());
    }

    /**
     * @return Flopy1DArray|null
     * @Assert\NotNull()
     */
    public function getDelr(){

        if (! $this->model->getBoundingBox() instanceof BoundingBox){
            return null;
        }

        $deltaX = $this->model->getBoundingBox()->getDXInMeters();

        if ($deltaX === null){
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
     * @Assert\NotNull()
     */
    public function getDelc(){

        if (! $this->model->getBoundingBox() instanceof BoundingBox){
            return null;
        }

        $deltaY = $this->model->getBoundingBox()->getDYInMeters();

        if ($deltaY === null){
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
     * @Assert\NotNull()
     */
    public function getLaycbd(){
        return Flopy1DArray::fromValue(0, $this->getNlay());
    }

    /**
     * @return Flopy2DArray|null
     * @Assert\NotNull()
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
     * @return Flopy3DArray|null
     * @Assert\NotNull()
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
        $ni = count($layers);
        for ($i=0; $i<$ni; $i++){
            $bottomElevations[] = $layers[$i]->getBottomElevation();
        }

        return Flopy3DArray::fromValue($bottomElevations, $this->getNlay(), $this->getNRow(), $this->getNCol());
    }

    /**
     * @return Flopy1DArray|null
     * @Assert\NotNull()
     */
    public function getPerlen(){

        $stressPeriods = $this->model->getStressPeriods();

        if ($stressPeriods === null){
            return null;
        }

        $perlen = array();

        $ni = count($stressPeriods);
        for ($i = 0; $i<$ni; $i++){

            /** @var StressPeriod $sp */
            $sp = $stressPeriods[$i];

            $perlen[] = $sp->getLengthInDays()+1;
        }

        return Flopy1DArray::fromValue($perlen, $this->getNper());
    }

    /**
     * @return Flopy1DArray|null
     * @Assert\NotNull()
     */
    public function getNstp(){

        $stressPeriods = $this->model->getStressPeriods();

        if ($stressPeriods === null){
            return null;
        }

        $nstp = array();

        $ni = count($stressPeriods);
        for ($i = 0; $i<$ni; $i++){

            /** @var StressPeriod $sp */
            $sp = $stressPeriods[$i];

            $nstp[] = $sp->getNumberOfTimeSteps();
        }

        return Flopy1DArray::fromValue($nstp, $this->getNper());
    }

    /**
     * @return Flopy1DArray|null
     * @Assert\NotNull()
     */
    public function getTsmult(){

        $stressPeriods = $this->model->getStressPeriods();

        if ($stressPeriods === null){
            return null;
        }

        $tsmult = array();

        $ni = count($stressPeriods);
        for ($i = 0; $i<$ni; $i++){

            /** @var StressPeriod $sp */
            $sp = $stressPeriods[$i];

            $tsmult[] = $sp->getTimeStepMultiplier();
        }

        return Flopy1DArray::fromValue($tsmult, $this->getNper());
    }

    /**
     * @return Flopy1DArray|null
     * @Assert\NotNull()
     */
    public function getSteady(){

        $stressPeriods = $this->model->getStressPeriods();

        if ($stressPeriods === null){
            return null;
        }

        $steady = array();

        $ni = count($stressPeriods);
        for ($i = 0; $i<$ni; $i++){

            /** @var StressPeriod $sp */
            $sp = $stressPeriods[$i];

            $steady[] = $sp->isSteady();
        }

        return Flopy1DArray::fromValue($steady, $this->getNper());
    }

    /**
     * @return int
     */
    public function getItmuni(){
        return 4;
    }

    /**
     * @return int
     */
    public function getLenuni(){
        return 2;
    }

    /**
     * @return string
     */
    public function getExtension(){
        return 'dis';
    }

    /**
     * @return int
     */
    public function getUnitnumber(){
        return 11;
    }

    /**
     * @return float|mixed
     */
    public function getXul(){
        if (! $this->model->getBoundingBox() instanceof BoundingBox){
            return 0.0;
        }

        if ($this->model->getBoundingBox()->getSrid() != 4326){
            return 0.0;
        }

        return $this->model->getBoundingBox()->getXMin();
    }

    /**
     * @return float|mixed
     */
    public function getYul(){
        if (! $this->model->getBoundingBox() instanceof BoundingBox){
            return 0.0;
        }

        if ($this->model->getBoundingBox()->getSrid() != 4326){
            return 0.0;
        }

        return $this->model->getBoundingBox()->getYMax();
    }

    /**
     * @return float
     */
    public function getRotation(){
        return 0.0;
    }

    /**
     * @return string
     */
    public function getProj4Str(){
        return 'EPSG:4326';
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getStartDateTime(){

        /** @var ArrayCollection $stressPeriods */
        $stressPeriods = $this->model->getStressPeriods();

        if ($stressPeriods === null){
            return null;
        }

        /** @var StressPeriod $sp */
        $sp = $stressPeriods[0];

        return \DateTimeImmutable::createFromMutable($sp->getDateTimeBegin());
    }
}
