<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\OptimizationInput;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */

class OptimizationInputWasUpdated extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowId;

    /** @var  UserId */
    private $userId;

    /** @var OptimizationInput */
    private $input;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $modflowId
     * @param OptimizationInput $input
     * @return self
     */
    public static function byUserToModel(UserId $userId, ModflowId $modflowId, OptimizationInput $input): self
    {
        /** @var self $event */
        $event = self::occur(
            $modflowId->toString(), [
                'user_id' => $userId->toString(),
                'input' => $input->toArray()
            ]
        );

        $event->modflowId = $modflowId;
        $event->userId = $userId;
        $event->input = $input;

        return $event;
    }

    public function modelId(): ModflowId
    {
        if ($this->modflowId === null) {
            $this->modflowId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modflowId;
    }

    public function input(): OptimizationInput
    {
        if ($this->input === null) {
            $this->input = OptimizationInput::fromArray($this->payload['input']);
        }

        return $this->input;
    }

    public function userId(): UserId
    {
        if ($this->userId === null) {
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
