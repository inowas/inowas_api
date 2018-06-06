<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Packages;

use Inowas\Common\Modflow\HeadObservationCollection;
use Inowas\Common\Modflow\Hobdry;
use Inowas\Common\Modflow\Iuhobsv;
use Inowas\Common\Modflow\Tomulth;
use Inowas\Common\Modflow\Extension;
use Inowas\Common\Modflow\Unitnumber;

class HobPackage extends AbstractPackage
{
    /**
     *
     */
    public const TYPE = 'hob';
    /**
     *
     */
    public const DESCRIPTION = 'Head Observation Package';

    /** @var  Iuhobsv */
    protected $iuhobsv;

    /** @var  Hobdry */
    protected $hobdry;

    /** @var  Tomulth */
    protected $tomulth;

    /** @var HeadObservationCollection */
    protected $obsData;

    /** @var  Extension */
    protected $extension;

    /** @var Unitnumber */
    protected $unitnumber;

    /**
     * @return HobPackage
     */
    public static function fromDefaults(): HobPackage
    {
        // DEFAULT
        $self = new self();
        $self->iuhobsv = Iuhobsv::fromInt(1051);
        $self->hobdry = Hobdry::fromFloat(0);
        $self->tomulth = Tomulth::fromFloat(1.0);
        $self->obsData = HeadObservationCollection::create();
        $self->extension = Extension::fromString(self::TYPE);
        $self->unitnumber = Unitnumber::fromValue(null);
        return $self;
    }

    /**
     * @param Iuhobsv $iuhobsv
     * @param Hobdry $hobdry
     * @param HeadObservationCollection $obsData
     * @return HobPackage
     */
    public static function fromParams(
       Iuhobsv $iuhobsv, Hobdry $hobdry, HeadObservationCollection $obsData
    ): HobPackage
    {
        $self = new self();
        $self->iuhobsv = $iuhobsv;
        $self->hobdry = $hobdry;
        $self->obsData = $obsData;
        return $self;
    }

    /**
     * @param array $arr
     * @return HobPackage
     */
    public static function fromArray(array $arr): HobPackage
    {
        $self = new self();
        $self->iuhobsv = Iuhobsv::fromInt($arr['iuhobsv']);
        $self->hobdry = Hobdry::fromFloat($arr['hobdry']);
        $self->tomulth = Tomulth::fromFloat($arr['tomulth']);
        $self->obsData = HeadObservationCollection::fromArray($arr['obs_data']);
        $self->extension = Extension::fromValue($arr['extension']);
        $self->unitnumber = Unitnumber::fromValue($arr['unitnumber']);
        return $self;
    }

    /**
     * HobPackage constructor.
     */
    private function __construct()
    {}

    /**
     * @param Iuhobsv $iuhobsv
     * @return HobPackage
     */
    public function updateIuhobsv(Iuhobsv $iuhobsv): HobPackage
    {
        $package = self::fromArray($this->toArray());
        $package->iuhobsv = $iuhobsv;
        return $package;
    }

    /**
     * @param Hobdry $hobdry
     * @return HobPackage
     */
    public function updateHobdry(Hobdry $hobdry): HobPackage
    {
        $package = self::fromArray($this->toArray());
        $package->hobdry = $hobdry;
        return $package;
    }

    /**
     * @param Tomulth $tomulth
     * @return HobPackage
     */
    public function updateTomulth(Tomulth $tomulth): HobPackage
    {
        $package = self::fromArray($this->toArray());
        $package->tomulth = $tomulth;
        return $package;
    }

    /**
     * @param HeadObservationCollection $obsData
     * @return HobPackage
     */
    public function updateObsData(HeadObservationCollection $obsData): HobPackage
    {
        $package = self::fromArray($this->toArray());
        $package->obsData = $obsData;
        return $package;
    }

    /**
     * @param Extension $extension
     * @return HobPackage
     */
    public function updateExtension(Extension $extension): HobPackage
    {
        $package = self::fromArray($this->toArray());
        $package->extension = $extension;
        return $package;
    }

    /**
     * @param Unitnumber $unitnumber
     * @return HobPackage
     */
    public function updateUnitnumber(Unitnumber $unitnumber): HobPackage
    {
        $package = self::fromArray($this->toArray());
        $package->unitnumber = $unitnumber;
        return $package;
    }

    /**
     * @return Iuhobsv
     */
    public function getIuhobsv(): Iuhobsv
    {
        return $this->iuhobsv;
    }

    /**
     * @return Hobdry
     */
    public function getHobdry(): Hobdry
    {
        return $this->hobdry;
    }

    /**
     * @return Tomulth
     */
    public function getTomulth(): Tomulth
    {
        return $this->tomulth;
    }

    /**
     * @return HeadObservationCollection
     */
    public function getObsData(): HeadObservationCollection
    {
        return $this->obsData;
    }

    /**
     * @return Extension
     */
    public function getExtension(): Extension
    {
        return $this->extension;
    }

    /**
     * @return Unitnumber
     */
    public function getUnitnumber(): Unitnumber
    {
        return $this->unitnumber;
    }


    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array(
            'iuhobsv' => $this->iuhobsv->toInt(),
            'hobdry' => $this->hobdry->toFloat(),
            'tomulth' => $this->tomulth->toFloat(),
            'obs_data' => $this->obsData->toArray(),
            'extension' => $this->extension->toValue(),
            'unitnumber' => $this->unitnumber->toValue()
        );
    }

    /**
     * @return array
     */
    public function getEditables(): array
    {
        return $this->toArray();
    }

    /**
     * @param array $arr
     */
    public function mergeEditables(array $arr): void
    {}

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
