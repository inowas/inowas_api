<?php
/**
 * botm : float or array of floats (nlay, nrow, ncol), optional
 * An array of the bottom elevation for each model cell
 * (the default is 0.)
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

use Inowas\Common\Exception\InvalidArrayDimensionException;

class Botm
{
    
    /** @var  array */
    protected $botm;

    public static function from3DArray(array $botm): Botm
    {
        if (self::is3dArray($botm)){
            throw InvalidArrayDimensionException::withExpectedDimensionAndValue(3, $botm);
        }

        $self = new self();
        $self->botm = $botm;
        $self->autoRepair();
        return $self;
    }

    public static function from2DArray(array $botm): Botm
    {

        if (self::is2dArray($botm)){
            throw InvalidArrayDimensionException::withExpectedDimensionAndValue(3, $botm);
        }

        $self = new self();
        $self->botm = $botm;
        return $self;
    }

    public function isValid(): bool
    {
        return false;
    }

    public static function fromValue($botm): Botm
    {
        $self = new self();
        $self->botm = $botm;
        return $self;
    }

    private function __construct(){}

    public function toValue()
    {
        return $this->botm;
    }

    public function is3dArray($value): bool
    {
        return (is_array($value) && is_array($value[0]) && is_array($value[0][0]) && ! is_array($value[0][0][0]));
    }

    public function is2dArray($value): bool
    {
        return (is_array($value) && is_array($value[0]) && ! is_array($value[0][0]));
    }

    private function autoRepair(): bool
    {
        if ($this->is3dArray($this->botm)){
            return $this->exchangeTopValuesHigherThenBotmValues();
        }

        return false;
    }

    private function exchangeTopValuesHigherThenBotmValues(): bool
    {
        for ($layerNumber = count($this->botm)-1; $layerNumber>=1; $layerNumber--) {
            foreach ($this->botm[$layerNumber] as $rowNumber => $col){
                foreach ($col as $colNumber => $value){
                    $topElevation = $this->botm[$layerNumber-1][$rowNumber][$colNumber];
                    $botmElevation = $this->botm[$layerNumber][$rowNumber][$colNumber];
                    if ($botmElevation > $topElevation){
                        $this->botm[$layerNumber-1][$rowNumber][$colNumber] = $botmElevation;
                        $this->botm[$layerNumber][$rowNumber][$colNumber] = $topElevation;
                    }
                }
            }
        }

        return true;
    }
}
