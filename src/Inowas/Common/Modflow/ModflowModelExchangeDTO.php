<?php

namespace Inowas\Common\Modflow;

use Inowas\Common\Boundaries\BoundaryCollection;
use Inowas\Common\Soilmodel\LayerCollection;
use Inowas\ModflowModel\Model\ModflowPackages;

class ModflowModelExchangeDTO implements \JsonSerializable
{

    /** @var ModflowModel */
    private $modflowModel;

    /** @var LayerCollection */
    private $layers;

    /** @var BoundaryCollection */
    private $boundaries;

    /** @var ModflowPackages */
    private $packages;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowModel $modflowModel
     * @param LayerCollection $layers
     * @param BoundaryCollection $boundaries
     * @param ModflowPackages|null $packages
     * @return ModflowModelExchangeDTO
     */
    public static function fromParams(
        ModflowModel $modflowModel,
        LayerCollection $layers,
        BoundaryCollection $boundaries,
        ?ModflowPackages $packages
    ): ModflowModelExchangeDTO
    {
        $self = new self();
        $self->layers = $layers;
        $self->modflowModel = $modflowModel;
        $self->boundaries = $boundaries;
        $self->packages = $packages;
        return $self;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'meta' => [
                'name' => $this->modflowModel->name()->toString(),
                'description' => $this->modflowModel->description()->toString(),
                'timeUnit' => $this->modflowModel->timeUnit()->toInt(),
                'lengthUnit' => $this->modflowModel->lengthUnit()->toInt()
            ],
            'geometry' => [
                'type' => 'Polygon',
                'coordinates' => $this->modflowModel->geometry()->toArray()
            ],
            'gridSize' => $this->modflowModel->gridSize()->toArray(),
            'activeCells' => $this->modflowModel->activeCells()->cells2D(),
            'stressperiods' => $this->modflowModel->stressPeriods()->toArray(),
            'layers' => $this->layers->toArray(),
            'boundaries' => $this->boundaries->toArray(),
            'packages' => $this->packages ? $this->packages->toArray() : null
        ];
    }
}
