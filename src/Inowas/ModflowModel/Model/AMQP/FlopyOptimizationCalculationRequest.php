<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\AMQP;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\OptimizationInput;
use Inowas\ModflowModel\Model\ModflowPackages;

class FlopyOptimizationCalculationRequest implements \JsonSerializable
{
    private $author = '';
    private $project = '';
    private $type = 'flopy_optimization_calculation';

    /** @var  OptimizationInput */
    private $optimizationInput;

    /** @var  ModflowId */
    private $modelId;

    /** @var  ModflowPackages */
    private $packages;

    public static function fromParams(ModflowId $modelId, ModflowPackages $packages, OptimizationInput $optimizationInput): self
    {
        $self = new self();
        $self->modelId = $modelId;
        $self->packages = $packages;
        $self->optimizationInput = $optimizationInput;
        return $self;
    }

    public function jsonSerialize(): array
    {
        return array(
            'author' => $this->author,
            'project' => $this->project,
            'model_id' => $this->modelId->toString(),
            'type' => $this->type,
            'version' => $this->packages->version(),
            'data' => $this->packages->toArray(),
            'optimization' => $this->optimizationInput->toArray()
        );
    }
}
