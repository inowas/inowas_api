<?php

namespace Tests\Inowas\Modflow\Model\Packages;

use Inowas\Common\Modflow\Extension;
use Inowas\Common\Modflow\HNoFlo;
use Inowas\Common\Modflow\IBound;
use Inowas\Common\Modflow\IchFlg;
use Inowas\Common\Modflow\Ixsec;
use Inowas\Common\Modflow\StoPer;
use Inowas\Common\Modflow\Strt;
use Inowas\Common\Modflow\UnitNumber;
use Inowas\Modflow\Model\Packages\BasPackage;

class BasPackageTest extends \PHPUnit_Framework_TestCase
{
    public function test_create(){

        // DEFAULTS
        $iBound = IBound::fromValue(1);
        $strt = Strt::fromValue(1.0);
        $ixsec = Ixsec::fromBool(false);
        $ichflg = IchFlg::fromBool(false);
        $stoper = StoPer::none();
        $hnoflo = HNoFlo::fromFloat(-999.99);
        $extension = Extension::fromString('bas');
        $unitnumber = UnitNumber::fromInteger(13);

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
}
