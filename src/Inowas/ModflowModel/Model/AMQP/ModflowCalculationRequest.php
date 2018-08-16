<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\AMQP;

use Inowas\Common\Id\CalculationId;
use Inowas\Common\Id\ModflowId;
use Inowas\ModflowModel\Model\ModflowPackages;

class ModflowCalculationRequest implements \JsonSerializable
{
    private $author = '';
    private $project = '';

    /** @var CalculationId */
    private $calculationId;

    /** @var ModflowId */
    private $modelId;

    private $type = 'flopy_calculation';

    /** @var  ModflowPackages */
    private $packages;

    public static function fromParams(
        ModflowId $modelId,
        CalculationId $calculationId,
        ModflowPackages $packages
    ): ModflowCalculationRequest
    {
        $self = new self();
        $self->calculationId = $calculationId->toString();
        $self->modelId = $modelId->toString();
        $self->packages = $packages;
        return $self;
    }

    public function jsonSerialize(): array
    {
        return array(
            'author' => $this->author,
            'project' => $this->project,
            'calculation_id' => $this->calculationId,
            'model_id' => $this->modelId,
            'type' => $this->type,
            'version' => $this->packages->version(),
            'data' => $this->packages->toArray()
        );
    }
}
