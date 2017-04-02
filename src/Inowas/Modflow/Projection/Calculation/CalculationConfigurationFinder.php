<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Inowas\Common\Calculation\BudgetType;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Id\ModflowId;
use Inowas\Modflow\Projection\Table;
use Inowas\ModflowBundle\Model\Calculation;
use Inowas\Soilmodel\Interpolation\FlopyConfiguration;

class CalculationConfigurationFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
    }

    public function findCalculationConfiguration(ModflowId $calculationId): ?FlopyConfiguration
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT configuration from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_CONFIG),
            ['calculation_id' => $calculationId->toString()]
        );

        if ($result == false){
            return null;
        }

        return FlopyConfiguration::fromData((array)json_decode($result['configuration']));
    }
}
