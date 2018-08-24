<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\AMQP;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\OptimizationInput;
use Inowas\ModflowModel\Model\ModflowPackages;

class ModflowOptimizationRequest implements \JsonSerializable
{
    private $author = '';
    private $project = '';
    private $type = 'optimization_start';

    /** @var  OptimizationInput */
    private $optimizationInput;

    /** @var  ModflowId */
    private $modelId;

    /** @var  ModflowPackages */
    private $packages;

    public static function fromParams(
        ModflowId $modelId,
        ModflowPackages $packages,
        OptimizationInput $optimizationInput
    ): self
    {
        $self = new self();
        $self->modelId = $modelId;
        $self->packages = $packages;
        $self->optimizationInput = $optimizationInput;
        return $self;
    }

    public static function fromJson(string $json): self
    {
        $arr = \json_decode($json, true);
        $self = new self();
        $self->modelId = ModflowId::fromString($arr['model_id']);
        $self->packages = ModflowPackages::fromArray($arr['data']);
        $self->optimizationInput = OptimizationInput::fromArray($arr['optimization']);
        return $self;
    }

    public function jsonSerialize(): array
    {
        return [
            'author' => $this->author,
            'project' => $this->project,
            'type' => $this->type,
            'version' => $this->packages->version(),
            'model_id' => $this->modelId->toString(),
            'calculation_id' => $this->packages->hash(),
            'optimization_id' => $this->optimizationInput->optimizationId()->toString(),
            'optimization' => $this->optimizationInput->toArray(),
            'data' => $this->packages->toArray()
        ];
    }

    private function __construct()
    {}

    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function setProject(string $project): self
    {
        $this->project = $project;
        return $this;
    }
}
