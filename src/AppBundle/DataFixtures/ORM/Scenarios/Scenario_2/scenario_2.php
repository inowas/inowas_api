<?php

namespace AppBundle\DataFixtures\ORM\Scenarios\Scenario_2;

use AppBundle\Entity\Area;
use AppBundle\Entity\AreaType;
use AppBundle\Entity\Boundary;
use AppBundle\Entity\Layer;
use AppBundle\Entity\ModelObject;
use AppBundle\Entity\ModelObjectProperty;
use AppBundle\Entity\ModelObjectPropertyType;
use AppBundle\Entity\Project;
use AppBundle\Entity\SoilProfile;
use AppBundle\Entity\SoilProfileLayer;
use AppBundle\Entity\TimeSeries;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadScenario_2 implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername('inowas_scenario_2');
        $user->setEmail('inowas_scenario_2@inowas.com');
        $user->setPassword('inowas_scenario_2');
        $userManager->updateUser($user);

        $project = new Project($user);
        $project->setName('Scenario 2');
        $entityManager->persist($project);
        $entityManager->flush();

        // Create layers
        $layers = array();
        $layers[] = new Layer($user, $project);
        $layers[] = new Layer($user, $project);
        $layers[] = new Layer($user, $project);
        $layers[] = new Layer($user, $project);

        foreach ($layers as $layer)
        {
            $entityManager->persist($layer);
        }

        $entityManager->persist($project);
        $entityManager->flush();

        /**
         * boreholes
         * format csv
         * values: x, z, top elevation, elevation layer 1 bottom, elevation layer 2 bottom, elevation layer 3 bottom, elevation layer 4 bottom
         */
        $boreholes = array(
            array(11771882.34, 2392544.12, 4.55, 3, -37.44, -38.45, -71.95),
            array(11789082.18, 2389714.82, 8.15, -1, -19.84, -20.85, -107.85),
            array(11778857.62, 2391711.98, 6.97, 1, -18.03, -31.03, -65.03),
            array(11784193.77, 2394196.31, 9.26, -1.75, -11.75, -13.75, -66.25),
            array(11781568.57, 2392545.18, 5.45, 0.45, -24.55, -38.05, -75.55),
            array(11777013.85, 2400404.42, 9.25, -1.4, -20.75, -26.75, -68.75),
            array(11783051.08, 2395101.75, 3.21, -11.79, -34.59, -38.29, -90.79),
            array(11777309.4, 2390254.19, 6.41, 1, -39.58, -40.59, -73.59),
            array(11784512.97, 2393046.65, 8.34, -0.66, -25.65, -26.66, -66.66),
            array(11778452.98, 2390393.68, 6.3, 2, -7.7, -32.2, -71.7),
            array(11778745.08, 2399607.08, 7.48, -1, -13.51, -14.52, -84.02),
            array(11778807.54, 2396471.02, 6.78, -3.73, -30.72, -31.73, -65.23),
            array(11772850.45, 2386662.03, 5.05, 1, -9.95, -29.95, -59.95),
            array(11781833.25, 2394756.26, 6.87, 2, -7.33, -13.13, -71.13),
            array(11785022.12, 2395765.18, 11.47, -1, -8.92, -9.93, -113.13),
            array(11775146.75, 2398847.69, 5.86, 0.2, -5.64, -24.14, -48.14),
            array(11781244.76, 2397032.49, 6.88, 2, -25.21, -26.22, -81.32),
            array(11777209.26, 2402770.9, 8.52, 0.7, -17.47, -18.48, -81.48),
            array(11783628.45, 2390521.59, 5.51, -7.09, -24.99, -37.99, -79.99),
            array(11787952.59, 2391352.68, 10.23, -1, -3.17, -13.77, -60.27),
            array(11772535.98, 2391516.61, 5.06, -1.44, -21.44, -34.64, -88.54),
            array(11779155.03, 2396640.9, 7.71, -1.5, -24.29, -27.79, -47.29),
            array(11760714.53, 2397939.64, 6.03, 1, -18.47, -32.47, -66.07),
            array(11774649.17, 2399215.18, 6.51, 3, -25.5, -30.5, -49.5),
            array(11782792.24, 2384025.09, 5.26, 1.76, -21.34, -33.64, -59.24),
            array(11780072.96, 2396064.94, 6.41, -3.19, -24.09, -31.09, -49.09),
            array(11777813.99, 2386822.58, 4, 0.2, -19.9, -33.2, -59.2),
            array(11786910.46, 2387406.18, 7.51, 2, -15.99, -32.29, -56.49),
            array(11788382.99, 2388557.67, 8.46, 0.86, -37.54, -41.74, -62.74),
            array(11781544.58, 2399809.73, 9.92, 1, -19.09, -24.09, -55.99),
            array(11779912.77, 2401723.79, 10.27, 0.5, -11.52, -12.53, -55.03),
            array(11778716.08, 2402222.88, 7.98, 0.2, -1, -7.02, -58.02),
            array(11782681.56, 2398443.64, 12.6, -4.4, -13.9, -16.4, -55.2),
            array(11782711.76, 2383219.36, 5.72, 1, -14.28, -35.58, -92.28),
            array(11782877.61, 2387087.35, 4.855, -10.15, -18.15, -33.15, -59.15),
            array(11780837.05, 2392172.81, 5.81, 0.5, -6.19, -37.19, -49.09),
            array(11775298.68, 2396584.49, 6.92, 0.5, 0.92, -12.58, -53.08),
            array(11771588.05, 2400278.11, 7.238, -8.76, -17.76, -26.76, -50.96),
            array(11786863.07, 2387774.93, 7.86, -4.14, -15.64, -31.14, -58.54),
            array(11785494.76, 2387728.64, 5.58, -2, -23.42, -36.22, -52.92),
            array(11785359.45, 2388446.5, 5.24, -1, -24.76, -29.76, -56.76),
            array(11783834.59, 2393475.89, 6.67, 0.47, -7.83, -25.33, -66.83),
            array(11778042.14, 2393748.39, 6.72, -0.58, -24.77, -25.78, -66.28),
            array(11774767.25, 2397966.02, 7.18, -0.5, -18.32, -19.32, -58.82),
            array(11778459.29, 2399719.84, 6.87, -2, -23.72, -24.73, -72.13),
            array(11779001.31, 2392931.32, 6.42, -2, -22.57, -23.58, -72.48),
            array(11787253.39, 2398790.31, 7.32, -11.18, -26.37, -27.38, -72.68),
            array(11779321.83, 2394682.34, 6.25, -1, -25.75, -27.55, -55.75),
            array(11788397.99, 2396629.41, 4.6, -5.1, -16.69, -17.7, -77.7),
            array(11776362.25, 2398212.2, 6.97, 0.47, -20.52, -21.53, -49.03),
            array(11780153.2, 2399710.99, 6.44, -1.7, -16.55, -17.56, -68.56),
            array(11775049.53, 2401787.45, 6.88, -1.4, -22.12, -26.52, -78.72),
            array(11773006.52, 2397389.94, 5.98, 2, -32.01, -33.02, -63.02),
            array(11775636.91, 2391945.87, 4.61, 1.01, -18.39, -22.89, -80.99),
            array(11782808.67, 2397713.81, 10.49, 3, -24.11, -30.01, -75.51),
            array(11782239.75, 2397303.54, 13.11, 2, -17.88, -18.89, -69.19),
            array(11778341.11, 2386909.75, 5.35, -2.65, -39.65, -44.65, -70.85),
            array(11777301.77, 2396625.85, 6.62, 0.3, -19.38, -20.38, -65.88),
            array(11778384.57, 2397052.79, 7, 2, -29.99, -31, -68.6),
            array(11781117.72, 2394046.04, 7.19, -9.41, -28.3, -29.31, -69.31),
            array(11781602.56, 2395825.27, 7.82, 3, -25.17, -26.18, -56.18),
            array(11784169.97, 2395592.91, 9.66, 2, -11.34, -27.34, -102.34),
            array(11781035.36, 2397295.63, 7.09, -3, -19.9, -20.91, -85.11),
            array(11782599.06, 2394228.74, 6.05, 3, -2.95, -25.45, -80.95),
            array(11784219.85, 2393183.29, 5.25, 2, -1.75, -29.75, -72.75),
            array(11784938.17, 2393227.68, 10.54, -1.2, -9.45, -10.46, -80.46),
            array(11782485.51, 2392841.91, 5.52, 3, -14.48, -31.48, -79.48),
            array(11783589.92, 2392750.53, 5.59, 1.09, -24.41, -25.41, -72.41),
            array(11777852.62, 2384147.89, 5.79, 3, -31.71, -37.21, -62.21),
            array(11778108.29, 2391057.06, 5.63, 0.13, -29.37, -36.37, -68.37),
            array(11783363.26, 2390073.25, 5.5, 0.5, -22.5, -48, -79.5),
            array(11778272.54, 2397633.23, 7.48, -1, -14.51, -15.52, -64.22),
            array(11771647.38, 2392411.6, 5.45, -4.55, -26.55, -30.55, -78.55),
            array(11776094.56, 2389455.36, 5.7, 0.5, -11.3, -33.3, -76.8),
            array(11788517.19, 2390860.62, 9.1, -2, -13.89, -14.9, -61.4),
            array(11777147.1, 2402553.12, 7.57, -0.43, -21.42, -22.43, -81.43),
            array(11786277.06, 2394518.28, 10.4, -0.5, -9.1, -20.1, -104.6),
            array(11785882.41, 2389731.92, 5.83, -1, -17.67, -33.17, -86.97),
            array(11775388.22, 2394326.9, 5.09, -6.91, -28.9, -29.91, -60.91),
            array(11783396.36, 2390930.73, 5.91, 3, -24.59, -37.59, -79.59),
            array(11783318.42, 2379920.67, 4.33, -0.67, -33.66, -34.67, -86.67),
            array(11770462, 2403116.55, 9.89, -2.5, -12.1, -13.11, -65.11),
            array(11783103.2, 2397142.16, 11.68, 3, -21.32, -23.82, -84.32),
            array(11776546.92, 2391893.9, 6.42, 2.62, -33.58, -34.58, -66.78),
            array(11780517.15, 2385713.39, 5.61, -1.89, -20.69, -39.39, -60.39),
            array(11782769.78, 2387640.47, 5.23, 2, -24.77, -37.17, -59.77),
            array(11776760.16, 2404465.83, 9.98, -2, -20.02, -29.02, -76.32),
            array(11766470.1, 2391498.39, 7.716, -3.78, -29.77, -30.78, -60.58),
            array(11775192.52, 2388842.32, 4.719, -23.28, -36.27, -37.28, -76.28),
            array(11772988.05, 2386432.76, 5.43, -9.57, -38.06, -39.07, -65.97)
        );

        foreach ($boreholes as $borehole)
        {
            $soilProfile = new SoilProfile($user, $project);
            $point = new Point($borehole[0], $borehole[1], 3857);
            $soilProfile->setPoint($point);
            $entityManager->persist($soilProfile);

            $soilProfileLayer = new SoilProfileLayer($user, $project);
            $soilProfileLayer->setSoilProfile($soilProfile);
            $soilProfileLayer->setTopElevation($borehole[2]);
            $soilProfileLayer->setBottomElevation($borehole[3]);

            /** @var Layer[] $layers */
            $layers[0]->addSoilProfileLayer($soilProfileLayer);
            $entityManager->persist($soilProfileLayer);

            $soilProfileLayer = new SoilProfileLayer($user, $project);
            $soilProfileLayer->setSoilProfile($soilProfile);
            $soilProfileLayer->setTopElevation($borehole[3]);
            $soilProfileLayer->setBottomElevation($borehole[4]);

            /** @var Layer[] $layers */
            $layers[1]->addSoilProfileLayer($soilProfileLayer);
            $entityManager->persist($soilProfileLayer);

            $soilProfileLayer = new SoilProfileLayer($user, $project);
            $soilProfileLayer->setSoilProfile($soilProfile);
            $soilProfileLayer->setTopElevation($borehole[4]);
            $soilProfileLayer->setBottomElevation($borehole[5]);

            /** @var Layer[] $layers */
            $layers[2]->addSoilProfileLayer($soilProfileLayer);
            $entityManager->persist($soilProfileLayer);

            $soilProfileLayer = new SoilProfileLayer($user, $project);
            $soilProfileLayer->setSoilProfile($soilProfile);
            $soilProfileLayer->setTopElevation($borehole[5]);
            $soilProfileLayer->setBottomElevation($borehole[6]);

            /** @var Layer[] $layers */
            $layers[3]->addSoilProfileLayer($soilProfileLayer);
            $entityManager->persist($soilProfileLayer);
        }
        $entityManager->flush();


        /*
        // Set ModelObjectProperties to layers
        // Layer 1 -> layers[0];
        $ts = new TimeSeries();
        $ts->setValue(40);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[0], 'Hydraulic conductivity', array($ts));
        $entityManager->persist($ts);
        $entityManager->persist($modelObjectProperty);

        $ts = new TimeSeries();
        $ts->setValue(8);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[0], 'Vertical anisotropy', array($ts));
        $entityManager->persist($ts);
        $entityManager->persist($modelObjectProperty);

        $ts = new TimeSeries();
        $ts->setValue(0.00001);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[0], 'Specific storage', array($ts));
        $entityManager->persist($ts);
        $entityManager->persist($modelObjectProperty);

        $ts = new TimeSeries();
        $ts->setValue(0.1);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[0], 'Specific yield', array($ts));
        $entityManager->persist($ts);
        $entityManager->persist($modelObjectProperty);

        // Set ModelObjectProperties to layers
        // Layer 2 -> layers[1];
        $ts = new TimeSeries();
        $ts->setValue(1);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[1], 'Vertical conductance', array($ts));
        $entityManager->persist($ts);
        $entityManager->persist($modelObjectProperty);

        $entityManager->flush();

        // Set ModelObjectProperties to layers
        // Layer 3 -> layers[2];
        $ts = new TimeSeries();
        $ts->setValue(42);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[2], 'Hydraulic conductivity', array($ts));
        $entityManager->persist($ts);
        $entityManager->persist($modelObjectProperty);


        $ts = new TimeSeries();
        $ts->setValue(21);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[2], 'Vertical anisotropy', array($ts));
        $entityManager->persist($ts);
        $entityManager->persist($modelObjectProperty);

        $ts = new TimeSeries();
        $ts->setValue(0.00001);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[2], 'Specific storage', array($ts));
        $entityManager->persist($ts);
        $entityManager->persist($modelObjectProperty);

        $ts = new TimeSeries();
        $ts->setValue(0.1);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[2], 'Specific yield', array($ts));
        $entityManager->persist($ts);
        $entityManager->persist($modelObjectProperty);

        // Set Area
        $polygonText = "POLYGON((11792952.16265026479959488 2396619.95378328999504447, 11789249.37860263884067535 2391542.94060458615422249, 11788791.30222561210393906 2391638.37318313354626298, 11788333.22584858722984791 2391810.1518245181068778, 11787417.07309453375637531 2392230.05517012532800436, 11786558.17988761141896248 2392459.09335863823071122, 11786367.31473051756620407 2392993.51579850167036057, 11786061.93047916702926159 2393279.81353414291515946, 11785794.7192592341452837 2393718.8033954594284296, 11785508.42152359336614609 2394291.39886674145236611, 11785412.98894504643976688 2394787.64827518630772829, 11785279.38333507999777794 2395512.93587214406579733, 11785050.34514656849205494 2395932.8392177508212626, 11784897.65302089229226112 2396448.17514190496876836, 11784802.22044234536588192 2397058.94364460650831461, 11784554.0957381222397089 2397803.31775727355852723, 11784515.92270670458674431 2398394.99974426534026861, 11784477.74967528507113457 2398853.07612129114568233, 11784248.71148677170276642 2399063.02779409429058433, 11784057.84632967785000801 2399387.49856115458533168, 11783771.5485940370708704 2399921.92100101802498102, 11783427.99131126701831818 2400360.91086233453825116, 11783504.33737410604953766 2400494.51647230051457882, 11783771.5485940370708704 2400418.1704094628803432, 11784038.75981396809220314 2400303.65131520619615912, 11784382.31709673814475536 2399979.18054814636707306, 11784725.87437950819730759 2399769.22887534275650978, 11785050.34514656849205494 2399769.22887534275650978, 11785527.50803930312395096 2399960.09403243707492948, 11785985.58441632799804211 2400189.13222094997763634, 11786462.74730906449258327 2400418.1704094628803432, 11786806.30459183268249035 2400685.38162939436733723, 11787321.64051598682999611 2400914.41981790727004409, 11788161.44720720127224922 2401277.06361638614907861, 11788848.56177274137735367 2401257.97710067685693502, 11789669.28194824606180191 2401067.11194358253851533, 11790509.08863945864140987 2400971.67936503561213613, 11791158.03017357923090458 2400666.29511368507519364, 11791558.84700347669422626 2400170.04570524021983147, 11791959.66383337415754795 2399502.01765541080385447, 11792207.78853759728372097 2398833.98960558138787746, 11792474.99975752830505371 2397822.40427298285067081, 11792761.29749316908419132 2396944.42455034982413054, 11792952.16265026479959488 2396619.95378328999504447))";
        $area = $this->addArea($entityManager, new Area($user, $project), 'area', $polygonText);
        $entityManager->persist($area);
        $this->addModelObjectPropertiesFromCSVFile($area, __DIR__.'/scenario_3_area_property_timeseries.csv', ';');

        // Set boundaries
        // Boundary 1
        $boundaryText = "LineString (11792952.16265026479959488 2396619.95378328999504447, 11792761.29749316908419132 2396944.42455034982413054, 11792474.99975752830505371 2397822.40427298285067081, 11792207.78853759728372097 2398833.98960558138787746, 11791959.66383337415754795 2399502.01765541080385447, 11791558.84700347669422626 2400170.04570524021983147, 11791158.03017357923090458 2400666.29511368507519364, 11790509.08863945864140987 2400971.67936503561213613, 11789669.28194824606180191 2401067.11194358253851533, 11788848.56177274137735367 2401257.97710067685693502, 11788161.44720720127224922 2401277.06361638614907861, 11787321.64051598682999611 2400914.41981790727004409, 11786806.30459183268249035 2400685.38162939436733723, 11786462.74730906449258327 2400418.1704094628803432, 11785985.58441632799804211 2400189.13222094997763634, 11785527.50803930312395096 2399960.09403243707492948, 11785050.34514656849205494 2399769.22887534275650978, 11784725.87437950819730759 2399769.22887534275650978, 11784382.31709673814475536 2399979.18054814636707306, 11784038.75981396809220314 2400303.65131520619615912, 11783771.5485940370708704 2400418.1704094628803432, 11783504.33737410604953766 2400494.51647230051457882, 11783427.99131126701831818 2400360.91086233453825116, 11783771.5485940370708704 2399921.92100101802498102, 11784057.84632967785000801 2399387.49856115458533168, 11784248.71148677170276642 2399063.02779409429058433, 11784477.74967528507113457 2398853.07612129114568233, 11784515.92270670458674431 2398394.99974426534026861, 11784554.0957381222397089 2397803.31775727355852723, 11784802.22044234536588192 2397058.94364460650831461, 11784897.65302089229226112 2396448.17514190496876836, 11785050.34514656849205494 2395932.8392177508212626, 11785279.38333507999777794 2395512.93587214406579733, 11785412.98894504643976688 2394787.64827518630772829, 11785508.42152359336614609 2394291.39886674145236611, 11785794.7192592341452837 2393718.8033954594284296, 11786061.93047916702926159 2393279.81353414291515946, 11786367.31473051756620407 2392993.51579850167036057, 11786558.17988761141896248 2392459.09335863823071122, 11787417.07309453375637531 2392230.05517012532800436, 11788333.22584858722984791 2391810.1518245181068778, 11788791.30222561210393906 2391638.37318313354626298)";
        $boundary_1 = $this->addBoundary(new Boundary($user, $project), $boundaryText);
        $entityManager->persist($boundary_1);
        $this->addModelObjectPropertiesFromCSVFile($boundary_1, __DIR__.'/scenario_3_boundary_1_property_timeseries.csv', ';');

        // Boundary 2
        $boundaryText = "LineString (11792952.16265026479959488 2396619.95378328999504447, 11789249.37860263884067535 2391542.94060458615422249, 11788791.30222561210393906 2391638.37318313354626298)";
        $boundary_2 = $this->addBoundary(new Boundary($user, $project), $boundaryText);
        $entityManager->persist($boundary_2);
        $this->addModelObjectPropertiesFromCSVFile($boundary_2, __DIR__.'/scenario_3_boundary_2_property_timeseries.csv', ';');
*/
        // Flush all Information to the database
        $entityManager->flush();
    }


    public function addModelObjectPropertiesFromCSVFile(ModelObject $baseElement, $filename, $delimiter)
    {
        $data = $this->convert($filename, $delimiter);
        $elementCount = count($data[0])-1;
        $dataFields = array_keys($data[0]);
        $counter = 0;

        for ($i = 1; $i <= $elementCount; $i++)
        {
            $modelObjectPropertyTypeName = $dataFields[$i];

            $modelObjectProperty = new ModelObjectProperty();
            $modelObjectPropertyType = $this->entityManager->getRepository('AppBundle:ModelObjectPropertyType')
                ->findOneBy(array(
                    'name' => $modelObjectPropertyTypeName
                ));

            if (!$modelObjectPropertyType)
            {
                $modelObjectPropertyType = new ModelObjectPropertyType();
                $modelObjectPropertyType->setName($modelObjectPropertyTypeName);
                $this->entityManager->persist($modelObjectPropertyType);
                $this->entityManager->flush();
            }

            $modelObjectProperty->setType($modelObjectPropertyType);
            $modelObjectProperty->setModelObject($baseElement);
            $this->entityManager->persist($modelObjectProperty);
            $this->entityManager->flush();


            foreach ($data as $dataPoint)
            {
                $ts = new TimeSeries();
                $ts->setTimeStamp(new \DateTime($dataPoint[$dataFields[0]]));
                $ts->setValue((float)$dataPoint[$dataFields[$i]]);
                $ts->setModelObjectProperties($modelObjectProperty);
                $this->entityManager->persist($ts);

                /**
                    $em = $this->container->get('doctrine.orm.default_entity_manager');
                    $rsm = new ResultSetMapping();
                    $nativeQuery = 'INSERT INTO inowas_time_series
                                    (model_object_properties_id, timestamp, value, id)
                                    VALUES
                                    (?, ?, ?, DEFAULT)';

                    $query = $em->createNativeQuery($nativeQuery, $rsm);
                    $query->setParameter(1, $modelObjectProperty->getId());
                    $query->setParameter(2, new \DateTime($dataPoint[$dataFields[0]]));
                    $query->setParameter(3, (float)$dataPoint[$dataFields[$i+1]]);
                    $result = $query->getResult();
                */

                echo $counter++."\n";
                if ($counter % 20 == 0)
                {
                    $this->entityManager->flush();
                }
            }
        }
    }

    public function generateTimeSeriesB0()
    {
        $date = new \DateTime('2000-01-01T00:00:00+00:00');
        $dateEnd = new \DateTime('2003-12-31');

        $timeSeries = array();

        while ($date <= $dateEnd)
        {
            $ts = new TimeSeries();
            $ts->setTimeStamp(clone $date);
            $ts->setValue(5);
            $timeSeries[] = $ts;
            $date->add(new \DateInterval('P1D'));
        }

        return $timeSeries;
    }

    public function generateTimeSeriesB1()
    {
        $date = new \DateTime('2000-01-01T00:00:00+00:00');
        $dateEnd = new \DateTime('2003-12-31');

        $timeSeries = array();

        while ($date <= $dateEnd)
        {
            $ts = new TimeSeries();
            $ts->setTimeStamp(clone $date);
            $ts->setValue(5);
            $timeSeries[] = $ts;
            $date->add(new \DateInterval('P1D'));
        }

        return $timeSeries;
    }

    public function addBoundary(Boundary $boundary, $geometryText)
    {
        $converter = new \CrEOF\Spatial\DBAL\Types\Geometry\Platforms\PostgreSql();

        /** @var LineString $lineString */
        $lineString = $converter->convertStringToPHPValue($geometryText);
        $lineString->setSrid(3857);

        $boundary->setGeometry($lineString);

        return $boundary;
    }

    public function addArea(ObjectManager $entityManager, Area $area, $areaTypeName, $geometryText)
    {
        $converter = new \CrEOF\Spatial\DBAL\Types\Geometry\Platforms\PostgreSql();
        $areaType = $entityManager->getRepository('AppBundle:AreaType')
            ->findOneBy(array(
                'name' => $areaTypeName
            ));

        if (!$areaType)
        {
            $areaType = new AreaType();
            $areaType->setName($areaTypeName);
            $entityManager->persist($areaType);
        }

        $area->setAreaType($areaType);

        /** @var Polygon $polygon */
        $polygon = $converter->convertStringToPHPValue($geometryText);
        $polygon->setSrid(3857);

        $area->setGeometry($polygon);

        return $area;
    }

    public function addModelObjectProperty(ObjectManager $entityManager, ModelObject $modelObject, $modelObjectPropertyTypeName, $timeSeries)
    {
        $modelObjectProperty = new ModelObjectProperty();
        $modelObjectPropertyType = $entityManager->getRepository('AppBundle:ModelObjectPropertyType')
            ->findOneBy(array(
                'name' => $modelObjectPropertyTypeName
            ));

        if (!$modelObjectPropertyType)
        {
            $modelObjectPropertyType = new ModelObjectPropertyType();
            $modelObjectPropertyType->setName($modelObjectPropertyTypeName);
            $entityManager->persist($modelObjectPropertyType);
            $entityManager->flush();
        }

        $modelObjectProperty->setType($modelObjectPropertyType);

        /** @var TimeSeries $timeserie */
        foreach ($timeSeries as $timeserie)
        {
            if ($timeserie instanceof TimeSeries)
            {
                $timeserie->setModelObjectProperties($modelObjectProperty);
                $modelObjectProperty->addTimeSeries($timeserie);
                $entityManager->persist($timeserie);
                $entityManager->persist($modelObjectProperty);
                $entityManager->flush();
            }
        }
        $modelObjectProperty->setModelObject($modelObject);
        return $modelObject;
    }

    public function convert($filename, $delimiter = ',')
    {
        if(!file_exists($filename) || !is_readable($filename)) {
            return FALSE;
        }

        $header = NULL;
        $data = array();

        if (($handle = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                if(!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

}