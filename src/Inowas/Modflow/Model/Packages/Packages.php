<?php

namespace Inowas\Modflow\Model\Packages;

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

    public function addPackage(PackageInterface $package): void
    {
        $this->packages[$package->type()] = $package;
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
