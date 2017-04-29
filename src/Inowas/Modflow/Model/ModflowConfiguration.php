<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\FileSystem\Modelworkspace;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\IdInterface;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\ExecutableName;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Listunit;
use Inowas\Common\Modflow\PackageName;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\Exception\InvalidPackageNameException;
use Inowas\Modflow\Model\Exception\InvalidPackageParameterUpdateMethodException;
use Inowas\Common\Modflow\Modelname;
use Inowas\Modflow\Model\Packages\BasPackage;
use Inowas\Modflow\Model\Packages\ChdPackage;
use Inowas\Modflow\Model\Packages\DisPackage;
use Inowas\Modflow\Model\Packages\GhbPackage;
use Inowas\Modflow\Model\Packages\LpfPackage;
use Inowas\Modflow\Model\Packages\MfPackage;
use Inowas\Modflow\Model\Packages\NwtPackage;
use Inowas\Modflow\Model\Packages\OcPackage;
use Inowas\Modflow\Model\Packages\PackageInterface;
use Inowas\Modflow\Model\Packages\PcgPackage;
use Inowas\Modflow\Model\Packages\RchPackage;
use Inowas\Modflow\Model\Packages\RivPackage;
use Inowas\Modflow\Model\Packages\UpwPackage;
use Inowas\Modflow\Model\Packages\WelPackage;
use Inowas\Soilmodel\Interpolation\FlopyConfiguration;

class ModflowConfiguration implements \JsonSerializable
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
        'nwt' => NwtPackage::class,
        'oc' => OcPackage::class,
        'chd' => ChdPackage::class,
        'ghb' => GhbPackage::class,
        'rch' => RchPackage::class,
        'riv' => RivPackage::class,
        'upw' => UpwPackage::class,
        'wel' => WelPackage::class
    ];

    /** @var array */
    private $availableFlowPackages = [
        'lpf' => LpfPackage::class,
        'upw' => UpwPackage::class
    ];

    /** @var array */
    private $availableSolverPackages = [
        'pcg' => PcgPackage::class,
        'nwt' => NwtPackage::class
    ];

    /** @var array */
    private $selectedPackages = [];

    /** @var array */
    private $packages = [];

    public static function createFromDefaultsWithId(IdInterface $calculationId): ModflowConfiguration
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

    public static function fromJson(string $json): ModflowConfiguration
    {
        $obj = json_decode($json);
        $self = new self();
        $self->calculationId = ModflowId::fromString($obj->id);
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
        if (! $this->packageIsAvailable($packageName)){
            throw InvalidPackageNameException::withName($packageName, $this->availablePackages);
        }

        if (! $this->packageIsSelected($packageName)){
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

        if (! $this->packageIsSelected($packageName)){
            $this->addPackageByName($packageName);
        }

        return $this->getPackageByName($packageName);
    }

    public function flowPackageName(): string
    {
        return $this->selectedPackages[3];
    }

    public function solverPackageName(): string
    {
        return $this->selectedPackages[4];
    }

    public function changeFlowPackage(PackageName $packageName): void
    {
        $packageName = $packageName->toString();
        if (! $this->flowPackageIsAvailable($packageName)){
            throw InvalidPackageNameException::withName($packageName, $this->availableFlowPackages);
        }

        if ($this->flowPackageName() == $packageName) {
            return;
        }

        $this->removePackageByName($this->flowPackageName());
        $class = $this->availablePackages[$packageName];
        $this->addPackage($class::fromDefaults());
        $this->selectedPackages[3] = $packageName;

        if ($packageName == 'upw'){
            $this->updateExecutableName(ExecutableName::fromString('mfnwt'));
            $this->updateVersion(Version::fromString(Version::MFNWT));
            $this->changeSolverPackage(PackageName::fromString('nwt'));
            return;
        }

        if ($packageName == 'lpf'){
            $this->updateExecutableName(ExecutableName::fromString('mf2005'));
            $this->updateVersion(Version::fromString(Version::MF2005));
            $this->changeSolverPackage(PackageName::fromString('pcg'));
            return;
        }
    }

    public function changeSolverPackage(PackageName $packageName): void
    {
        $packageName = $packageName->toString();
        if (! $this->solverPackageIsAvailable($packageName)){
            throw InvalidPackageNameException::withName($packageName, $this->availableFlowPackages);
        }

        if ($this->solverPackageName() == $packageName) {
            return;
        }

        $this->removePackageByName($this->solverPackageName());
        $class = $this->availablePackages[$packageName];
        $this->addPackage($class::fromDefaults());
        $this->selectedPackages[4] = $packageName;
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

    private function removePackageByName(string $packageName) {
        $package = $this->getPackageByName($packageName);
        unset($this->packages[$package->type()]);
    }

    private function updatePackage(PackageInterface $package): void
    {
        $this->packages[$package->type()] = $package;
    }

    private function addPackageByName(string $packageName): void
    {
        if (! $this->packageIsAvailable($packageName)) {
            throw InvalidPackageNameException::withName($packageName, $this->availablePackages);
        }
        
        $class = $this->availablePackages[$packageName];
        $this->addPackage($class::fromDefaults());
        $this->selectedPackages[] = $packageName;
    }

    private function getPackageByName(string $packageName): PackageInterface
    {
        if (! $this->packageIsAvailable($packageName)){
            throw InvalidPackageNameException::withName($packageName, $this->availablePackages);
        }

        return $this->packages[$packageName];
    }

    private function packageIsSelected(string $packageName): bool
    {
        return array_key_exists($packageName, $this->packages);
    }

    private function packageIsAvailable(string $packageName): bool
    {
        return array_key_exists($packageName, $this->availablePackages);
    }

    private function flowPackageIsAvailable(string $packageName): bool
    {
        return array_key_exists($packageName, $this->availableFlowPackages);
    }

    private function solverPackageIsAvailable(string $packageName): bool
    {
        return array_key_exists($packageName, $this->availableSolverPackages);
    }
}
