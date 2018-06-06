<?php

namespace Tests\Inowas\Modflow\Model\Packages;

use Inowas\Common\Modflow\Extension;
use Inowas\Common\Modflow\Hnoflo;
use Inowas\Common\Modflow\Ibound;
use Inowas\Common\Modflow\IchFlg;
use Inowas\Common\Modflow\Ixsec;
use Inowas\Common\Modflow\Stoper;
use Inowas\Common\Modflow\Strt;
use Inowas\Common\Modflow\Unitnumber;
use Inowas\ModflowModel\Model\Packages\BasPackage;

class BasPackageTest extends \PHPUnit_Framework_TestCase
{
    public function test_create(): void
    {
        // DEFAULTS
        $iBound = Ibound::fromValue(1);
        $strt = Strt::fromValue(1.0);
        $ixsec = Ixsec::fromBool(false);
        $ichflg = IchFlg::fromBool(false);
        $stoper = Stoper::none();
        $hnoflo = Hnoflo::fromFloat(-999.99);
        $extension = Extension::fromString('bas');
        $unitnumber = Unitnumber::fromInteger(13);

        $basPackage = BasPackage::fromParams(
            $iBound,
            $strt,
            $ixsec,
            $ichflg,
            $stoper,
            $hnoflo,
            $extension,
            $unitnumber
        );

        $this->assertInstanceOf(BasPackage::class, $basPackage);
        $json = json_encode($basPackage);
        $this->assertJson($json);
    }

    public function test_create_from_defaults(): void
    {
        $basPackage = BasPackage::fromDefaults();
        $this->assertInstanceOf(BasPackage::class, $basPackage);
    }

    public function test_update_iBound(): void
    {
        $basPackage = BasPackage::fromDefaults();
        $basPackage = $basPackage->updateIBound(Ibound::fromValue(2));
        $obj = json_decode(json_encode($basPackage));
        $this->assertEquals(2, $obj->ibound);
    }
}
