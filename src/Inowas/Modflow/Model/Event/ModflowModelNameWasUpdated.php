<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Modflow\Model\ModflowModelName;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelNameWasUpdated extends AggregateChanged
{
    /** @var ModflowId */
    private $calculationId;

    /** @var  ModflowModelName */
    protected $name;

    public static function to(
        ModflowId $calculationId,
        ModflowModelName $name
    ): ModflowModelNameWasUpdated
    {
        $event = self::occur($calculationId->toString(),[
            'name' => $name->toString()
        ]);

        return $event;
    }

    public function calculationId(): ModflowId
    {
        if ($this->calculationId === null){
            $this->calculationId = ModflowId::fromString($this->aggregateId());
        }

        return $this->calculationId;
    }

    public function name(): ModflowModelName
    {
        if ($this->name === null) {
            $this->name = ModflowModelName::fromString($this->payload['name']);
        }

        return $this->name;
    }
}
