<?php

namespace Tests\Inowas\Modflow\Model\Packages;

use Inowas\Common\FileSystem\ExternalPath;
use Inowas\Common\FileSystem\FileExtension;
use Inowas\Common\FileSystem\FileName;
use Inowas\Common\FileSystem\ModelWorkSpace;
use Inowas\Common\Modflow\ListUnit;
use Inowas\Common\Modflow\Verbose;
use Inowas\Modflow\Model\ModflowModelName;
use Inowas\Modflow\Model\ModflowVersion;
use Inowas\Modflow\Model\Packages\MfPackage;

class MfPackageTest extends \PHPUnit_Framework_TestCase
{
    public function test_create(){
        $modflowModelName = ModflowModelName::fromString('ModelName');
        $fileExtension = FileExtension::fromString('nam');
        $version = ModflowVersion::fromString(ModflowVersion::MF2005);
        $executableName = FileName::fromString('mf2005');
        $listUnit = ListUnit::fromInt(2);
        $modelWorkSpace = ModelWorkSpace::fromString('.');
        $externalPath = ExternalPath::none();
        $verbose = Verbose::fromBool(false);

        $mfPackage = MfPackage::fromParams(
            $modflowModelName,
            $fileExtension,
            $version,
            $executableName,
            $listUnit,
            $modelWorkSpace,
            $externalPath,
            $verbose
        );

        $this->assertInstanceOf(MfPackage::class, $mfPackage);
        $json = json_encode($mfPackage);
        $this->assertJson($json);
    }
}
