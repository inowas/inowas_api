<?php

namespace Inowas\SoilmodelBundle\Service;

use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;

class InterpolationResult
{

    const METHOD_GAUSSIAN = "gaussian";
    const METHOD_MEAN = "mean";

    /** @var  BoundingBox */
    protected $boundingBox;

    /** @var  GridSize */
    protected $gridSize;

    /** @var array  */
    protected $methods = [];

    /** @var  array */
    protected $pointValues = [];

    /** @var  array */
    protected $result = [];

    public static function fromInterpolation(Interpolation $interpolation, string $result): InterpolationResult
    {
        $self = new self();
        $self->boundingBox = $interpolation->boundingBox();
        $self->gridSize = $interpolation->gridSize();
        $self->methods = $interpolation->methods();
        $self->pointValues = $interpolation->pointValues();
        $self->result = json_decode($result);
        return $self;
    }

    public function result(): array
    {
        return $this->result;
    }
}
