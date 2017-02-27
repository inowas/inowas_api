<?php

namespace Inowas\Modflow\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Inowas\Common\FileName;
use Inowas\Modflow\Model\CalculationResultData;
use Inowas\Modflow\Model\CalculationResultType;
use Inowas\Modflow\Model\CalculationResultWithData;
use Inowas\Modflow\Model\LayerNumber;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\TotalTime;
use Inowas\Modflow\Model\UserId;
use Inowas\Modflow\Projection\Table;
use Inowas\ModflowBundle\Service\CalculationResultsPersister;

class CalculationResultsFinder
{
    /** @var Connection $connection */
    protected $connection;

    /** @var  CalculationResultsPersister */
    protected $persister;

    public function __construct(Connection $connection, CalculationResultsPersister $persister) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
        $this->persister = $persister;
    }

    public function findTimesByModelId(ModflowId $modelId, CalculationResultType $type, LayerNumber $layerNumber)
    {
        $calculationId = $this->connection->fetchColumn(
            sprintf('SELECT calculation_id from %s WHERE model_id = :model_id ORDER BY id DESC LIMIT 1', Table::CALCULATION_LIST),
            ['model_id' => $modelId->toString()]
        );

        if ($calculationId == false){
            return [];
        }

        return $this->findTimes(ModflowId::fromString($calculationId), $type, $layerNumber);
    }

    public function findTimes(ModflowId $calculationId, CalculationResultType $type, LayerNumber $layerNumber): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT DISTINCT totim from %s WHERE calculation_id = :calculation_id AND type = :type AND layer = :layer ORDER BY totim', Table::CALCULATION_RESULTS),
            [
                'calculation_id' => $calculationId->toString(),
                'type' => $type->toString(),
                'layer' => $layerNumber->toInteger()
            ]
        );

        $result = [];
        foreach ($rows as $row){
            $result[] = TotalTime::fromInt($row['totim']);
        }

        return $result;
    }

    public function findLayerValues(ModflowId $calculationId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT layer, type from %s WHERE calculation_id = :calculation_id GROUP BY type, layer ORDER BY type, layer', Table::CALCULATION_RESULTS),
            ['calculation_id' => $calculationId->toString()]
        );

        $result = [];
        foreach ($rows as $row){

            if (array_key_exists('type', $row) && array_key_exists('layer', $row)){
                $result[] = [$row['type'] => $row['layer']];
            }
        }

        return $result;
    }

    public function findValue(ModflowId $calculationId, CalculationResultType $type, LayerNumber $layerNumber, TotalTime $totalTime)
    {
        $filename = $this->connection->fetchColumn(

            sprintf('SELECT filename from %s WHERE calculation_id = :calculation_id AND type = :type AND layer = :layer AND totim = :totim', Table::CALCULATION_RESULTS),
                [
                    'calculation_id' => $calculationId->toString(),
                    'type' => $type->toString(),
                    'layer' => $layerNumber->toInteger(),
                    'totim' => $totalTime->toInteger()
                ]
        );

        if ($filename == false){
            throw new \Exception();
        }


        return $this->persister->read($calculationId, FileName::fromString($filename));
    }
}
