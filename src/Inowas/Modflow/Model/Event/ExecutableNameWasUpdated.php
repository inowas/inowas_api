<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\FileSystem\FileName;
use Inowas\Common\Id\ModflowId;
use Prooph\EventSourcing\AggregateChanged;

class ExecutableNameWasUpdated extends AggregateChanged
{
    /** @var ModflowId */
    private $calculationId;

    /** @var  FileName */
    protected $executableName;

    public static function to(
        ModflowId $calculationId,
        FileName $executableName
    ): ExecutableNameWasUpdated
    {
        $event = self::occur($calculationId->toString(),[
            'executable_name' => $executableName->toString()
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

    public function name(): FileName
    {
        if ($this->executableName === null) {
            $this->executableName = FileName::fromString($this->payload['executable_name']);
        }

        return $this->executableName;
    }
}
