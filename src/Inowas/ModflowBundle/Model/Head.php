<?php

namespace Inowas\ModflowBundle\Model;

use Inowas\ModflowBundle\Model\ValueObject\Flopy2DArray;
use Ramsey\Uuid\Uuid;

class Head
{
    /** @var Uuid  */
    private $id;

    /** @var Uuid  */
    private $modelId;

    /** @var integer */
    private $layerNumber;

    /** @var float  */
    private $minValue;

    /** @var float */
    private $maxValue;

    /** @var float */
    private $meanValue;

    /** @var integer */
    private $totalTime;

    /** @var array */
    private $data;

    public function __construct() {
        $this->id = Uuid::uuid4();
    }

    /**
     * @param Uuid $modelId
     * @return Head
     */
    public function setModelId(Uuid $modelId): Head
    {
        $this->modelId = $modelId;
        return $this;
    }

    /**
     * @return Uuid
     */
    public function getModelId(): Uuid
    {
        return $this->modelId;
    }

    /**
     * @return int
     */
    public function getLayerNumber(): int
    {
        return $this->layerNumber;
    }

    /**
     * @param int $layerNumber
     * @return Head
     */
    public function setLayerNumber(int $layerNumber): Head
    {
        $this->layerNumber = $layerNumber;
        return $this;
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setTotalTime(int $totalTime){
        $this->totalTime = $totalTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalTime(): int
    {
        return $this->totalTime;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param Flopy2DArray $data
     * @return $this
     */
    public function setData(Flopy2DArray $data)
    {
        $this->data = $data->toArray();

        $counter = 0;
        $sum = 0;
        foreach ($data as $nRow => $row){
            foreach ($row as $nCol => $value) {
                if (is_null($this->minValue)){
                    $this->minValue = $value;
                }

                if (! is_null($this->minValue) && ! is_null($value)){
                    if ($value < $this->minValue){
                        $this->minValue = $value;
                    }
                }

                if (is_null($this->maxValue)){
                    $this->maxValue = $value;
                }

                if (! is_null($this->maxValue) && ! is_null($value)){
                    if ($value > $this->maxValue ){
                        $this->maxValue = $value;
                    }
                }

                $counter++;
                $sum += $value;
            }
        }

        $this->meanValue = $sum/$counter;

        return $this;
    }

    /**
     * @return float
     */
    public function getMinValue(): float
    {
        return $this->minValue;
    }

    /**
     * @return float
     */
    public function getMaxValue(): float
    {
        return $this->maxValue;
    }

    /**
     * @return float
     */
    public function getMeanValue(): float
    {
        return $this->meanValue;
    }

    /**
     * @param float $meanValue
     * @return Head
     */
    public function setMeanValue(float $meanValue): Head
    {
        $this->meanValue = $meanValue;
        return $this;
    }
}
