<?php

namespace Inowas\Soilmodel\Projection\LayerDetails;

use Doctrine\DBAL\Connection;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Grid\Nlay;
use Inowas\Common\Modflow\Botm;
use Inowas\Common\Modflow\Top;
use Inowas\Common\Soilmodel\AbstractSoilproperty;
use Inowas\Soilmodel\Model\GeologicalLayerNumber;
use Inowas\Soilmodel\Model\SoilmodelId;
use Inowas\Soilmodel\Projection\Table;

class LayerValuesFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
    }


    public function getTop(SoilmodelId $soilmodelId): ?Top
    {

        $type = 'htop';
        $layernumber = 0;

        $result = $this->getValue($soilmodelId, $type, $layernumber);
        if (is_array($result) && array_key_exists('values', $result)){
            return Top::from2DArray(
                json_decode($result['values'])
            );
        }

        return null;
    }

    public function getBotm(SoilmodelId $soilmodelId): Botm
    {
        $type = 'hbot';
        $layers = $this->getSortedLayerNumbers($soilmodelId);

        /** @var LayerNumber $layer */

        $botmArr = [];
        foreach ($layers as $layer) {
            $result = $this->getValue($soilmodelId, $type, $layer->toInteger());
            if (is_array($result) && array_key_exists('values', $result)){
                $botmArr[] = json_decode($result['values']);
            }
        }

        return Botm::from3DArray($botmArr);
    }

    public function getNlay(SoilmodelId $soilmodelId): Nlay
    {
        $layers = $this->getSortedLayerNumbers($soilmodelId);
        return Nlay::fromInteger(count($layers));
    }

    public function getValues(SoilmodelId $soilmodelId, AbstractSoilproperty $prop): ?AbstractSoilproperty
    {

        $layers = $this->getSortedLayerNumbers($soilmodelId);
        if (is_null($layers)){
            return null;
        }

        /** @var GeologicalLayerNumber $layer */
        foreach ($layers as $layer) {
            $result = $this->getValue($soilmodelId, $prop->identifier(), $layer->toInteger());
            if ($result === false){
                return null;
            }

            if (is_array($result) && array_key_exists('values', $result)){
                $prop->addLayerValue(
                    json_decode($result['values']),
                    $layer
                );
            }
        }

        return $prop;
    }

    private function getValue(SoilmodelId $soilmodelId, string $type, $layernumber)
    {
        return $this->connection->fetchAssoc(
            sprintf('SELECT * from %s WHERE soilmodel_id = :soilmodel_id AND layer_number = :layer_number AND type = :type', Table::LAYER_INTERPOLATIONS),
            [
                'soilmodel_id' => $soilmodelId->toString(),
                'type' => $type,
                'layer_number' => $layernumber
            ]
        );
    }

    private function getSortedLayerNumbers(SoilmodelId $soilmodelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT DISTINCT layer_number from %s WHERE soilmodel_id = :soilmodel_id ORDER BY layer_number', Table::LAYER_INTERPOLATIONS),
            ['soilmodel_id' => $soilmodelId->toString()]
        );

        if (! is_array($rows)){
            return null;
        }

        $layers = [];
        foreach ($rows as $row){
            if (array_key_exists('layer_number', $row)){
                $layers[] = GeologicalLayerNumber::fromInteger($row['layer_number']);
            }
        }
        return $layers;
    }
}
