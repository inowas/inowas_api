<?php

namespace Inowas\ModflowBundle\Model\Adapter;

use Doctrine\Common\Collections\ArrayCollection;
use Inowas\ModflowBundle\Model\BoundingBox;
use Inowas\ModflowBundle\Model\GridSize;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\StressPeriod;
use Inowas\ModflowBundle\Model\ValueObject\Flopy1DArray;
use Inowas\ModflowBundle\Model\ValueObject\Flopy2DArray;
use Inowas\ModflowBundle\Model\ValueObject\Flopy3DArray;
use Inowas\SoilmodelBundle\Model\Layer;
use Inowas\SoilmodelBundle\Model\Property;
use Inowas\SoilmodelBundle\Model\PropertyType;
use Inowas\SoilmodelBundle\Model\Soilmodel;
use Symfony\Component\Validator\Constraints as Assert;

class DisPackageAdapter
{
    /** @var ModflowModel */
    protected $model;

    /** @var  Soilmodel  */
    protected $soilmodel;

    /**
     * DisPackageAdapter constructor.
     * @param ModflowModel $modFlowModel
     * @param Soilmodel $soilmodel
     */
    public function __construct(ModflowModel $modFlowModel, Soilmodel $soilmodel){
        $this->model = $modFlowModel;
        $this->soilmodel = $soilmodel;
    }

    /**
     * @return int
     * @Assert\GreaterThan(0)
     */
    public function getNlay(): int
    {
        return $this->soilmodel->getLayers()->count();
    }

    /**
     * @return int
     * @Assert\GreaterThan(0)
     */
    public function getNRow():int
    {
        if (! $this->model->getGridSize() instanceof GridSize){
            return 0;
        }

        return $this->model->getGridSize()->getNumberOfRows();
    }

    /**
     * @return int
     * @Assert\GreaterThan(0)
     */
    public function getNCol(): int
    {
        if (! $this->model->getGridSize() instanceof GridSize){
            return 0;
        }

        return $this->model->getGridSize()->getNumberOfColumns();
    }

    /**
     * @return int
     * @Assert\GreaterThan(0)
     */
    public function getNper(): int
    {
        return count($this->model->getGlobalStressPeriods());
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

        if ($this->soilmodel->getLayers()->count() === 0) {
            return null;
        }

        /** @var Layer $topLayer */
        $topLayer = $this->soilmodel->getLayers()->first();

        /** @var Property $property */
        $property = $topLayer->findPropertyByType(PropertyType::fromString(PropertyType::TOP_ELEVATION));

        if (! $property instanceof Property){
            return null;
        }

        return Flopy2DArray::fromValue(
            $property->getValue()->getValue(),
            $this->getNRow(),
            $this->getNCol()
        );
    }

    /**
     * @return Flopy3DArray|null
     * @Assert\NotNull()
     */
    public function getBotm(){

        if ($this->soilmodel->getLayers()->count() === 0) {
            return null;
        }

        $layers = $this->soilmodel->getLayers();

        $bottomElevations = array();
        $nLay = count($layers);

        for ($i=0; $i<$nLay; $i++){

            /** @var Layer $layer */
            $layer = $layers[$i];

            /** @var Property $property */
            $property = $layer->findPropertyByType(PropertyType::fromString(PropertyType::BOTTOM_ELEVATION));

            if (! $property instanceof Property){
                return null;
            }

            $bottomElevations[] = $property->getValue()->getValue();
        }

        return Flopy3DArray::fromValue($bottomElevations, $this->getNlay(), $this->getNRow(), $this->getNCol());
    }

    /**
     * @return Flopy1DArray|null
     * @Assert\NotNull()
     */
    public function getPerlen(){

        $stressPeriods = $this->model->getGlobalStressPeriods();

        if ($stressPeriods === null){
            return null;
        }

        $perlen = array();
        for ($i = 0; $i<count($stressPeriods); $i++){
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

        $stressPeriods = $this->model->getGlobalStressPeriods();

        if ($stressPeriods === null){
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

    /**
     * @return Flopy1DArray|null
     * @Assert\NotNull()
     */
    public function getTsmult(){

        $stressPeriods = $this->model->getGlobalStressPeriods();

        if ($stressPeriods === null){
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

    /**
     * @return Flopy1DArray|null
     * @Assert\NotNull()
     */
    public function getSteady(){

        $stressPeriods = $this->model->getGlobalStressPeriods();
        if ($stressPeriods === null){
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
        $stressPeriods = $this->model->getGlobalStressPeriods();

        if ($stressPeriods === null){
            return null;
        }

        /** @var StressPeriod $sp */
        $sp = $stressPeriods[0];

        return \DateTimeImmutable::createFromMutable($sp->getDateTimeBegin());
    }
}
