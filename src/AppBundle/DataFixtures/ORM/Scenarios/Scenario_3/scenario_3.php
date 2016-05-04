<?php

namespace AppBundle\DataFixtures\ORM\Scenarios\Scenario_3;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\User;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\AreaTypeFactory;
use AppBundle\Model\BoundaryFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyTimeValueFactory;
use AppBundle\Model\PropertyValueFactory;

use AppBundle\Model\Point;
use AppBundle\Model\SoilModelFactory;
use CrEOF\Spatial\DBAL\Platform\PostgreSql;
use CrEOF\Spatial\DBAL\Types\AbstractSpatialType;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $public = true;
        $this->entityManager = $entityManager;

        $username = 'inowas';
        $email = 'inowas@inowas.com';
        $password = 'inowas';

        $user = $entityManager->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $username
            ));

        if (!$user)
        {
            // Add new User
            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setEnabled(true);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        // Create Modflow-Model
        /** @var ModFlowModel $modflowModel */
        $modflowModel = ModFlowModelFactory::create()
            ->setName('Modflow-Model Scenario 3')
            ->setOwner($user)
        ;
        $entityManager->persist($modflowModel);
        $entityManager->flush();

        $properties = $modflowModel->getCalculationProperties();
        $properties['grid_size'] = array(
            'rows' => 50,
            'cols' => 50
        );
        $modflowModel->setCalculationProperties($properties);
        $entityManager->persist($modflowModel);
        $entityManager->flush();

        // Create a soilmodel
        $soilModel = SoilModelFactory::create();
        $soilModel->setOwner($user)->setName('SM Scenario 3');
        $modflowModel->setSoilModel($soilModel);
        $entityManager->persist($soilModel);

        // Create new geological layers
        $layer_1 = GeologicalLayerFactory::setOwnerNameAndPublic($user, 'SC3_L1', $public);
        $soilModel->addGeologicalLayer($layer_1);
        $entityManager->persist($layer_1);

        $layer_2 = GeologicalLayerFactory::setOwnerNameAndPublic($user, 'SC3_L2', $public);
        $soilModel->addGeologicalLayer($layer_2);
        $entityManager->persist($layer_2);

        $layer_3 = GeologicalLayerFactory::setOwnerNameAndPublic($user, 'SC3_L3', $public);
        $soilModel->addGeologicalLayer($layer_3);
        $entityManager->persist($layer_3);
        $entityManager->flush();

        /**
         * Geological Units
         * format csv
         * values: name, x, z, top elevation, elevation layer 1 bottom, elevation layer 2 bottom, elevation layer 3 bottom
         */
        $boreholes = array(
            array('SC3_GU1', 11788180.14119624905288219, 2399553.70677717169746757, 6.9620555082, -15.620461, -16.6950614647, -29.6238878368),
            array('SC3_GU2', 11786677.1576645914465189, 2399023.25597102148458362, 6.3898393629, -18.692347, -19.6996569609, -32.5665305456),
            array('SC3_GU3', 11790319.33101668581366539, 2398894.851558992639184, 6.4946664581, -13.079302, -14.910420567, -30.8610677157),
            array('SC3_GU4', 11787852.11418525315821171, 2398370.05450820876285434, 6.6255488629, -14.659299, -16.0398404293, -32.55306351),
            array('SC3_GU5', 11788813.92677002400159836, 2397933.54498588852584362, 6.00167328, -13.052457, -14.6532666561, -32.1812023187),
            array('SC3_GU6', 11786027.24900209158658981, 2397733.62118429923430085, 5.8840840611, -18.705297, -20.7075689585, -35.5333428407),
            array('SC3_GU7', 11789988.11762658506631851, 2397172.48782889731228352, 4.2165512235, -7.0936039, -12.0379149657, -31.1265758612),
            array('SC3_GU8', 11791380.79679805226624012, 2397164.43166950764134526, 4.3417676952, -10.386462, -12.8329227765, -38.8812598791),
            array('SC3_GU9', 11788809.08773461729288101, 2397071.46049584727734327, 5.8281212776, -13.302987, -16.0170465372, -33.3523350985),
            array('SC3_GU10', 11787630.06820404902100563, 2396970.35447654547169805, 6.3122125587, -19.153846, -19.4959900515, -36.3420303062),
            array('SC3_GU11', 11789449.4274347797036171, 2396636.77293530059978366, 4.8102839087, -7.3111901, -12.3626654894, -31.2542124112),
            array('SC3_GU12', 11786126.7311509121209383, 2396332.1691995277069509, 8.0264573378, -20.123346, -20.1233463996, -34.6577082023),
            array('SC3_GU13', 11788587.59028892032802105, 2395779.62533995928242803, 5.4408550039, -9.0921872, -13.6747104681, -31.6138470968),
            array('SC3_GU14', 11787194.41469062119722366, 2395679.70603070175275207, 6.6896371476, -25.315095, -25.9193635476, -39.5301528491),
            array('SC3_GU15', 11790727.54822205752134323, 2395336.36947100562974811, 5.883913212, -12.022013, -13.6956019976, -42.7144197219),
            array('SC3_GU16', 11789653.92750435508787632, 2394911.54699952248483896, 4.8793368059, -14.590854, -16.9008577469, -35.5136943279),
            array('SC3_GU17', 11788792.7711452916264534, 2394162.22863991418853402, 7.4945438256, -18.060908, -20.4922798132, -31.0874280172),
            array('SC3_GU18', 11786864.16385251842439175, 2394065.31637709401547909, 9.8397560393, -12.043455, -30.8630841769, -31.6942006527),
            array('SC3_GU19', 11790286.8163007590919733, 2393184.02505666017532349, 5.1983350317, -9.573477, -10.2657631458, -33.081997754),
            array('SC3_GU20', 11788463.01185098849236965, 2392655.74013845855370164, 9.6630525443, -13.489015, -14.6492345712, -29.4182375737)
        );

        foreach ($boreholes as $borehole)
        {
            echo "Add geologicalPoint".$borehole[0]."\r\n";
            $geologicalPoint = GeologicalPointFactory::setOwnerNameAndPoint($user, $borehole[0], new Point($borehole[1], $borehole[2], 3857), $public);
            $entityManager->persist($geologicalPoint);

            $geologicalUnit = GeologicalUnitFactory::setOwnerNameAndPublic($user, $borehole[0].'.1', $public);
            $geologicalUnit->setTopElevation($borehole[3]);
            $geologicalUnit->setBottomElevation($borehole[4]);
            $geologicalUnit->addGeologicalLayer($layer_1);
            $geologicalPoint->addGeologicalUnit($geologicalUnit);
            $entityManager->persist($geologicalUnit);

            $geologicalUnit = GeologicalUnitFactory::setOwnerNameAndPublic($user, $borehole[0].'.2', $public);
            $geologicalUnit->setTopElevation($borehole[4]);
            $geologicalUnit->setBottomElevation($borehole[5]);
            $geologicalUnit->addGeologicalLayer($layer_2);
            $geologicalPoint->addGeologicalUnit($geologicalUnit);
            $entityManager->persist($geologicalUnit);

            $geologicalUnit = GeologicalUnitFactory::setOwnerNameAndPublic($user, $borehole[0].'.3', $public);
            $geologicalUnit->setTopElevation($borehole[5]);
            $geologicalUnit->setBottomElevation($borehole[6]);
            $geologicalUnit->addGeologicalLayer($layer_3);
            $geologicalPoint->addGeologicalUnit($geologicalUnit);
            $entityManager->persist($geologicalUnit);

            $soilModel->addGeologicalPoint($geologicalPoint);
            $entityManager->flush();
        }

        // Add properties to Layer SC3_L1
        $geologicalLayer = $layer_1;
        if (!$geologicalLayer) {
            throw new NotFoundHttpException('Layer not found');
        }

        $propertyType = $this->getPropertyType($entityManager, 'hc');
        $property = PropertyFactory::setTypeAndModelObject($propertyType, $geologicalLayer);
        $entityManager->persist($property);
        $propertyValue = PropertyValueFactory::setPropertyAndValue($property, 40);
        $entityManager->persist($propertyValue);

        $propertyType = $this->getPropertyType($entityManager, 'va');
        $property = PropertyFactory::setTypeAndModelObject($propertyType, $geologicalLayer);
        $entityManager->persist($property);
        $propertyValue = PropertyValueFactory::setPropertyAndValue($property, 8);
        $entityManager->persist($propertyValue);

        $propertyType = $this->getPropertyType($entityManager, 'ss');
        $property = PropertyFactory::setTypeAndModelObject($propertyType, $geologicalLayer);
        $entityManager->persist($property);
        $propertyValue = PropertyValueFactory::setPropertyAndValue($property, 0.00001);
        $entityManager->persist($propertyValue);

        $propertyType = $this->getPropertyType($entityManager, 'sy');
        $property = PropertyFactory::setTypeAndModelObject($propertyType, $geologicalLayer);
        $entityManager->persist($property);
        $propertyValue = PropertyValueFactory::setPropertyAndValue($property, 0.1);
        $entityManager->persist($propertyValue);

        // Add properties to Layer SC3_L2
        $geologicalLayer = $layer_2;

        if (!$geologicalLayer) {
            throw new NotFoundHttpException('Layer not found');
        }

        $propertyType = $this->getPropertyType($entityManager, 'vc');
        $property = PropertyFactory::setTypeAndModelObject($propertyType, $geologicalLayer);
        $entityManager->persist($property);
        $propertyValue = PropertyValueFactory::setPropertyAndValue($property, 1);
        $entityManager->persist($propertyValue);

        // Add properties to Layer SC3_L3
        $geologicalLayer = $layer_3;

        if (!$geologicalLayer) {
            throw new NotFoundHttpException('Layer not found');
        }

        $propertyType = $this->getPropertyType($entityManager, 'hc');
        $property = PropertyFactory::setTypeAndModelObject($propertyType, $geologicalLayer);
        $entityManager->persist($property);
        $propertyValue = PropertyValueFactory::setPropertyAndValue($property, 42);
        $entityManager->persist($propertyValue);

        $propertyType = $this->getPropertyType($entityManager, 'va');
        $property = PropertyFactory::setTypeAndModelObject($propertyType, $geologicalLayer);
        $entityManager->persist($property);
        $propertyValue = PropertyValueFactory::setPropertyAndValue($property, 21);
        $entityManager->persist($propertyValue);

        $propertyType = $this->getPropertyType($entityManager, 'ss');
        $property = PropertyFactory::setTypeAndModelObject($propertyType, $geologicalLayer);
        $entityManager->persist($property);
        $propertyValue = PropertyValueFactory::setPropertyAndValue($property, 0.00001);
        $entityManager->persist($propertyValue);

        $propertyType = $this->getPropertyType($entityManager, 'sy');
        $property = PropertyFactory::setTypeAndModelObject($propertyType, $geologicalLayer);
        $entityManager->persist($property);
        $propertyValue = PropertyValueFactory::setPropertyAndValue($property, 0.1);
        $entityManager->persist($propertyValue);

        // Add new AreaType
        $areaType = AreaTypeFactory::setName('SC3_AT1');
        $entityManager->persist($areaType);

        // Add new Area
        $area = AreaFactory::setOwnerNameTypeAndPublic($user, 'SC3_A1', $areaType, $public);
        $polygonText = "POLYGON((11792952.16265026479959488 2396619.95378328999504447, 11789249.37860263884067535 2391542.94060458615422249, 11788791.30222561210393906 2391638.37318313354626298, 11788333.22584858722984791 2391810.1518245181068778, 11787417.07309453375637531 2392230.05517012532800436, 11786558.17988761141896248 2392459.09335863823071122, 11786367.31473051756620407 2392993.51579850167036057, 11786061.93047916702926159 2393279.81353414291515946, 11785794.7192592341452837 2393718.8033954594284296, 11785508.42152359336614609 2394291.39886674145236611, 11785412.98894504643976688 2394787.64827518630772829, 11785279.38333507999777794 2395512.93587214406579733, 11785050.34514656849205494 2395932.8392177508212626, 11784897.65302089229226112 2396448.17514190496876836, 11784802.22044234536588192 2397058.94364460650831461, 11784554.0957381222397089 2397803.31775727355852723, 11784515.92270670458674431 2398394.99974426534026861, 11784477.74967528507113457 2398853.07612129114568233, 11784248.71148677170276642 2399063.02779409429058433, 11784057.84632967785000801 2399387.49856115458533168, 11783771.5485940370708704 2399921.92100101802498102, 11783427.99131126701831818 2400360.91086233453825116, 11783504.33737410604953766 2400494.51647230051457882, 11783771.5485940370708704 2400418.1704094628803432, 11784038.75981396809220314 2400303.65131520619615912, 11784382.31709673814475536 2399979.18054814636707306, 11784725.87437950819730759 2399769.22887534275650978, 11785050.34514656849205494 2399769.22887534275650978, 11785527.50803930312395096 2399960.09403243707492948, 11785985.58441632799804211 2400189.13222094997763634, 11786462.74730906449258327 2400418.1704094628803432, 11786806.30459183268249035 2400685.38162939436733723, 11787321.64051598682999611 2400914.41981790727004409, 11788161.44720720127224922 2401277.06361638614907861, 11788848.56177274137735367 2401257.97710067685693502, 11789669.28194824606180191 2401067.11194358253851533, 11790509.08863945864140987 2400971.67936503561213613, 11791158.03017357923090458 2400666.29511368507519364, 11791558.84700347669422626 2400170.04570524021983147, 11791959.66383337415754795 2399502.01765541080385447, 11792207.78853759728372097 2398833.98960558138787746, 11792474.99975752830505371 2397822.40427298285067081, 11792761.29749316908419132 2396944.42455034982413054, 11792952.16265026479959488 2396619.95378328999504447))";
        $converter = new PostgreSql();

        /** @var AbstractSpatialType $polygonType */
        $polygonType = Type::getType('polygon');

        /** @var Polygon $polygon */
        $polygon = $converter->convertStringToPHPValue($polygonType , $polygonText);
        $polygon->setSrid(3857);
        $area->setGeometry($polygon);
        $entityManager->persist($area);
        $soilModel->setArea($area);
        $modflowModel->setArea($area);
        $entityManager->flush();


        $this->addModelObjectPropertiesFromCSVFile($area, __DIR__.'/scenario_3_area_property_timeseries.csv', ';');
        // Add new boundaries
        $boundary = BoundaryFactory::setOwnerNameAndPublic($user, 'SC3_B1', $public);
        $geometryText = "LineString (11792952.16265026479959488 2396619.95378328999504447, 11792761.29749316908419132 2396944.42455034982413054, 11792474.99975752830505371 2397822.40427298285067081, 11792207.78853759728372097 2398833.98960558138787746, 11791959.66383337415754795 2399502.01765541080385447, 11791558.84700347669422626 2400170.04570524021983147, 11791158.03017357923090458 2400666.29511368507519364, 11790509.08863945864140987 2400971.67936503561213613, 11789669.28194824606180191 2401067.11194358253851533, 11788848.56177274137735367 2401257.97710067685693502, 11788161.44720720127224922 2401277.06361638614907861, 11787321.64051598682999611 2400914.41981790727004409, 11786806.30459183268249035 2400685.38162939436733723, 11786462.74730906449258327 2400418.1704094628803432, 11785985.58441632799804211 2400189.13222094997763634, 11785527.50803930312395096 2399960.09403243707492948, 11785050.34514656849205494 2399769.22887534275650978, 11784725.87437950819730759 2399769.22887534275650978, 11784382.31709673814475536 2399979.18054814636707306, 11784038.75981396809220314 2400303.65131520619615912, 11783771.5485940370708704 2400418.1704094628803432, 11783504.33737410604953766 2400494.51647230051457882, 11783427.99131126701831818 2400360.91086233453825116, 11783771.5485940370708704 2399921.92100101802498102, 11784057.84632967785000801 2399387.49856115458533168, 11784248.71148677170276642 2399063.02779409429058433, 11784477.74967528507113457 2398853.07612129114568233, 11784515.92270670458674431 2398394.99974426534026861, 11784554.0957381222397089 2397803.31775727355852723, 11784802.22044234536588192 2397058.94364460650831461, 11784897.65302089229226112 2396448.17514190496876836, 11785050.34514656849205494 2395932.8392177508212626, 11785279.38333507999777794 2395512.93587214406579733, 11785412.98894504643976688 2394787.64827518630772829, 11785508.42152359336614609 2394291.39886674145236611, 11785794.7192592341452837 2393718.8033954594284296, 11786061.93047916702926159 2393279.81353414291515946, 11786367.31473051756620407 2392993.51579850167036057, 11786558.17988761141896248 2392459.09335863823071122, 11787417.07309453375637531 2392230.05517012532800436, 11788333.22584858722984791 2391810.1518245181068778, 11788791.30222561210393906 2391638.37318313354626298)";
        $converter = new PostgreSql();

        /** @var AbstractSpatialType $lineStringType */
        $lineStringType = Type::getType('linestring');

        /** @var LineString $lineString */
        $lineString = $converter->convertStringToPHPValue($lineStringType, $geometryText);
        $lineString->setSrid(3857);
        $boundary->setGeometry($lineString);
        $modflowModel->addBoundary($boundary);
        $entityManager->persist($boundary);
        $entityManager->flush();
        $this->addModelObjectPropertiesFromCSVFile($boundary, __DIR__.'/scenario_3_boundary_1_property_timeseries.csv', ';');

        $boundary = BoundaryFactory::setOwnerNameAndPublic($user, 'SC3_B2', $public);
        $geometryText = "LineString (11792952.16265026479959488 2396619.95378328999504447, 11789249.37860263884067535 2391542.94060458615422249, 11788791.30222561210393906 2391638.37318313354626298)";
        $converter = new PostgreSql();

        /** @var AbstractSpatialType $lineStringType */
        $lineStringType = Type::getType('linestring');

        /** @var LineString $lineString */
        $lineString = $converter->convertStringToPHPValue($lineStringType, $geometryText);
        $lineString->setSrid(3857);
        $boundary->setGeometry($lineString);
        $modflowModel->addBoundary($boundary);
        $entityManager->persist($boundary);
        $entityManager->flush();
        $this->addModelObjectPropertiesFromCSVFile($boundary, __DIR__.'/scenario_3_boundary_2_property_timeseries.csv', ';');
    }


    /**
     * @param \AppBundle\Entity\ModelObject $baseElement
     * @param $filename
     * @param $delimiter
     */
    public function addModelObjectPropertiesFromCSVFile(\AppBundle\Entity\ModelObject $baseElement, $filename, $delimiter)
    {
        $data = $this->convert($filename, $delimiter);
        $elementCount = count($data[0])-1;
        $dataFields = array_keys($data[0]);
        $counter = 0;

        for ($i = 1; $i <= $elementCount; $i++)
        {
            $propertyTypeName = $dataFields[$i];

            $propertyType = $this->getPropertyType($this->entityManager, $propertyTypeName);
            $property = PropertyFactory::setTypeAndModelObject($propertyType, $baseElement);
            $this->entityManager->persist($property);
            $this->entityManager->flush();
            
            foreach ($data as $dataPoint)
            {
                $propertyTimeValue = PropertyTimeValueFactory::setPropertyDateTimeAndValue($property, new \DateTime($dataPoint[$dataFields[0]]), (float)$dataPoint[$dataFields[$i]]);
                $this->entityManager->persist($propertyTimeValue);

                /*
                $em = $this->container->get('doctrine.orm.default_entity_manager');
                $nativeQuery = 'WITH rows as(
                    INSERT INTO values
                        (property_id, name, id)
                    VALUES
                        (?, timevalue, (SELECT nextval(\'values_id_seq\'))
                    RETURNING INTO last_id
                    )
                    INSERT INTO property_time_values
                        (id, timestamp, value)
                    VALUES
                        (last_id, ?, ?)
                    --;
                ';


                $nativeQuery = 'INSERT INTO values
                                (property_id, name, id)
                                VALUES
                                (?, ?, (SELECT nextval(\'values_id_seq\')))';

                $query = $em->createNativeQuery($nativeQuery, new ResultSetMapping());
                $query->setParameter(1, $property->getId());
                $query->setParameter(2, new \DateTime($dataPoint[$dataFields[0]]));
                $query->setParameter(3, (float)$dataPoint[$dataFields[$i]]);
                $result = $query->getResult();
                */

                echo $counter++."\n";
                if ($counter % 100 == 0)
                {
                    $this->entityManager->flush();
                }
            }
        }
    }

    /**
     * @param $filename
     * @param string $delimiter
     * @return array|bool
     */
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

    /**
     * @param ObjectManager $entityManager
     * @param $propertyAbbreviation
     * @return \AppBundle\Entity\PropertyType|object
     */
    private function getPropertyType(ObjectManager $entityManager, $propertyAbbreviation)
    {
        $propertyType = $entityManager->getRepository('AppBundle:PropertyType')
            ->findOneBy(array(
                'abbreviation' => $propertyAbbreviation
            ));

        if (!$propertyType)
        {
            throw new NotFoundHttpException($propertyAbbreviation.' not found.');
        }

        return $propertyType;
    }
}