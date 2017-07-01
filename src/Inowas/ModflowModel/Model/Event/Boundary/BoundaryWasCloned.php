<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event\Boundary;

use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class BoundaryWasCloned extends AggregateChanged
{

    /** @var BoundaryId */
    private $boundaryId;

    /** @var BoundaryId */
    private $fromBoundary;

    /** @var ModflowId */
    private $modelId;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param BoundaryId $boundaryId
     * @param BoundaryId $fromBoundary
     * @param ModflowId $modelId
     * @return BoundaryWasCloned
     */
    public static function withParameters(
        BoundaryId $boundaryId,
        BoundaryId $fromBoundary,
        ModflowId $modelId
    ): BoundaryWasCloned
    {
        $event = self::occur(
            $boundaryId->toString(), [
                'model_id' => $modelId->toString(),
                'from' => $fromBoundary->toString()
            ]
        );

        $event->boundaryId = $boundaryId;
        $event->fromBoundary = $fromBoundary;
        $event->modelId = $modelId;

        return $event;
    }

    public function boundaryId(): BoundaryId
    {
        if (null === $this->boundaryId) {
            $this->boundaryId = BoundaryId::fromString($this->aggregateId());
        }

        return $this->boundaryId;
    }

    public function fromBoundary(): BoundaryId
    {
        if (null === $this->fromBoundary) {
            $this->fromBoundary = BoundaryId::fromString($this->payload['from']);
        }

        return $this->fromBoundary;
    }

    public function modelId(): ModflowId
    {
        if (null === $this->modelId) {
            $this->modelId = ModflowId::fromString($this->payload['model_id']);
        }

        return $this->modelId;
    }
}
