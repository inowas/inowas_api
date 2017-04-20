<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\FileSystem\Modelworkspace;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\IdInterface;
use Inowas\Common\Modflow\ExecutableName;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Listunit;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\Exception\InvalidPackageNameException;
use Inowas\Modflow\Model\Exception\InvalidPackageParameterUpdateMethodException;
use Inowas\Common\Modflow\Modelname;
use Inowas\Modflow\Model\Version;
use Inowas\Soilmodel\Interpolation\FlopyConfiguration;

class Packages implements \JsonSerializable
{
    /** @var string  */
    private $author = "";

    /** @var string  */
    private $project = "";

    /** @var IdInterface  */
    private $calculationId;

    /** @var string  */
    private $type = "flopy_calculation";

    /** @var string  */
    private $version = "3.2.6";

    /** @var array */
    private $availablePackages = [
        'mf' => MfPackage::class,
        'bas' => BasPackage::class,
        'dis' => DisPackage::class,
        'lpf' => LpfPackage::class,
        'pcg' => PcgPackage::class,
        'oc' => OcPackage::class,
        'chd' => ChdPackage::class,
        'ghb' => GhbPackage::class,
        'rch' => RchPackage::class,
        'riv' => RivPackage::class,
        'wel' => WelPackage::class
    ];

    /** @var array */
    private $selectedPackages = [];

    /** @var array */
    private $packages = [];

    public static function createFromDefaultsWithId(IdInterface $calculationId): Packages
    {
        $self = new self();
        $self->calculationId = $calculationId;
        $self->selectedPackages = ['mf', 'dis', 'bas', 'lpf', 'pcg', 'oc'];
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

    public function updateModelName(Modelname $name): void
    {
        // The executableName is configured in the MfPackage
        /** @var MfPackage $mfPackage */
        $mfPackage = $this->getPackage('mf');
        $mfPackage = $mfPackage->updateModelName($name);
        $this->updatePackage($mfPackage);
    }

    public function updateVersion(Version $version): void
    {
        // The executableName is configured in the MfPackage
        /** @var MfPackage $mfPackage */
        $mfPackage = $this->getPackage('mf');
        $mfPackage = $mfPackage->updateVersion($version);
        $this->updatePackage($mfPackage);
    }

    public function updateExecutableName(ExecutableName $name): void
    {
        // The executableName is configured in the MfPackage
        /** @var MfPackage $mfPackage */
        $mfPackage = $this->getPackage('mf');
        $mfPackage = $mfPackage->updateExecutableName($name);
        $this->updatePackage($mfPackage);
    }

    public function updateListUnit(Listunit $listUnit): void
    {
        // The executableName is configured in the MfPackage
        /** @var MfPackage $mfPackage */
        $mfPackage = $this->getPackage('mf');
        $mfPackage = $mfPackage->updateListUnit($listUnit);
        $this->updatePackage($mfPackage);
    }

    public function updateModelWorkSpace(Modelworkspace $workSpace): void
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

    public function updatePackageParameter(string $packageName, string $parameterName, $value): void
    {
        if (! $this->hasAvailablePackage($packageName)){
            throw InvalidPackageNameException::withName($packageName, $this->availablePackages);
        }

        if (! $this->hasPackage($packageName)){
            $this->addPackageByName($packageName);
        }

        $package = $this->getPackageByName($packageName);

        $expectedMethod = 'update'.ucfirst($parameterName);
        if (! method_exists($package, $expectedMethod)){
            throw InvalidPackageParameterUpdateMethodException::withName($packageName, $expectedMethod);
        }

        $package = $package->$expectedMethod($value);
        $this->updatePackage($package);
    }

    public function getPackage(string $packageName): PackageInterface
    {
        if (! array_key_exists($packageName, $this->availablePackages)){
            throw InvalidPackageNameException::withName($packageName, $this->availablePackages);
        }

        if (! $this->hasPackage($packageName)){
            $this->addPackageByName($packageName);
        }

        return $this->getPackageByName($packageName);
    }

    public function author(): string
    {
        return $this->author;
    }

    public function project(): string
    {
        return $this->project;
    }

    public function jsonSerialize(): array
    {
        $packageData = [];
        $packageData['packages'] = $this->selectedPackages;
        $packageData['write_input'] = true;
        $packageData['run_model'] = true;

        foreach ($this->selectedPackages as $selectedPackage) {
            /** @var PackageInterface $package */
            $package = $this->packages[$selectedPackage];
            $packageData[$package->type()] = $package;
        }

        $data = array(
            "author" => $this->author,
            "project" => $this->project,
            "id" => $this->calculationId->toString(),
            "type" => $this->type,
            "version" => $this->version,
            "data" => $packageData
        );

        return FlopyConfiguration::fromData($data)->jsonSerialize();
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
        if (! $this->hasAvailablePackage($packageName)) {
            throw InvalidPackageNameException::withName($packageName, $this->availablePackages);
        }
        
        $class = $this->availablePackages[$packageName];
        $this->addPackage($class::fromDefaults());
        $this->selectedPackages[] = $packageName;
    }

    private function getPackageByName(string $packageName): PackageInterface
    {
        if (! $this->hasAvailablePackage($packageName)){
            throw InvalidPackageNameException::withName($packageName, $this->availablePackages);
        }

        return $this->packages[$packageName];
    }

    private function hasPackage(string $packageName): bool
    {
        return array_key_exists($packageName, $this->packages);
    }

    private function hasAvailablePackage(string $packageName): bool
    {
        return array_key_exists($packageName, $this->availablePackages);
    }
}
