<?php

namespace Inowas\Soilmodel\Projection\LayerDetails;

use Doctrine\DBAL\Connection;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Soilmodel\Model\GeologicalLayerNumber;
use Inowas\Soilmodel\Model\SoilmodelId;
use Inowas\Soilmodel\Projection\Table;

class LayerDetailsFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
    }

    public function getNumberOfLayers(SoilmodelId $soilmodelId): ?GeologicalLayerNumber
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) from %s WHERE soilmodel_id = :soilmodel_id', Table::LAYER_DETAILS),
            ['soilmodel_id' => $soilmodelId->toString()]
        );

        if (is_array($result) && array_key_exists('count', $result)){
            return GeologicalLayerNumber::fromInteger($result['count']);
        }

        return null;
    }
}
