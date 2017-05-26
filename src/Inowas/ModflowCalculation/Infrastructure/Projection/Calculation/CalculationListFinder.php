<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Infrastructure\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Inowas\Common\Id\ModflowId;
use Inowas\ModflowCalculation\Infrastructure\Projection\Table;

class CalculationListFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
    }

    public function findLastCalculationByModelId(ModflowId $modelId)
    {
        return $this->connection->fetchAssoc(
            sprintf('SELECT * from %s WHERE model_id = :model_id ORDER BY id DESC LIMIT 1', Table::CALCULATION_LIST),
            ['model_id' => $modelId->toString()]
        );
    }

    public function findCalculationById(ModflowId $calculationId): ?array
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT calculation_id AS id, model_id, soilmodel_id, user_id, state, start_date_time, end_date_time from %s WHERE calculation_id = :calculation_id ORDER BY id DESC LIMIT 1', Table::CALCULATION_LIST),
            ['calculation_id' => $calculationId->toString()]
        );

        if ($result === false) {
            return null;
        }

        return $result;
    }
}
