<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

use Inowas\Common\Modflow\Extension;
use Inowas\Common\Modflow\Ipakcb;
use Inowas\Common\Modflow\OcStressPeriodData;
use Inowas\Common\Modflow\Options;
use Inowas\Common\Modflow\Unitnumber;
use Inowas\Common\Modflow\WelStressPeriodData;

class WelPackage implements PackageInterface
{

    /** @var string  */
    protected $type = 'oc';

    /** @var  Ipakcb */
    protected $ipakcb;

    /** @var  WelStressPeriodData */
    protected $stressPeriodData;

    /** @var  Options */
    protected $options;

    /** @var Extension */
    protected $extension;

    /** @var  Unitnumber */
    protected $unitnumber;


    public static function fromDefaults()
    {
        $ipakcb = Ipakcb::fromInteger(0);
        $stressPeriodData = WelStressPeriodData::create();
        $options = Options::fromValue(null);
        $extension = Extension::fromString('wel');
        $unitnumber = Unitnumber::fromInteger(11);

        return new self($ipakcb, $stressPeriodData, $options, $extension, $unitnumber);
    }

    public static function fromParams(
        Ipakcb $ipakcb,
        WelStressPeriodData $welStressPeriodData,
        Options $options,
        Extension $extension,
        Unitnumber $unitnumber
    ): WelPackage
    {
        return new self($ipakcb, $welStressPeriodData, $options, $extension, $unitnumber);
    }

    public static function fromArray(array $arr): WelPackage
    {
        $ipakcb = Ipakcb::fromInteger($arr['ipakcb']);
        $stressPeriodData = WelStressPeriodData::fromArray($arr['stress_period_data']);
        $options = OcStressPeriodData::fromArray($arr['stress_period_data']);
        $extension = Extension::fromArray($arr['extension']);
        $unitnumber = Unitnumber::fromArray($arr['unitnumber']);

        return new self($ipakcb, $stressPeriodData, $options, $extension, $unitnumber);
    }

    private function __construct(
        Ipakcb $ipakcb,
        WelStressPeriodData $stressPeriodData,
        Options $options,
        Extension $extension,
        Unitnumber $unitnumber
    )
    {
        $this->ipakcb = $ipakcb;
        $this->stressPeriodData = $stressPeriodData;
        $this->options = $options;
        $this->extension = $extension;
        $this->unitnumber = $unitnumber;
    }

    public function updateIpakcb(Ipakcb $ipakcb): WelPackage
    {
        $package = self::fromArray($this->toArray());
        $package->ipakcb = $ipakcb;
        return $package;
    }

    public function updateWelStressPeriodData(WelStressPeriodData $stressPeriodData): WelPackage
    {
        $package = self::fromArray($this->toArray());
        $package->stressPeriodData = $stressPeriodData;
        return $package;
    }

    public function updateOptions(Options $options): WelPackage
    {
        $package = self::fromArray($this->toArray());
        $package->options = $options;
        return $package;
    }

    public function updateExtension(Extension $extension): WelPackage
    {
        $package = self::fromArray($this->toArray());
        $package->extension = $extension;
        return $package;
    }

    public function updateUnitnumber(Unitnumber $unitnumber): WelPackage
    {
        $package = self::fromArray($this->toArray());
        $package->unitnumber = $unitnumber;
        return $package;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function toArray(): array
    {
        return array(
            "ipakcb" => $this->ipakcb->toInteger(),
            "stress_period_data" => $this->stressPeriodData->toArray(),
            "options" => $this->options->toValue(),
            "extension" => $this->extension->toValue(),
            "unitnumber" => $this->unitnumber->toValue()
        );
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }
}
