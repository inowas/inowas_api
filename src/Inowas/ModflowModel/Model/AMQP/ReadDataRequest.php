<?php

namespace Inowas\ModflowModel\Model\AMQP;

use Inowas\Common\Calculation\ResultType;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Grid\Ncol;
use Inowas\Common\Grid\Nrow;
use Inowas\Common\Id\CalculationId;
use Inowas\Common\Modflow\Extension;

class ReadDataRequest implements \JsonSerializable
{
    public const REQUEST_TYPE_LAYER_DATA = 'layerdata';
    public const REQUEST_TYPE_TIME_SERIES = 'timeseries';
    public const REQUEST_TYPE_FILE_LIST = 'filelist';
    public const REQUEST_TYPE_FILE = 'file';

    public const DATA_TYPE_HEAD = 'head';
    public const DATA_TYPE_DRAWDOWN = 'drawdown';
    public const DATA_TYPE_budget = 'budget';

    public const VERSION = '3.2.6';

    /** @var \stdClass */
    private $data;


    /**
     * @noinspection MoreThanThreeArgumentsInspection
     * @param CalculationId $calculationId
     * @param ResultType $dataType
     * @param TotalTime $totim
     * @param LayerNumber $layer
     * @return ReadDataRequest
     */
    public static function forLayerData(CalculationId $calculationId, ResultType $dataType, TotalTime $totim, LayerNumber $layer): ReadDataRequest
    {
        $arr = array();
        $arr['calculation_id'] = $calculationId->toString();
        $arr['type'] = 'flopy_read_data';
        $arr['version'] = self::VERSION;
        $arr['request'] = (object)array(
            self::REQUEST_TYPE_LAYER_DATA => (object)array(
                'type' => $dataType->toString(),
                'totim' => $totim->toInteger(),
                'layer' => $layer->toInt()
            )
        );

        $self = new self();
        $self->data = (object)$arr;
        return $self;
    }

    /**
     * @noinspection MoreThanThreeArgumentsInspection
     * @param CalculationId $calculationId
     * @param ResultType $dataType
     * @param LayerNumber $layer
     * @param Nrow $ny
     * @param Ncol $nx
     * @return ReadDataRequest
     */
    public static function forTimeSeries(CalculationId $calculationId, ResultType $dataType, LayerNumber $layer, Nrow $ny, Ncol $nx): ReadDataRequest
    {
        $arr = array();
        $arr['calculation_id'] = $calculationId->toString();
        $arr['type'] = 'flopy_read_data';
        $arr['version'] = self::VERSION;
        $arr['request'] = (object)array(
            self::REQUEST_TYPE_TIME_SERIES => (object)array(
                'type' => $dataType->toString(),
                'layer' => $layer->toInt(),
                'row' => $ny->toInt(),
                'column' => $nx->toInt()
            )
        );

        $self = new self();
        $self->data = (object)$arr;
        return $self;
    }

    public static function forFile(CalculationId $calculationId, Extension $extension): ReadDataRequest
    {
        $arr = array();
        $arr['calculation_id'] = $calculationId->toString();
        $arr['type'] = 'flopy_read_data';
        $arr['version'] = self::VERSION;
        $arr['request'] = (object)array(self::REQUEST_TYPE_FILE => $extension->toString());

        $self = new self();
        $self->data = (object)$arr;
        return $self;
    }

    public static function forFileList(CalculationId $calculationId): ReadDataRequest
    {
        $arr = array();
        $arr['calculation_id'] = $calculationId->toString();
        $arr['type'] = 'flopy_read_data';
        $arr['version'] = self::VERSION;
        $arr['request'] = (object)array(self::REQUEST_TYPE_FILE_LIST => true);

        $self = new self();
        $self->data = (object)$arr;
        return $self;
    }

    public function jsonSerialize(): \stdClass
    {
        return $this->data;
    }
}
