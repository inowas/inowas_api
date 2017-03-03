<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Inowas\Common\Calculation\HeadData;
use Inowas\Common\Grid\ColumnNumber;
use Inowas\Common\FileSystem\FileName;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Grid\RowNumber;
use Inowas\Common\Calculation\ResultType;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Modflow\Projection\Table;
use Inowas\ModflowBundle\Service\FilePersister;

class CalculationResultsFinder
{
    /** @var Connection $connection */
    protected $connection;

    /** @var  FilePersister */
    protected $persister;

    public function __construct(Connection $connection, FilePersister $persister) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
        $this->persister = $persister;
    }

    public function findTimesByModelId(ModflowId $modelId, ResultType $type, LayerNumber $layerNumber)
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

    public function findTimes(ModflowId $calculationId, ResultType $type, LayerNumber $layerNumber): array
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
            $result[$row['layer']][] =  $row['type'];
        }

        return $result;
    }

    public function findValue(ModflowId $calculationId, ResultType $type, LayerNumber $layerNumber, TotalTime $totalTime): HeadData
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

    public function findTimeSeries(ModflowId $calculationId, ResultType $type, LayerNumber $layerNumber, ColumnNumber $nx, RowNumber $ny): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT filename, totim from %s WHERE calculation_id = :calculation_id AND type = :type AND layer = :layer', Table::CALCULATION_RESULTS),
            [
                'calculation_id' => $calculationId->toString(),
                'type' => $type->toString(),
                'layer' => $layerNumber->toInteger()
            ]
        );

        $result = [];
        foreach ($rows as $row){
            $data = $this->persister->read($calculationId, FileName::fromString($row['filename']));
            $result[$row['totim']] = $data->toArray()[$ny->toInteger()][$nx->toInteger()];
        }

        return $result;
    }
}
