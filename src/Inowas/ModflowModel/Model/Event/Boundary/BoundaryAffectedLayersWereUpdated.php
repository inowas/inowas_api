<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event\Boundary;

use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class BoundaryAffectedLayersWereUpdated extends AggregateChanged
{

    /** @var UserId */
    private $userId;

    /** @var BoundaryId */
    private $boundaryId;

    /** @var AffectedLayers */
    private $affectedLayers;

    public static function of(BoundaryId $boundaryId, UserId $userId, AffectedLayers $affectedLayers): BoundaryAffectedLayersWereUpdated
    {
        $event = self::occur(
            $boundaryId->toString(), [
                'user_id' => $userId->toString(),
                'affected_layers' => $affectedLayers->toArray()
            ]
        );

        $event->boundaryId = $boundaryId;
        $event->affectedLayers = $affectedLayers;

        return $event;
    }

    public function boundaryId(): BoundaryId
    {
        if ($this->boundaryId === null){
            $this->boundaryId = BoundaryId::fromString($this->payload['boundary_id']);
        }

        return $this->boundaryId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function affectedLayers(): AffectedLayers
    {
        if ($this->affectedLayers === null){
            $this->affectedLayers = AffectedLayers::fromArray($this->payload['affected_layers']);
        }

        return $this->affectedLayers;
    }
}
