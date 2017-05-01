<?php

namespace Inowas\Soilmodel\Tests\Model;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Soilmodel\Interpolation\InterpolationConfiguration;
use Inowas\Soilmodel\Interpolation\InterpolationResult;
use Inowas\Soilmodel\Interpolation\PyModellingInterpolation;
use Inowas\SoilmodelBundle\Model\PointValue;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InterpolationTest extends WebTestCase
{
    /** @var PyModellingInterpolation */
    protected $interpolationService;

    /** @var InterpolationConfiguration */
    protected $interpolation;

    public function setUp(){

        self::bootKernel();

        $this->interpolationService = static::$kernel->getContainer()
            ->get('inowas.soilmodel.interpolation_service')
        ;

        $this->interpolation = new InterpolationConfiguration();
        $this->interpolation->addMethod(InterpolationConfiguration::METHOD_GAUSSIAN);
        $this->interpolation->setGridSize(GridSize::fromXY(100, 100));
        $this->interpolation->setBoundingBox(BoundingBox::fromCoordinates(11775189.217654, 11789747.539231, 2385794.8345812, 2403506.4981163, 4835));
        $this->interpolation->addPointValue(new PointValue(new Point(11771882.34, 2392544.12), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(11789082.18, 2389714.82), -1));
        $this->interpolation->addPointValue(new PointValue(new Point(11778857.62, 2391711.98), 1));
        $this->interpolation->addPointValue(new PointValue(new Point(11784193.77, 2394196.31), -1.75));
        $this->interpolation->addPointValue(new PointValue(new Point(11781568.57, 2392545.18), 0.45));
        $this->interpolation->addPointValue(new PointValue(new Point(11777013.85, 2400404.42), -1.4));
        $this->interpolation->addPointValue(new PointValue(new Point(11783051.08, 2395101.75), -11.79));
        $this->interpolation->addPointValue(new PointValue(new Point(11777309.40, 2390254.19), 1));
        $this->interpolation->addPointValue(new PointValue(new Point(11778452.98, 2390393.68), 2));
        $this->interpolation->addPointValue(new PointValue(new Point(11784512.97, 2393046.65), -0.66));
        $this->interpolation->addPointValue(new PointValue(new Point(11778807.54, 2396471.02), -3.73));
        $this->interpolation->addPointValue(new PointValue(new Point(11781833.25, 2394756.26), 2));
        $this->interpolation->addPointValue(new PointValue(new Point(11778745.08, 2399607.08), -1));
        $this->interpolation->addPointValue(new PointValue(new Point(11772850.45, 2386662.03), 1));
        $this->interpolation->addPointValue(new PointValue(new Point(11785022.12, 2395765.18), -1));
        $this->interpolation->addPointValue(new PointValue(new Point(11775146.75, 2398847.69), 0.2));
        $this->interpolation->addPointValue(new PointValue(new Point(11777209.26, 2402770.9), 0.7));
        $this->interpolation->addPointValue(new PointValue(new Point(11787952.59, 2391352.68), -1));
        $this->interpolation->addPointValue(new PointValue(new Point(11779155.03, 2396640.9), -1.5));
        $this->interpolation->addPointValue(new PointValue(new Point(11772535.98, 2391516.61), -1.44));
        $this->interpolation->addPointValue(new PointValue(new Point(11760714.53, 2397939.64), 1));
        $this->interpolation->addPointValue(new PointValue(new Point(11783628.45, 2390521.59), -7.09));
        $this->interpolation->addPointValue(new PointValue(new Point(11782792.24, 2384025.09), 1.76));
        $this->interpolation->addPointValue(new PointValue(new Point(11774649.17, 2399215.18), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(11781244.76, 2397032.49), 2));
        $this->interpolation->addPointValue(new PointValue(new Point(11777813.99, 2386822.58), 0.2));
        $this->interpolation->addPointValue(new PointValue(new Point(11786910.46, 2387406.18), 2));
        $this->interpolation->addPointValue(new PointValue(new Point(11788382.99, 2388557.67), 0.86));
        $this->interpolation->addPointValue(new PointValue(new Point(11781544.58, 2399809.73), 1));
        $this->interpolation->addPointValue(new PointValue(new Point(11780072.96, 2396064.94), -3.19));
        $this->interpolation->addPointValue(new PointValue(new Point(11779912.77, 2401723.79), 0.5));
        $this->interpolation->addPointValue(new PointValue(new Point(11782681.56, 2398443.64), -4.4));
        $this->interpolation->addPointValue(new PointValue(new Point(11778716.08, 2402222.88), 0.2));
        $this->interpolation->addPointValue(new PointValue(new Point(11782711.76, 2383219.36), 1));
        $this->interpolation->addPointValue(new PointValue(new Point(11782877.61, 2387087.35), -10.15));
        $this->interpolation->addPointValue(new PointValue(new Point(11775298.68, 2396584.49), 0.5));
        $this->interpolation->addPointValue(new PointValue(new Point(11771588.05, 2400278.11), -8.76));
        $this->interpolation->addPointValue(new PointValue(new Point(11774767.25, 2397966.02), -0.5));
        $this->interpolation->addPointValue(new PointValue(new Point(11778042.14, 2393748.39), -0.58));
        $this->interpolation->addPointValue(new PointValue(new Point(11783834.59, 2393475.89), 0.47));
        $this->interpolation->addPointValue(new PointValue(new Point(11785494.76, 2387728.64), -2));
        $this->interpolation->addPointValue(new PointValue(new Point(11785359.45, 2388446.5), -1));
        $this->interpolation->addPointValue(new PointValue(new Point(11780837.05, 2392172.81), 0.5));
        $this->interpolation->addPointValue(new PointValue(new Point(11786863.07, 2387774.93), -4.14));
        $this->interpolation->addPointValue(new PointValue(new Point(11778459.29, 2399719.84), -2));
        $this->interpolation->addPointValue(new PointValue(new Point(11787253.39, 2398790.31), -11.18));
        $this->interpolation->addPointValue(new PointValue(new Point(11779321.83, 2394682.34), -1));
        $this->interpolation->addPointValue(new PointValue(new Point(11780153.20, 2399710.99), -1.7));
        $this->interpolation->addPointValue(new PointValue(new Point(11776362.25, 2398212.2), 0.47));
        $this->interpolation->addPointValue(new PointValue(new Point(11779001.31, 2392931.32), -2));
        $this->interpolation->addPointValue(new PointValue(new Point(11775049.53, 2401787.45), -1.4));
        $this->interpolation->addPointValue(new PointValue(new Point(11788397.99, 2396629.41), -5.1));
        $this->interpolation->addPointValue(new PointValue(new Point(11773006.52, 2397389.94), 2));
        $this->interpolation->addPointValue(new PointValue(new Point(11775636.91, 2391945.87), 1.01));
        $this->interpolation->addPointValue(new PointValue(new Point(11782239.75, 2397303.54), 2));
        $this->interpolation->addPointValue(new PointValue(new Point(11778341.11, 2386909.75), -2.65));
        $this->interpolation->addPointValue(new PointValue(new Point(11777301.77, 2396625.85), 0.3));
        $this->interpolation->addPointValue(new PointValue(new Point(11778384.57, 2397052.79), 2));
        $this->interpolation->addPointValue(new PointValue(new Point(11781117.72, 2394046.04), -9.41));
        $this->interpolation->addPointValue(new PointValue(new Point(11781602.56, 2395825.27), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(11782808.67, 2397713.81), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(11781035.36, 2397295.63), -3));
        $this->interpolation->addPointValue(new PointValue(new Point(11784169.97, 2395592.91), 2));
        $this->interpolation->addPointValue(new PointValue(new Point(11782599.06, 2394228.74), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(11784938.17, 2393227.68), -1.2));
        $this->interpolation->addPointValue(new PointValue(new Point(11783363.26, 2390073.25), 0.5));
        $this->interpolation->addPointValue(new PointValue(new Point(11777852.62, 2384147.89), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(11782485.51, 2392841.91), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(11784219.85, 2393183.29), 2));
        $this->interpolation->addPointValue(new PointValue(new Point(11783589.92, 2392750.53), 1.09));
        $this->interpolation->addPointValue(new PointValue(new Point(11778108.29, 2391057.06), 0.13));
        $this->interpolation->addPointValue(new PointValue(new Point(11778272.54, 2397633.23), -1));
        $this->interpolation->addPointValue(new PointValue(new Point(11776094.56, 2389455.36), 0.5));
        $this->interpolation->addPointValue(new PointValue(new Point(11788517.19, 2390860.62), -2));
        $this->interpolation->addPointValue(new PointValue(new Point(11771647.38, 2392411.6), -4.55));
        $this->interpolation->addPointValue(new PointValue(new Point(11785882.41, 2389731.92), -1));
        $this->interpolation->addPointValue(new PointValue(new Point(11786277.06, 2394518.28), -0.5));
        $this->interpolation->addPointValue(new PointValue(new Point(11775388.22, 2394326.9), -6.91));
        $this->interpolation->addPointValue(new PointValue(new Point(11777147.10, 2402553.12), -0.43));
        $this->interpolation->addPointValue(new PointValue(new Point(11783396.36, 2390930.73), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(11783318.42, 2379920.67), -0.67));
        $this->interpolation->addPointValue(new PointValue(new Point(11770462.00, 2403116.55), -2.5));
        $this->interpolation->addPointValue(new PointValue(new Point(11783103.20, 2397142.16), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(11780517.15, 2385713.39), -1.89));
        $this->interpolation->addPointValue(new PointValue(new Point(11776546.92, 2391893.9), 2.62));
        $this->interpolation->addPointValue(new PointValue(new Point(11782769.78, 2387640.47), 2));
        $this->interpolation->addPointValue(new PointValue(new Point(11776760.16, 2404465.83), -2));
        $this->interpolation->addPointValue(new PointValue(new Point(11766470.10, 2391498.39), -3.78));
        $this->interpolation->addPointValue(new PointValue(new Point(11775192.52, 2388842.32), -23.28));
        $this->interpolation->addPointValue(new PointValue(new Point(11772988.05, 2386432.76), -9.57));
    }

    /**
     * @group messaging-integration-tests
     */
    public function test_the_interpolation()
    {
        /** @var InterpolationResult $result */
        $result = $this->interpolationService->interpolate($this->interpolation);
        $this->assertInstanceOf(InterpolationResult::class, $result);
    }
}
