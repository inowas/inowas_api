<?php

namespace Inowas\Soilmodel\Interpolation;

use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\SoilmodelBundle\Model\PointValue;

class InterpolationConfiguration
{

    const METHOD_GAUSSIAN = "gaussian";
    const METHOD_MEAN = "mean";

    /** @var  array */
    protected $pointValues = [];

    /** @var array  */
    protected $methods = [];

    /** @var  BoundingBox */
    protected $boundingBox;

    /** @var  GridSize */
    protected $gridSize;

    public function setBoundingBox(BoundingBox $bb): InterpolationConfiguration
    {
        $this->boundingBox = $bb;
        return $this;
    }

    public function boundingBox(): BoundingBox
    {
        return $this->boundingBox;
    }

    public function setGridSize(GridSize $gz): InterpolationConfiguration
    {
        $this->gridSize = $gz;
        return $this;
    }

    public function gridSize(): GridSize
    {
        return $this->gridSize;
    }

    public function addMethod(string $method): InterpolationConfiguration
    {
        $this->methods[] = $method;
        return $this;
    }

    public function methods(): array
    {
        return $this->methods;
    }

    public function addPointValue(PointValue $pv): InterpolationConfiguration
    {
        $this->pointValues[] = $pv;
        return $this;
    }

    public function pointValues(): array
    {
        return $this->pointValues;
    }

    public function toJson(): string
    {
        $arr = [];
        $arr['author'] = '';
        $arr['project'] = '';
        $arr['type'] = 'interpolation';
        $arr['version'] = '1.0';
        $arr['data'] = array(
            'methods' => $this->methods,
            'bounding_box' => array(
                'x_min' => $this->boundingBox->xMin(),
                'x_max' => $this->boundingBox->xMax(),
                'y_min' => $this->boundingBox->yMin(),
                'y_max' => $this->boundingBox->yMax()
            ),
            'grid_size' => array(
                'n_x' => $this->gridSize->nX(),
                'n_y' => $this->gridSize->ny()
            ),
            'point_values' => $this->pointValues
        );


        return json_encode($arr);
    }
}
