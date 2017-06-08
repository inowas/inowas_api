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

    public function findCalculationsByModelId(ModflowId $modelId): ?array
    {
        $result = $this->connection->fetchAll(
            sprintf('SELECT calculation_id from %s WHERE model_id = :model_id', Table::CALCULATION_LIST),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false){
            return null;
        }

        return $result;
    }

    public function findLastCalculationByModelId(ModflowId $modelId): ?ModflowId
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT calculation_id from %s WHERE model_id = :model_id ORDER BY created_at DESC LIMIT 1', Table::CALCULATION_LIST),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false){
            return null;
        }

        return ModflowId::fromString($result['calculation_id']);
    }

    public function findCalculationById(ModflowId $calculationId): ?array
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT calculation_id AS id, model_id, soilmodel_id, user_id, calculation_state as state, start_date_time, end_date_time from %s WHERE calculation_id = :calculation_id ORDER BY id DESC LIMIT 1', Table::CALCULATION_LIST),
            ['calculation_id' => $calculationId->toString()]
        );

        if ($result === false) {
            return null;
        }

        return $result;
    }
}
