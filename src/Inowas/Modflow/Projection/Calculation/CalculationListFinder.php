<?php

namespace Inowas\Modflow\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Projection\Table;
use Inowas\ModflowBundle\Service\CalculationResultsPersister;

class CalculationListFinder
{
    /** @var Connection $connection */
    protected $connection;

    /** @var  CalculationResultsPersister */
    protected $persister;

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
}
