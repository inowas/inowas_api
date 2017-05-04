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

    public function findCalculationById(ModflowId $calculationId)
    {
        return $this->connection->fetchAssoc(
            sprintf('SELECT * from %s WHERE calculation_id = :calculation_id ORDER BY id DESC LIMIT 1', Table::CALCULATION_LIST),
            ['calculation_id' => $calculationId->toString()]
        );
    }
}
