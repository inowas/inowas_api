<?php

namespace Inowas\Modflow\Projection\ModelScenarioList;

use Doctrine\DBAL\Connection;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\UserId;
use Inowas\Modflow\Projection\Table;

class ModelDetailsFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function findByBaseModelId(ModflowId $modelId)
    {
        return $this->connection->fetchAssoc(
            sprintf('SELECT * FROM %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString()]
        );
    }
}
