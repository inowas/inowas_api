<?php

namespace Inowas\Modflow\Model\Packages;

use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\Exception\InvalidPackageNameException;

class Packages implements \JsonSerializable
{
    private $availablePackages = [
        'mf' => MfPackage::class,
        'bas' => BasPackage::class,
        'dis' => DisPackage::class,
        'lpf' => LpfPackage::class
    ];

    private $selectedPackages = [];

    private $packages = [];

    public static function createFromDefaults()
    {
        $self = new self();
        $self->selectedPackages = ['mf', 'bas', 'dis', 'lpf'];
        foreach ($self->selectedPackages as $packageName){
            $class = $self->availablePackages[$packageName];
            $self->addPackage($class::fromDefaults());
        }

        return $self;
    }

    private function __construct(){}

    public function updateUnits(TimeUnit $timeUnit, LengthUnit $lengthUnit)
    {
        // The units are configured in the DisPackage
        /** @var DisPackage $disPackage */
        $disPackage = $this->getPackage('dis');

    }

    public function updateGridParameters(GridSize $gridSize, BoundingBox $boundingBox)
    {

    }

    private function addPackage(PackageInterface $package): void
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

        return array(
            "author" => "",
            "project" => "",
            "type" => "flopy",
            "version" => "3.2.6",
            "data" => $packageData
        );
    }
}
