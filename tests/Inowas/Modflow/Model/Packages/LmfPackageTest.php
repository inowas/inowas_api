<?php

namespace Tests\Inowas\Modflow\Model\Packages;

use Inowas\Common\FileSystem\FileName;
use Inowas\Common\Modflow\Extension;
use Inowas\Common\Modflow\FileFormat;
use Inowas\Common\Modflow\FileHeader;
use Inowas\Common\Modflow\Filenames;
use Inowas\Common\Modflow\PackageFlows;
use Inowas\Common\Modflow\Unitnumber;
use Inowas\ModflowModel\Model\Packages\LmtPackage;

class LmfPackageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function create_from_default(): void
    {
        $package = LmtPackage::fromDefaults();
        $this->assertInstanceOf(LmtPackage::class, $package);
        $json = json_encode($package);
        $this->assertJson($json);
    }

    /**
     * @test
     */
    public function from_array_to_array(): void
    {
        $package = LmtPackage::fromDefaults();
        $this->assertEquals($package->toArray(), LmtPackage::fromArray($package->toArray())->toArray());
    }

    /**
     * @test
     */
    public function it_updates_output_file_name(): void
    {
        $package = LmtPackage::fromDefaults();
        $package = $package->updateOutputFileName(FileName::fromString('test.test'));
        $this->assertEquals($package->outputFileName()->toString(), 'test.test');
    }

    /**
     * @test
     */
    public function it_updates_output_file_unit(): void
    {
        $package = LmtPackage::fromDefaults();
        $package = $package->updateOutputFileUnit(Unitnumber::fromInteger(50));
        $this->assertEquals($package->outputFileUnit()->toInteger(), 50);
    }

    /**
     * @test
     */
    public function it_updates_output_file_header(): void
    {
        $package = LmtPackage::fromDefaults();
        $package = $package->updateOutputFileHeader(FileHeader::fromString('Header'));
        $this->assertEquals($package->outputFileHeader()->toString(), 'Header');
    }

    /**
     * @test
     */
    public function it_updates_output_file_format(): void
    {
        $package = LmtPackage::fromDefaults();
        $package = $package->updateOutputFileFormat(FileFormat::fromString('FileFormat'));
        $this->assertEquals($package->outputFileFormat()->toString(), 'FileFormat');
    }

    /**
     * @test
     */
    public function it_updates_extension(): void
    {
        $package = LmtPackage::fromDefaults();
        $package = $package->updateExtension(Extension::fromString('ext'));
        $this->assertEquals($package->extension()->toString(), 'ext');
    }

    /**
     * @test
     */
    public function it_updates_package_flows(): void
    {
        $package = LmtPackage::fromDefaults();
        $package = $package->updatePackageFlows(PackageFlows::fromArray(['abc', 'def', 1]));
        $this->assertEquals($package->packageFlows()->toArray(), ['abc', 'def', 1]);
    }

    /**
     * @test
     */
    public function it_updates_unitnumber(): void
    {
        $package = LmtPackage::fromDefaults();
        $package = $package->updateUnitnumber(Unitnumber::fromInteger(56));
        $this->assertEquals($package->unitnumber()->toInteger(), 56);
    }

    /**
     * @test
     */
    public function it_updates_filenames(): void
    {
        $package = LmtPackage::fromDefaults();
        $package = $package->updateFilenames(Filenames::fromValues(null));
        $this->assertEquals($package->filenames()->toValues(), null);
    }

}
