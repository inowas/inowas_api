<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;


class AffectedLayers
{
    /** @var int[] */
    private $layers = [];

    public static function createWithLayerNumber(LayerNumber $layerNumber)
    {
        $self = new self();
        $self->layers = [$layerNumber->toInteger()];
        return $self;
    }

    public static function createWithLayerNumbers(array $layerNumbers)
    {

        if (count($layerNumbers) === 0){
            // @todo specify
            throw new \Exception();
        }

        $layers = [];
        foreach ($layerNumbers as $layerNumber) {
            if (! $layerNumber instanceof LayerNumber){
                // @todo specify
                throw new \Exception();
            }

            $layers[] = $layerNumber->toInteger();
        }

        $self = new self();
        $self->layers = $layers;
        return $self;
    }

    public static function fromArray(array $layers): AffectedLayers
    {
        $self = new self();
        $self->layers = $layers;
        return $self;
    }

    private function __construct(){}

    public function addLayerNumber(LayerNumber $layerNumber)
    {
        $self = new self();
        $self->layers = $this->layers[$layerNumber->toInteger()];
        return $self;
    }

    public function layers(): array
    {
        $layers = [];
        foreach ($this->layers as $layer){
            $layers[] = LayerNumber::fromInteger($layer);
        }
        return $layers;
    }

    public function toArray(): array
    {
        return $this->layers;
    }
}
