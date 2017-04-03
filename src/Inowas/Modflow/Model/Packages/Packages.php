<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\FileSystem\FileName;
use Inowas\Common\FileSystem\ModelWorkSpace;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Modflow\Ibound;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\ListUnit;
use Inowas\Common\Modflow\Strt;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\Exception\InvalidPackageNameException;
use Inowas\Modflow\Model\Exception\InvalidPackageParameterUpdateMethodException;
use Inowas\Modflow\Model\ModflowModelName;
use Inowas\Modflow\Model\ModflowVersion;
use Inowas\Soilmodel\Interpolation\FlopyConfiguration;

class Packages implements \JsonSerializable
{
    /** @var string  */
    private $author = "";

    /** @var string  */
    private $project = "";

    /** @var string  */
    private $type = "flopy";

    /** @var string  */
    private $version = "3.2.6";

    /** @var array */
    private $availablePackages = [
        'mf' => MfPackage::class,
        'bas' => BasPackage::class,
        'dis' => DisPackage::class,
        'lpf' => LpfPackage::class
    ];

    /** @var array */
    private $selectedPackages = [];

    /** @var array */
    private $packages = [];

    public static function createFromDefaults(): Packages
    {
        $self = new self();
        $self->selectedPackages = ['mf', 'bas', 'dis', 'lpf'];
        foreach ($self->selectedPackages as $packageName){
            $class = $self->availablePackages[$packageName];
            $self->addPackage($class::fromDefaults());
        }

        return $self;
    }

    public static function fromJson(string $json): Packages
    {
        $obj = json_decode($json);
        $self = new self();
        $self->author = $obj->author;
        $self->project = $obj->project;
        $self->type = $obj->type;
        $self->version = $obj->version;
        $self->selectedPackages = $obj->data->packages;
        if (is_array($self->selectedPackages)) {
            foreach ($self->selectedPackages as $selectedPackage){
                if (array_key_exists($selectedPackage, $self->availablePackages)){
                    $class = $self->availablePackages[$selectedPackage];
                    $self->addPackage($class::fromArray((array)$obj->data->$selectedPackage));
                }
            }
        }

        return $self;
    }

    private function __construct(){}

    public function updateModelName(ModflowModelName $name): void
    {
        // The executableName is configured in the MfPackage
        /** @var MfPackage $mfPackage */
        $mfPackage = $this->getPackage('mf');
        $mfPackage = $mfPackage->updateModelName($name);
        $this->updatePackage($mfPackage);
    }

    public function updateVersion(ModflowVersion $version): void
    {
        // The executableName is configured in the MfPackage
        /** @var MfPackage $mfPackage */
        $mfPackage = $this->getPackage('mf');
        $mfPackage = $mfPackage->updateVersion($version);
        $this->updatePackage($mfPackage);
    }

    public function updateExecutableName(FileName $name): void
    {
        // The executableName is configured in the MfPackage
        /** @var MfPackage $mfPackage */
        $mfPackage = $this->getPackage('mf');
        $mfPackage = $mfPackage->updateExecutableName($name);
        $this->updatePackage($mfPackage);
    }

    public function updateListUnit(ListUnit $listUnit): void
    {
        // The executableName is configured in the MfPackage
        /** @var MfPackage $mfPackage */
        $mfPackage = $this->getPackage('mf');
        $mfPackage = $mfPackage->updateListUnit($listUnit);
        $this->updatePackage($mfPackage);
    }

    public function updateModelWorkSpace(ModelWorkSpace $workSpace): void
    {
        // The executableName is configured in the MfPackage
        /** @var MfPackage $mfPackage */
        $mfPackage = $this->getPackage('mf');
        $mfPackage = $mfPackage->updateModelWorkSpace($workSpace);
        $this->updatePackage($mfPackage);
    }

    public function updateTimeUnit(TimeUnit $timeUnit): void
    {
        // The timeunit is configured in the DisPackage
        /** @var DisPackage $disPackage */
        $disPackage = $this->getPackage('dis');
        $disPackage = $disPackage->updateTimeUnit($timeUnit);
        $this->updatePackage($disPackage);
    }

    public function updateLengthUnit(LengthUnit $lengthUnit)
    {
        // The lengthunit is configured in the DisPackage
        /** @var DisPackage $disPackage */
        $disPackage = $this->getPackage('dis');
        $disPackage = $disPackage->updateLengthUnit($lengthUnit);
        $this->updatePackage($disPackage);
    }

    public function updateGridParameters(GridSize $gridSize, BoundingBox $boundingBox)
    {
        // The gridparameters are configured in the DisPackage
        /** @var DisPackage $disPackage */
        $disPackage = $this->getPackage('dis');
        $disPackage = $disPackage->updateGridParameters($gridSize, $boundingBox);
        $this->updatePackage($disPackage);
    }

    public function updateStartDateTime(DateTime $start): void
    {
        // The StartDate is configured in the DisPackage
        /** @var DisPackage $disPackage */
        $disPackage = $this->getPackage('dis');
        $disPackage = $disPackage->updateStartDateTime($start);
        $this->updatePackage($disPackage);
    }

    public function updateIBound(Ibound $iBound): void
    {
        // The Ibound is configured in the BasPackage
        /** @var BasPackage $basPackage */
        $basPackage = $this->getPackage('bas');
        $basPackage = $basPackage->updateIBound($iBound);
        $this->updatePackage($basPackage);
    }

    public function updateStrt(Strt $strt): void
    {
        // The Strt is configured in the BasPackage
        /** @var BasPackage $basPackage */
        $basPackage = $this->getPackage('bas');
        $basPackage = $basPackage->updateStrt($strt);
        $this->updatePackage($basPackage);
    }

    public function updatePackageParameter(string $packageName, string $parameterName, $value): void
    {
        if (! $this->hasPackage($packageName)){
            throw InvalidPackageNameException::withName($packageName, $this->availablePackages);
        }

        $package = $this->getPackageByName($packageName);
        $expectedMethod = 'update'.ucfirst($parameterName);
        if (! method_exists($package, $expectedMethod)){
            throw InvalidPackageParameterUpdateMethodException::withName($packageName, $expectedMethod);
        }

        $package = $package->$expectedMethod($value);
        $this->updatePackage($package);
    }

    public function author(): string
    {
        return $this->author;
    }

    public function project(): string
    {
        return $this->project;
    }

    private function addPackage(PackageInterface $package): void
    {
        $this->packages[$package->type()] = $package;
    }

    private function updatePackage(PackageInterface $package): void
    {
        $this->packages[$package->type()] = $package;
    }

    private function addPackageByName(string $packageName): void
    {
        if (! array_key_exists($packageName, $this->availablePackages)){
            throw InvalidPackageNameException::withName($packageName, $this->availablePackages);
        }

        $class = $this->availablePackages[$packageName];
        $this->addPackage($class::fromDefaults());
    }

    private function getPackageByName(string $packageName): PackageInterface
    {
        if (! $this->hasPackage($packageName)){
            throw InvalidPackageNameException::withName($packageName, $this->availablePackages);
        }

        return $this->packages[$packageName];
    }

    private function getPackage(string $packageName): PackageInterface
    {
        if (! array_key_exists($packageName, $this->availablePackages)){
            throw InvalidPackageNameException::withName($packageName, $this->availablePackages);
        }

        if (! $this->hasPackage($packageName)){
            $this->addPackageByName($packageName);
        }

        return $this->getPackageByName($packageName);
    }

    private function hasPackage(string $packageName): bool
    {
        return array_key_exists($packageName, $this->packages);
    }

    /**
     * @return array
     */
    function jsonSerialize(): array
    {
        $packageData = [];
        $packageData['packages'] = $this->selectedPackages;

        foreach ($this->selectedPackages as $selectedPackage) {
            /** @var PackageInterface $package */
            $package = $this->packages[$selectedPackage];
            $packageData[$package->type()] = $package;
        }

        $data = array(
            "author" => $this->author,
            "project" => $this->project,
            "type" => $this->type,
            "version" => $this->version,
            "data" => $packageData
        );

        return FlopyConfiguration::fromData($data)->jsonSerialize();
    }
}
