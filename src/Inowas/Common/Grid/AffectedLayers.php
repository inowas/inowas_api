<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;


class AffectedLayers
{
    /** @var int[] */
    private $layers = [];

    public static function createWithLayerNumber(LayerNumber $layerNumber): AffectedLayers
    {
        $self = new self();
        $self->layers = [$layerNumber->toInt()];
        return $self;
    }

    /**
     * @param array $layerNumbers
     * @return AffectedLayers
     * @throws \Exception
     */
    public static function createWithLayerNumbers(array $layerNumbers): AffectedLayers
    {

        if (\count($layerNumbers) === 0){
            // @todo specify
            throw new \RuntimeException();
        }

        $layers = [];
        foreach ($layerNumbers as $layerNumber) {
            if (! $layerNumber instanceof LayerNumber){
                // @todo specify
                throw new \RuntimeException();
            }

            $layers[] = $layerNumber->toInt();
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

    public function addLayerNumber(LayerNumber $layerNumber):AffectedLayers
    {
        $self = new self();
        $self->layers = $this->layers[$layerNumber->toInt()];
        return $self;
    }

    public function layers(): array
    {
        $layers = [];
        foreach ($this->layers as $layer){
            $layers[] = LayerNumber::fromInt($layer);
        }
        return $layers;
    }

    public function toArray(): array
    {
        return $this->layers;
    }
}
