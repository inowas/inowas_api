<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Modflow\Projection\Table;

class BoundaryFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function findByModelId(ModflowId $modelId)
    {
        return $this->connection->fetchAll(
            sprintf('SELECT boundary_id, name, geometry, metadata FROM %s WHERE model_id = :model_id', Table::BOUNDARIES),
            ['model_id' => $modelId->toString()]
        );
    }

    public function findBoundaryById(ModflowId $modelId, BoundaryId $boundaryId)
    {
        return $this->connection->fetchAssoc(
            sprintf('SELECT * FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARIES),
            [
                'model_id' => $modelId->toString(),
                'boundary_id' => $boundaryId->toString()
            ]
        );
    }
}
