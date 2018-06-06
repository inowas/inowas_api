<?php

namespace Tests\Inowas\Modflow\Model\Packages;

use Inowas\Common\Grid\Ncol;
use Inowas\Common\Grid\Nlay;
use Inowas\Common\Grid\Nrow;
use Inowas\Common\Modflow\Extension;
use Inowas\Common\Modflow\HeadObservation;
use Inowas\Common\Modflow\HeadObservationCollection;
use Inowas\Common\Modflow\Hobdry;
use Inowas\Common\Modflow\Iuhobsv;
use Inowas\Common\Modflow\Obsname;
use Inowas\Common\Modflow\TimeSeriesData;
use Inowas\Common\Modflow\Tomulth;
use Inowas\Common\Modflow\Unitnumber;
use Inowas\ModflowModel\Model\Packages\HobPackage;

class HobPackageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function create_from_defaults(): void
    {
        // DEFAULTS
        $iuhobsv = Iuhobsv::fromInt(1051);
        $hobdry = Hobdry::fromFloat(0);
        $tomulth = Tomulth::fromFloat(1.0);
        $obsData = HeadObservationCollection::create();
        $extension = Extension::fromString('hob');
        $unitnumber = Unitnumber::fromValue(null);

        $hobPackage = HobPackage::fromDefaults();
        $this->assertInstanceOf(HobPackage::class, $hobPackage);
        $this->assertTrue($hobPackage->getIuhobsv()->sameAs($iuhobsv));
        $this->assertTrue($hobPackage->getHobdry()->sameAs($hobdry));
        $this->assertTrue($hobPackage->getTomulth()->sameAs($tomulth));
        $this->assertTrue($hobPackage->getObsData()->sameAs($obsData));
        $this->assertTrue($hobPackage->getExtension()->sameAs($extension));
        $this->assertTrue($hobPackage->getUnitnumber()->sameAs($unitnumber));

        $json = json_encode($hobPackage);
        $this->assertJson($json);
    }

    /**
     * @test
     */
    public function update_obs_data(): void
    {
        $newHobData = HeadObservationCollection::create();
        $newHobData->add(HeadObservation::fromNameLayerRowColumnAndTimeSeriesData(
            Obsname::fromString('hob_01'),
            Nlay::fromInt(0),
            Nrow::fromInt(1),
            Ncol::fromInt(3),
            TimeSeriesData::fromArray([[0, 10], [1, 11]])
        ));

        $hobPackage = HobPackage::fromDefaults();
        $hobPackage = $hobPackage->updateObsData($newHobData);

        $this->assertInstanceOf(HobPackage::class, $hobPackage);
        $this->assertTrue($hobPackage->getObsData()->sameAs($newHobData));
    }
}
