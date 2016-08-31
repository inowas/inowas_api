<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy1DArray;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy3DArray;

class LpfPackageAdapter
{
    /**
     * ModFlowModel $model
     */
    protected $model;

    /**
     * LpfPackageAdapter constructor.
     * @param ModFlowModel $model
     */
    public function __construct(ModFlowModel $model){
        $this->model = $model;
    }

    /**
     * @return Flopy1DArray
     */
    public function getLaytyp(): Flopy1DArray{
        return Flopy1DArray::fromValue(0);
    }

    /**
     * @return Flopy1DArray
     */
    public function getLayavg(): Flopy1DArray
    {
        return Flopy1DArray::fromValue(0);
    }

    /**
     * @return Flopy1DArray
     */
    public function getChani(): Flopy1DArray
    {
        return Flopy1DArray::fromValue(0.0);
    }

    /**
     * @return Flopy1DArray
     */
    public function getLayvka(): Flopy1DArray
    {
        return Flopy1DArray::fromValue(0.0);
    }

    /**
     * @return Flopy1DArray
     */
    public function getLaywet(): Flopy1DArray
    {
        return Flopy1DArray::fromValue(0.0);
    }

    /**
     * @return int
     */
    public function getIpakcb(): int
    {
        return 53;
    }

    /**
     * @return float
     */
    public function getHdry(): float
    {
        return -1E+30;
    }

    /**
     * @return int
     */
    public function getIwdflg(): int
    {
        return 0;
    }

    /**
     * @return float
     */
    public function getWetfct(): float
    {
        return 0.1;
    }

    /**
     * @return int
     */
    public function getIwetit(): int
    {
        return 1;
    }

    /**
     * @return int
     */
    public function getIhdwet(): int
    {
        return 0;
    }

    /**
     * @return Flopy3DArray
     */
    public function getHk(): Flopy3DArray
    {
        if (! $this->model->hasSoilModel()){
            return null;
        }

        if ($this->model->getSoilModel()->getSortedGeologicalLayers() === null){
            return null;
        }

        if ($this->model->getSoilModel()->getSortedGeologicalLayers()->count() == 0){
            return null;
        }

        $layers = $this->model->getSoilModel()->getSortedGeologicalLayers();

        $hk = array();
        for ($i=0; $i<count($layers); $i++){
            $hk[] = $layers[$i]->getKx();
        }

        return Flopy3DArray::fromValue($hk);
    }

    /**
     * @return Flopy3DArray
     */
    public function getHani(): Flopy3DArray
    {
        return Flopy3DArray::fromValue(1.0);
    }

    /**
     * @return Flopy3DArray
     */
    public function getVka(): Flopy3DArray
    {
        return Flopy3DArray::fromValue(1.0);
    }

    /**
     * @return Flopy3DArray
     */
    public function getSs(): Flopy3DArray
    {
        return Flopy3DArray::fromValue(1e-5);
    }

    /**
     * @return Flopy3DArray
     */
    public function getSy(): Flopy3DArray
    {
        return Flopy3DArray::fromValue(0.15);
    }

    /**
     * @return Flopy3DArray
     */
    public function getVkcb(): Flopy3DArray
    {
        return Flopy3DArray::fromValue(0.0);
    }

    /**
     * @return Flopy3DArray
     */
    public function getWetdry(): Flopy3DArray
    {
        return Flopy3DArray::fromValue(-0.01);
    }

    /**
     * @return boolean
     */
    public function isStoragecoefficient(): bool
    {
        return false;
    }

    /**
     * @return boolean
     */
    public function isConstantcv(): bool
    {
        return false;
    }

    /**
     * @return boolean
     */
    public function isThickstrt(): bool
    {
        return false;
    }

    /**
     * @return boolean
     */
    public function isNocvcorrection(): bool
    {
        return false;
    }

    /**
     * @return boolean
     */
    public function isNovfc(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return 'lpf';
    }

    /**
     * @return int
     */
    public function getUnitnumber(): int
    {
        return 15;
    }
}