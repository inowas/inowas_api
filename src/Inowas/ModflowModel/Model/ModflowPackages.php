<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\FileSystem\Modelworkspace;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\Distance;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Modflow\ExecutableName;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Listunit;
use Inowas\Common\Modflow\Mt3dms;
use Inowas\Common\Modflow\PackageName;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Modflow\Version;
use Inowas\ModflowModel\Model\Exception\InvalidPackageNameException;
use Inowas\ModflowModel\Model\Exception\InvalidPackageParameterUpdateMethodException;
use Inowas\Common\Modflow\Name;
use Inowas\ModflowModel\Model\Packages\BasPackage;
use Inowas\ModflowModel\Model\Packages\ChdPackage;
use Inowas\ModflowModel\Model\Packages\DisPackage;
use Inowas\ModflowModel\Model\Packages\GhbPackage;
use Inowas\ModflowModel\Model\Packages\HobPackage;
use Inowas\ModflowModel\Model\Packages\LmtPackage;
use Inowas\ModflowModel\Model\Packages\LpfPackage;
use Inowas\ModflowModel\Model\Packages\MfPackage;
use Inowas\ModflowModel\Model\Packages\NwtPackage;
use Inowas\ModflowModel\Model\Packages\OcPackage;
use Inowas\ModflowModel\Model\Packages\PackageInterface;
use Inowas\ModflowModel\Model\Packages\PcgPackage;
use Inowas\ModflowModel\Model\Packages\RchPackage;
use Inowas\ModflowModel\Model\Packages\RivPackage;
use Inowas\ModflowModel\Model\Packages\UpwPackage;
use Inowas\ModflowModel\Model\Packages\WelPackage;

class ModflowPackages implements \JsonSerializable
{
    /** @var array */
    private $availablePackages;

    /** @var array */
    private $generalPackages = [
        'available' => [
            'mf' => MfPackage::class,
            'bas' => BasPackage::class,
            'dis' => DisPackage::class,
            'oc' => OcPackage::class
        ],
        'selected' => ['mf', 'bas', 'dis', 'oc']
    ];

    /** @var array */
    private $boundaryPackages = [
        'available' => [
            'chd' => ChdPackage::class,
            'ghb' => GhbPackage::class,
            'rch' => RchPackage::class,
            'riv' => RivPackage::class,
            'wel' => WelPackage::class,
            'hob' => HobPackage::class
        ],
        'selected' => []
    ];

    /** @var array */
    private $flowPackages = [
        'available' => [
            'lpf' => LpfPackage::class,
            'upw' => UpwPackage::class
        ],
        'selected' => 'lpf'
    ];

    /** @var array */
    private $solverPackages = [
        'available' => [
            'pcg' => PcgPackage::class,
            'nwt' => NwtPackage::class
        ],
        'selected' => 'pcg'
    ];

    /** @var array */
    private $mt3dPackages = [
        'available' => [
            'lmt' => LmtPackage::class
        ],
        'selected' => null
    ];

    /** @var array */
    private $mt3dms;

    /** @var array */
    private $packages = [];

    private $flopyVersion = '3.2.9';

    public static function createFromDefaults(): ModflowPackages
    {
        $self = new self();
        foreach ($self->selectedPackages() as $packageName) {
            if (array_key_exists($packageName, $self->availablePackages)) {
                $class = $self->availablePackages[$packageName];
                $self->setPackage($class::fromDefaults());
            }
        }
        return $self;
    }

    public static function fromArray(array $arr): ModflowPackages
    {
        $self = new self();

        $mf = $arr;
        if (\array_key_exists('mf', $mf)) {
            $mf = $mf['mf'];
        }

        /** @var array $selectedPackages */
        $selectedPackages = $mf['packages'];
        $self->setSelectedPackages($selectedPackages);

        foreach ($self->selectedPackages() as $package) {
            if (array_key_exists($package, $self->availablePackages)) {
                $class = $self->availablePackages[$package];
                $self->setPackage($class::fromArray($mf[$package]));
            }
        }

        if (\array_key_exists('mt', $arr)) {
            $self->mt3dms = $arr['mt'];
        }

        return $self;
    }

    private function __construct()
    {
        $this->getAvailablePackages();
    }

    /**
     * @param Name $name
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     */
    public function updateModelName(Name $name): void
    {
        // The executableName is configured in the MfPackage
        /** @var MfPackage $mfPackage */
        $mfPackage = $this->getPackage('mf');
        $mfPackage = $mfPackage->updateModelname($name);
        $this->updatePackage($mfPackage);
    }

    /**
     * @param Version $version
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     */
    public function updateVersion(Version $version): void
    {
        // The executableName is configured in the MfPackage
        /** @var MfPackage $mfPackage */
        $mfPackage = $this->getPackage('mf');
        $mfPackage = $mfPackage->updateVersion($version);
        $this->updatePackage($mfPackage);
    }

    /**
     * @param ExecutableName $name
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     */
    public function updateExecutableName(ExecutableName $name): void
    {
        // The executableName is configured in the MfPackage
        /** @var MfPackage $mfPackage */
        $mfPackage = $this->getPackage('mf');
        $mfPackage = $mfPackage->updateExecutableName($name);
        $this->updatePackage($mfPackage);
    }

    /**
     * @param Listunit $listUnit
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     */
    public function updateListUnit(Listunit $listUnit): void
    {
        // The executableName is configured in the MfPackage
        /** @var MfPackage $mfPackage */
        $mfPackage = $this->getPackage('mf');
        $mfPackage = $mfPackage->updateListunit($listUnit);
        $this->updatePackage($mfPackage);
    }

    /**
     * @param Modelworkspace $workSpace
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     */
    public function updateModelWorkSpace(Modelworkspace $workSpace): void
    {
        // The executableName is configured in the MfPackage
        /** @var MfPackage $mfPackage */
        $mfPackage = $this->getPackage('mf');
        $mfPackage = $mfPackage->updateModelworkspace($workSpace);
        $this->updatePackage($mfPackage);
    }

    /**
     * @param TimeUnit $timeUnit
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     * @throws \Exception
     */
    public function updateTimeUnit(TimeUnit $timeUnit): void
    {
        // The timeunit is configured in the DisPackage
        /** @var DisPackage $disPackage */
        $disPackage = $this->getPackage('dis');
        $disPackage = $disPackage->updateTimeUnit($timeUnit);
        $this->updatePackage($disPackage);
    }

    /**
     * @param LengthUnit $lengthUnit
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     * @throws \Exception
     */
    public function updateLengthUnit(LengthUnit $lengthUnit): void
    {
        // The lengthunit is configured in the DisPackage
        /** @var DisPackage $disPackage */
        $disPackage = $this->getPackage('dis');
        $disPackage = $disPackage->updateLengthUnit($lengthUnit);
        $this->updatePackage($disPackage);
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param GridSize $gridSize
     * @param BoundingBox $boundingBox
     * @param Distance $dx
     * @param Distance $dy
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     * @throws \Exception
     */
    public function updateGridParameters(GridSize $gridSize, BoundingBox $boundingBox, Distance $dx, Distance $dy): void
    {
        // The gridparameters are configured in the DisPackage
        /** @var DisPackage $disPackage */
        $disPackage = $this->getPackage('dis');
        $disPackage = $disPackage->updateGridParameters($gridSize, $boundingBox, $dx, $dy);
        $this->updatePackage($disPackage);
    }

    /**
     * @param DateTime $start
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     * @throws \Exception
     */
    public function updateStartDateTime(DateTime $start): void
    {
        // The StartDate is configured in the DisPackage
        /** @var DisPackage $disPackage */
        $disPackage = $this->getPackage('dis');
        $disPackage = $disPackage->updateStartDateTime($start);
        $this->updatePackage($disPackage);
    }

    /**
     * @param string $packageName
     * @param string $parameterName
     * @param $value
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageParameterUpdateMethodException
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     */
    public function updatePackageParameter(string $packageName, string $parameterName, $value): void
    {
        if (!$this->packageIsAvailable($packageName)) {
            throw InvalidPackageNameException::withName($packageName, $this->availablePackages);
        }

        if (!$this->packageIsSelected($packageName)) {
            $this->addPackageByName($packageName);
        }

        $package = $this->getPackageByName($packageName);

        $expectedMethod = 'update' . ucfirst($parameterName);
        if (!method_exists($package, $expectedMethod)) {
            throw InvalidPackageParameterUpdateMethodException::withName($packageName, $expectedMethod);
        }

        $package = $package->$expectedMethod($value);
        $this->updatePackage($package);
    }

    /**
     * @param string $packageName
     * @param string $parameterName
     * @return bool
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageParameterUpdateMethodException
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     */
    public function canUpdatePackageParameter(string $packageName, string $parameterName): bool
    {
        if (!$this->packageIsAvailable($packageName)) {
            throw InvalidPackageNameException::withName($packageName, $this->availablePackages);
        }

        if (!$this->packageIsSelected($packageName)) {
            $this->addPackageByName($packageName);
        }

        $package = $this->getPackageByName($packageName);

        $expectedMethod = 'update' . ucfirst($parameterName);
        if (!method_exists($package, $expectedMethod)) {
            throw InvalidPackageParameterUpdateMethodException::withName($packageName, $expectedMethod);
        }

        return true;
    }

    /**
     * @param string $packageName
     * @return PackageInterface
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     */
    public function getPackage(string $packageName): PackageInterface
    {
        return $this->getPackageByName($packageName);
    }

    public function flowPackageName(): string
    {
        return $this->flowPackages['selected'];
    }

    public function solverPackageName(): string
    {
        return $this->solverPackages['selected'];
    }

    /**
     * @param PackageName $name
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     */
    public function changeFlowPackage(PackageName $name): void
    {
        $packageName = $name->toString();
        if (!$this->flowPackageIsAvailable($packageName)) {
            throw InvalidPackageNameException::withName($packageName, $this->flowPackages['available']);
        }

        if ($this->flowPackageName() === $packageName) {
            return;
        }

        if (!array_key_exists($packageName, $this->packages)) {
            $class = $this->availablePackages[$packageName];
            $this->setPackage($class::fromDefaults());
        }

        $this->flowPackages['selected'] = $packageName;

        if ($packageName === 'upw') {
            $this->updateExecutableName(ExecutableName::fromString('mfnwt'));
            $this->updateVersion(Version::fromString(Version::MFNWT));
            $this->changeSolverPackage(PackageName::fromString('nwt'));
            return;
        }

        if ($packageName === 'lpf') {
            $this->updateExecutableName(ExecutableName::fromString('mf2005'));
            $this->updateVersion(Version::fromString(Version::MF2005));
            $this->changeSolverPackage(PackageName::fromString('pcg'));
            return;
        }
    }

    /**
     * @param PackageName $name
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     */
    public function changeSolverPackage(PackageName $name): void
    {
        $packageName = $name->toString();
        if (!$this->solverPackageIsAvailable($packageName)) {
            throw InvalidPackageNameException::withName($packageName, $this->solverPackages['available']);
        }

        if ($this->solverPackageName() === $packageName) {
            return;
        }

        if (!array_key_exists($packageName, $this->packages)) {
            $class = $this->availablePackages[$packageName];
            $this->setPackage($class::fromDefaults());
        }

        $this->solverPackages['selected'] = $packageName;
    }

    public function setMt3dms(Mt3dms $mt3dms): void
    {
        /** @var LmtPackage $lmtPackage */
        $lmtPackage = LmtPackage::fromDefaults();
        $this->setPackage($lmtPackage);
        $this->mt3dms = $mt3dms->toArray();
    }

    public function unsetMt3dms(): void
    {
        $this->removePackage(PackageName::fromString(LmtPackage::type()));
        $this->mt3dms = null;
    }

    public function metaData(): array
    {
        $metadata = [];
        $metadata['general'] = $this->generalPackages;
        $metadata['boundary'] = $this->boundaryPackages;
        $metadata['flow'] = $this->flowPackages;
        $metadata['solver'] = $this->solverPackages;

        foreach ($metadata as $category => $packages) {

            if (!array_key_exists('available', $packages)) {
                continue;
            }

            /** @var array $availablePackages */
            $availablePackages = $packages['available'];

            /** @var PackageInterface $package */
            foreach ($availablePackages as $name => $package) {
                $metadata[$category]['available'][$name] = $package::description();
            }
        }

        return $metadata;
    }

    private function mfPackageData(): array
    {
        $mfData = [];
        $mfData['packages'] = $this->selectedPackages();
        $mfData['version'] = $this->flopyVersion;

        foreach ($this->selectedPackages() as $selectedPackage) {
            if (!array_key_exists($selectedPackage, $this->packages)) {
                $class = $this->availablePackages[$selectedPackage];
                $this->setPackage($class::fromDefaults());
            }

            /** @var PackageInterface $package */
            $package = $this->packages[$selectedPackage];
            if ($package->isValid()) {
                $mfData[$package::type()] = $package->toArray();
            }
        }

        return $mfData;
    }

    public function packageData(): array
    {
        if (\is_array($this->mt3dms)) {
            return [
                'mf' => $this->mfPackageData(),
                'mt' => $this->mt3dms
            ];
        }

        return [
            'mf' => $this->mfPackageData()
        ];
    }

    public function isSelected(PackageName $name): bool
    {
        return $this->packageIsSelected($name->toString());
    }

    public function unSelectBoundaryPackage(PackageName $name): void
    {
        $selectedBoundaryPackages = $this->boundaryPackages['selected'];

        if (($key = array_search($name->toString(), $selectedBoundaryPackages, false)) !== false) {
            unset($selectedBoundaryPackages[$key]);
            $selectedBoundaryPackages = array_values($selectedBoundaryPackages);
        }

        $this->boundaryPackages['selected'] = $selectedBoundaryPackages;
    }

    /**
     * @param PackageName $name
     * @param array $data
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     */
    public function mergePackageData(PackageName $name, array $data): void
    {
        $package = $this->getPackage($name->toString());
        $package->mergeEditables($data);
        $this->updatePackage($package);
    }

    private function getAvailablePackages(): array
    {
        if (null === $this->availablePackages) {
            $this->availablePackages = array_merge(
                $this->generalPackages['available'],
                $this->boundaryPackages['available'],
                $this->flowPackages['available'],
                $this->solverPackages['available'],
                $this->mt3dPackages['available']
            );
        }

        return $this->availablePackages;
    }

    public function selectedPackages(): array
    {
        $selectedPackages = array_merge(
            $this->generalPackages['selected'],
            $this->boundaryPackages['selected'],
            [$this->flowPackages['selected']],
            [$this->solverPackages['selected']]
        );

        if (\is_string($this->mt3dPackages['selected'])) {
            $selectedPackages[] = $this->mt3dPackages['selected'];
        }

        return $selectedPackages;
    }


    private function setSelectedPackages(array $selectedPackages): void
    {
        $this->generalPackages['selected'] = [];
        $this->boundaryPackages['selected'] = [];
        $this->flowPackages['selected'] = [];
        $this->solverPackages['selected'] = [];
        $this->mt3dPackages['selected'] = null;

        foreach ($selectedPackages as $selectedPackage) {
            $this->setSelectedPackage($selectedPackage);
        }
    }

    private function setSelectedPackage(string $packageName): void
    {
        if (
            array_key_exists($packageName, $this->generalPackages['available']) &&
            !\in_array($packageName, $this->generalPackages['selected'], true)
        ) {
            $this->generalPackages['selected'][] = $packageName;
        }

        if (
            array_key_exists($packageName, $this->boundaryPackages['available']) &&
            !\in_array($packageName, $this->boundaryPackages['selected'], true)
        ) {
            $this->boundaryPackages['selected'][] = $packageName;
        }

        if (array_key_exists($packageName, $this->flowPackages['available'])) {
            $this->flowPackages['selected'] = $packageName;
        }

        if (array_key_exists($packageName, $this->solverPackages['available'])) {
            $this->solverPackages['selected'] = $packageName;
        }

        if (array_key_exists($packageName, $this->mt3dPackages['available'])) {
            $this->mt3dPackages['selected'] = $packageName;
        }
    }

    private function addPackage(PackageInterface $package): void
    {
        $this->packages[$package::type()] = $package;
        $this->setSelectedPackage($package::type());
    }

    private function setPackage(PackageInterface $package): void
    {
        $this->packages[$package::type()] = $package;
        $this->setSelectedPackage($package::type());
    }

    private function removePackage(PackageName $packageName): void
    {
        if (\array_key_exists($packageName->toString(), $this->packages)) {
            unset($this->packages[$packageName->toString()]);
        }
    }

    private function updatePackage(PackageInterface $package): void
    {
        $this->packages[$package::type()] = $package;
        $this->setSelectedPackage($package::type());
    }

    /**
     * @param string $packageName
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     */
    private function addPackageByName(string $packageName): void
    {
        if (!$this->packageIsAvailable($packageName)) {
            throw InvalidPackageNameException::withName($packageName, $this->availablePackages);
        }

        $class = $this->availablePackages[$packageName];
        $this->addPackage($class::fromDefaults());
    }

    /**
     * @param string $packageName
     * @return PackageInterface
     * @throws \Inowas\ModflowModel\Model\Exception\InvalidPackageNameException
     */
    private function getPackageByName(string $packageName): PackageInterface
    {
        if (!$this->packageIsAvailable($packageName)) {
            throw InvalidPackageNameException::withName($packageName, $this->availablePackages);
        }

        if (!array_key_exists($packageName, $this->packages)) {
            $class = $this->availablePackages[$packageName];
            $this->addPackage($class::fromDefaults());
        }

        return $this->packages[$packageName];
    }

    private function packageIsSelected(string $packageName): bool
    {
        return \in_array($packageName, $this->selectedPackages(), false);
    }

    private function packageIsAvailable(string $packageName): bool
    {
        return array_key_exists($packageName, $this->availablePackages);
    }

    private function flowPackageIsAvailable(string $packageName): bool
    {
        return array_key_exists($packageName, $this->flowPackages['available']);
    }

    private function solverPackageIsAvailable(string $packageName): bool
    {
        return array_key_exists($packageName, $this->solverPackages['available']);
    }

    public function hash(): string
    {
        $packageData = $this->packageData();
        $this->recursiveKeySort($packageData);
        return md5(json_encode($packageData));
    }

    public function version(): string
    {
        return $this->flopyVersion;
    }

    public function toArray(): array
    {
        return $this->packageData();
    }

    public function jsonSerialize(): array
    {
        return $this->packageData();
    }

    /**
     * @param $by_ref_array
     */
    private function recursiveKeySort(&$by_ref_array): void
    {
        ksort($by_ref_array, SORT_NUMERIC);

        /** @var array $by_ref_array */
        foreach ($by_ref_array as $key => $value) {
            if (\is_array($value)) {
                $this->recursiveKeySort($by_ref_array[$key]);
            }
        }
    }
}
