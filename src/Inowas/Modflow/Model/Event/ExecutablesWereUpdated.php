<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Id\ModflowId;
use Prooph\EventSourcing\AggregateChanged;

class ExecutablesWereUpdated extends AggregateChanged
{
    /** @var ModflowId */
    private $calculationId;

    /** @var  array */
    protected $executables;

    public static function to(
        ModflowId $calculationId,
        array $executables
    ): ExecutablesWereUpdated
    {
        $event = self::occur($calculationId->toString(),[
            'executables' => $executables
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

    public function executables(): array
    {
        if ($this->executables === null) {
            $this->executables = $this->payload['executables'];
        }

        return $this->executables;
    }
}
