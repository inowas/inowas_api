<?php

namespace AppBundle\DataFixtures\ORM;

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

class LoadScenario_3 implements FixtureInterface, ContainerAwareInterface
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
        $user->setUsername('inowas_scenario_3');
        $user->setEmail('inowas_scenario_3@inowas.com');
        $user->setPassword('inowas_scenario_3');
        $userManager->updateUser($user);

        $project = new Project($user);
        $project->setName('Scenario 3');
        $entityManager->persist($project);

        // Create layers
        $layers = array();
        $layers[] = new Layer($user, $project);
        $layers[] = new Layer($user, $project);
        $layers[] = new Layer($user, $project);

        foreach ($layers as $layer)
        {
            $entityManager->persist($layer);
        }

        /**
         * boreholes
         * format csv
         * values: x, z, top elevation, elevation layer 1 bottom, elevation layer 2 bottom, elevation layer 3 bottom
         */
        $boreholes = array(
            array(11788180.14119624905288219, 2399553.70677717169746757, 6.9620555082, -15.620461, -16.6950614647, -29.6238878368),
            array(11786677.1576645914465189, 2399023.25597102148458362, 6.3898393629, -18.692347, -19.6996569609, -32.5665305456),
            array(11790319.33101668581366539, 2398894.851558992639184, 6.4946664581, -13.079302, -14.910420567, -30.8610677157),
            array(11787852.11418525315821171, 2398370.05450820876285434, 6.6255488629, -14.659299, -16.0398404293, -32.55306351),
            array(11788813.92677002400159836, 2397933.54498588852584362, 6.00167328, -13.052457, -14.6532666561, -32.1812023187),
            array(11786027.24900209158658981, 2397733.62118429923430085, 5.8840840611, -18.705297, -20.7075689585, -35.5333428407),
            array(11789988.11762658506631851, 2397172.48782889731228352, 4.2165512235, -7.0936039, -12.0379149657, -31.1265758612),
            array(11791380.79679805226624012, 2397164.43166950764134526, 4.3417676952, -10.386462, -12.8329227765, -38.8812598791),
            array(11788809.08773461729288101, 2397071.46049584727734327, 5.8281212776, -13.302987, -16.0170465372, -33.3523350985),
            array(11787630.06820404902100563, 2396970.35447654547169805, 6.3122125587, -19.153846, -19.4959900515, -36.3420303062),
            array(11789449.4274347797036171, 2396636.77293530059978366, 4.8102839087, -7.3111901, -12.3626654894, -31.2542124112),
            array(11786126.7311509121209383, 2396332.1691995277069509, 8.0264573378, -20.123346, -20.1233463996, -34.6577082023),
            array(11788587.59028892032802105, 2395779.62533995928242803, 5.4408550039, -9.0921872, -13.6747104681, -31.6138470968),
            array(11787194.41469062119722366, 2395679.70603070175275207, 6.6896371476, -25.315095, -25.9193635476, -39.5301528491),
            array(11790727.54822205752134323, 2395336.36947100562974811, 5.883913212, -12.022013, -13.6956019976, -42.7144197219),
            array(11789653.92750435508787632, 2394911.54699952248483896, 4.8793368059, -14.590854, -16.9008577469, -35.5136943279),
            array(11788792.7711452916264534, 2394162.22863991418853402, 7.4945438256, -18.060908, -20.4922798132, -31.0874280172),
            array(11786864.16385251842439175, 2394065.31637709401547909, 9.8397560393, -12.043455, -30.8630841769, -31.6942006527),
            array(11790286.8163007590919733, 2393184.02505666017532349, 5.1983350317, -9.573477, -10.2657631458, -33.081997754),
            array(11788463.01185098849236965, 2392655.74013845855370164, 9.6630525443, -13.489015, -14.6492345712, -29.4182375737)
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
        }

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
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[0], 'Vertical conductance', array($ts));
        $entityManager->persist($ts);
        $entityManager->persist($modelObjectProperty);

        $entityManager->flush();

        // Set ModelObjectProperties to layers
        // Layer 3 -> layers[2];
        $ts = new TimeSeries();
        $ts->setValue(42);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[0], 'Hydraulic conductivity', array($ts));
        $entityManager->persist($ts);
        $entityManager->persist($modelObjectProperty);


        $ts = new TimeSeries();
        $ts->setValue(21);
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

        // Set Area
        $polygonText = "POLYGON((11792952.16265026479959488 2396619.95378328999504447, 11789249.37860263884067535 2391542.94060458615422249, 11788791.30222561210393906 2391638.37318313354626298, 11788333.22584858722984791 2391810.1518245181068778, 11787417.07309453375637531 2392230.05517012532800436, 11786558.17988761141896248 2392459.09335863823071122, 11786367.31473051756620407 2392993.51579850167036057, 11786061.93047916702926159 2393279.81353414291515946, 11785794.7192592341452837 2393718.8033954594284296, 11785508.42152359336614609 2394291.39886674145236611, 11785412.98894504643976688 2394787.64827518630772829, 11785279.38333507999777794 2395512.93587214406579733, 11785050.34514656849205494 2395932.8392177508212626, 11784897.65302089229226112 2396448.17514190496876836, 11784802.22044234536588192 2397058.94364460650831461, 11784554.0957381222397089 2397803.31775727355852723, 11784515.92270670458674431 2398394.99974426534026861, 11784477.74967528507113457 2398853.07612129114568233, 11784248.71148677170276642 2399063.02779409429058433, 11784057.84632967785000801 2399387.49856115458533168, 11783771.5485940370708704 2399921.92100101802498102, 11783427.99131126701831818 2400360.91086233453825116, 11783504.33737410604953766 2400494.51647230051457882, 11783771.5485940370708704 2400418.1704094628803432, 11784038.75981396809220314 2400303.65131520619615912, 11784382.31709673814475536 2399979.18054814636707306, 11784725.87437950819730759 2399769.22887534275650978, 11785050.34514656849205494 2399769.22887534275650978, 11785527.50803930312395096 2399960.09403243707492948, 11785985.58441632799804211 2400189.13222094997763634, 11786462.74730906449258327 2400418.1704094628803432, 11786806.30459183268249035 2400685.38162939436733723, 11787321.64051598682999611 2400914.41981790727004409, 11788161.44720720127224922 2401277.06361638614907861, 11788848.56177274137735367 2401257.97710067685693502, 11789669.28194824606180191 2401067.11194358253851533, 11790509.08863945864140987 2400971.67936503561213613, 11791158.03017357923090458 2400666.29511368507519364, 11791558.84700347669422626 2400170.04570524021983147, 11791959.66383337415754795 2399502.01765541080385447, 11792207.78853759728372097 2398833.98960558138787746, 11792474.99975752830505371 2397822.40427298285067081, 11792761.29749316908419132 2396944.42455034982413054, 11792952.16265026479959488 2396619.95378328999504447))";
        $area = $this->addArea($entityManager, new Area($user, $project), 'area', $polygonText);
        $entityManager->persist($area);

        $this->addModelObjectPropertiesFromCSVFile($area, __DIR__.'/scenario_3_area_mop.csv', ';');

        // Set boundaries
        $boundaries = array();
        // Boundary 1
        $boundaryText = "LineString (11792952.16265026479959488 2396619.95378328999504447, 11792761.29749316908419132 2396944.42455034982413054, 11792474.99975752830505371 2397822.40427298285067081, 11792207.78853759728372097 2398833.98960558138787746, 11791959.66383337415754795 2399502.01765541080385447, 11791558.84700347669422626 2400170.04570524021983147, 11791158.03017357923090458 2400666.29511368507519364, 11790509.08863945864140987 2400971.67936503561213613, 11789669.28194824606180191 2401067.11194358253851533, 11788848.56177274137735367 2401257.97710067685693502, 11788161.44720720127224922 2401277.06361638614907861, 11787321.64051598682999611 2400914.41981790727004409, 11786806.30459183268249035 2400685.38162939436733723, 11786462.74730906449258327 2400418.1704094628803432, 11785985.58441632799804211 2400189.13222094997763634, 11785527.50803930312395096 2399960.09403243707492948, 11785050.34514656849205494 2399769.22887534275650978, 11784725.87437950819730759 2399769.22887534275650978, 11784382.31709673814475536 2399979.18054814636707306, 11784038.75981396809220314 2400303.65131520619615912, 11783771.5485940370708704 2400418.1704094628803432, 11783504.33737410604953766 2400494.51647230051457882, 11783427.99131126701831818 2400360.91086233453825116, 11783771.5485940370708704 2399921.92100101802498102, 11784057.84632967785000801 2399387.49856115458533168, 11784248.71148677170276642 2399063.02779409429058433, 11784477.74967528507113457 2398853.07612129114568233, 11784515.92270670458674431 2398394.99974426534026861, 11784554.0957381222397089 2397803.31775727355852723, 11784802.22044234536588192 2397058.94364460650831461, 11784897.65302089229226112 2396448.17514190496876836, 11785050.34514656849205494 2395932.8392177508212626, 11785279.38333507999777794 2395512.93587214406579733, 11785412.98894504643976688 2394787.64827518630772829, 11785508.42152359336614609 2394291.39886674145236611, 11785794.7192592341452837 2393718.8033954594284296, 11786061.93047916702926159 2393279.81353414291515946, 11786367.31473051756620407 2392993.51579850167036057, 11786558.17988761141896248 2392459.09335863823071122, 11787417.07309453375637531 2392230.05517012532800436, 11788333.22584858722984791 2391810.1518245181068778, 11788791.30222561210393906 2391638.37318313354626298)";
        $boundary = $this->addBoundary($boundaryText);
        $timeSeries_b0 = $this->generateTimeSeriesB0();
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $boundary, 'Water head', $timeSeries_b0);
        $boundaries[] = $boundary;
        $entityManager->persist($modelObjectProperty);

        // Boundary 2
        $boundaryText = "LineString (11792952.16265026479959488 2396619.95378328999504447, 11789249.37860263884067535 2391542.94060458615422249, 11788791.30222561210393906 2391638.37318313354626298)";
        $boundaries[] = $this->addBoundary($boundaryText);
        $timeSeries_b1 = $this->generateTimeSeriesB1();
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $boundary, 'Water head', $timeSeries_b1);
        $boundaries[] = $boundary;
        $entityManager->persist($modelObjectProperty);

        foreach ($boundaries as $boundary)
        {
            $entityManager->persist($boundary);
        }



        // Flush all Information to the database
        $entityManager->flush();
    }

    public function addModelObjectPropertiesFromCSVFile(ModelObject $baseElement, $filename, $delimiter)
    {
        $data = $this->convert($filename, $delimiter);
        $elementCount = count($data[0])-1;
        $dataFields = array_keys($data[0]);

        for ($i = 0; $i < $elementCount; $i++) {
            $timeseries = array();
            foreach ($data as $dataPoint)
            {
                $ts = new TimeSeries();
                $ts->setTimeStamp(new \DateTime($dataPoint[$dataFields[0]]));
                $ts->setValue((float)$dataPoint[$dataFields[$i+1]]);
                $timeseries[] = $ts;
            }
            
            $this->addModelObjectProperty($this->entityManager, $baseElement, $dataFields[$i], $timeseries);
        }

        die();
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

    public function addBoundary($geometryText)
    {
        $converter = new \CrEOF\Spatial\DBAL\Types\Geometry\Platforms\PostgreSql();

        /** @var LineString $lineString */
        $lineString = $converter->convertStringToPHPValue($geometryText);
        $lineString->setSrid(3857);

        $boundary = new Boundary();
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