<?php

namespace AppBundle\DataFixtures\ORM\Scenarios\Scenario_2;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\User;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\AreaTypeFactory;
use AppBundle\Model\BoundaryFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\ObservationPointFactory;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyTimeValueFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\Point;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\StressPeriod;
use AppBundle\Model\StressPeriodFactory;
use AppBundle\Model\WellBoundaryFactory;
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
        $public = true;
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
        }

        // Load PropertyTypes
        $propertyTypeGwHead = $entityManager->getRepository('AppBundle:PropertyType')
            ->findOneBy(array(
                'abbreviation' => "hh"
            ));

        if (!$propertyTypeGwHead) {
            return new NotFoundHttpException();
        }

        $propertyTypeTopElevation = $entityManager->getRepository('AppBundle:PropertyType')
            ->findOneBy(array(
                'abbreviation' => "et"
            ));

        if (!$propertyTypeTopElevation) {
            return new NotFoundHttpException();
        }

        $propertyTypeBottomElevation = $entityManager->getRepository('AppBundle:PropertyType')
            ->findOneBy(array(
                'abbreviation' => "eb"
            ));

        if (!$propertyTypeBottomElevation) {
            return new NotFoundHttpException();
        }

        $propertyTypePumpingRate = $entityManager->getRepository('AppBundle:PropertyType')
            ->findOneBy(array(
                'abbreviation' => "pur"
            ));

        if (!$propertyTypePumpingRate) {
            return new NotFoundHttpException();
        }


        // Create AreaType
        $areaType = AreaTypeFactory::setName('SC2_AT1');
        $entityManager->persist($areaType);

        // Create area
        $area = AreaFactory::setOwnerNameTypeAndPublic($user, "SC2_A1", $areaType, $public);
        $entityManager->persist($area);

        $converter = new PostgreSql();
        $geometryText = "Polygon ((11777056.49104572273790836 2403440.17028302047401667, 11777973.9436037577688694 2403506.49811625294387341, 11780228.12698311358690262 2402856.2682070448063314, 11781703.59880801662802696 2401713.22520185634493828, 11782192.89715446159243584 2400859.20254275016486645, 11782678.03379831649363041 2399224.82580633740872145, 11782955.64566324092447758 2398372.03099954081699252, 11783586.59488865174353123 2397659.24991086078807712, 11784427.14815393835306168 2396590.66674219723790884, 11784914.27011025696992874 2395382.18267500726506114, 11785330.82068796083331108 2394174.15454542031511664, 11785536.96124399080872536 2393180.11378513323143125, 11786097.1273522675037384 2392467.84464810928329825, 11787011.69080197438597679 2392108.19440084183588624, 11787715.90038010291755199 2391962.42985267844051123, 11788487.82464707084000111 2391319.86146369902417064, 11789680.65233467146754265 2390320.33801258727908134, 11789747.53923093341290951 2389681.79035578016191721, 11789176.05731181986629963 2388337.88133400911465287, 11788252.26803966984152794 2386996.03587882174178958, 11787540.82363948784768581 2385794.83458124194294214, 11783036.01740818470716476 2386882.81766726961359382, 11777486.37431096099317074 2390598.53498441586270928, 11775189.21765423379838467 2396638.4036272126249969, 11777056.49104572273790836 2403440.17028302047401667))";

        /** @var AbstractSpatialType $polygonType */
        $polygonType = Type::getType('polygon');

        /** @var Polygon $polygon */
        $polygon = $converter->convertStringToPHPValue($polygonType, $geometryText);
        $polygon->setSrid(3857);
        $area->setGeometry($polygon);

        // Create a soilmodel
        $soilModel = SoilModelFactory::create();
        $soilModel
            ->setOwner($user)
            ->setName('SM Scenario 2')
            ->setArea($area);
        ;
        $entityManager->persist($soilModel);

        // Create new geological layers
        $layer_1 = GeologicalLayerFactory::setOwnerNameAndPublic($user, 'SC2_L1', $public);
        $entityManager->persist($layer_1);
        $soilModel->addGeologicalLayer($layer_1);

        $layer_2 = GeologicalLayerFactory::setOwnerNameAndPublic($user, 'SC2_L2', $public);
        $entityManager->persist($layer_2);
        $soilModel->addGeologicalLayer($layer_2);

        $layer_3 = GeologicalLayerFactory::setOwnerNameAndPublic($user, 'SC2_L3', $public);
        $entityManager->persist($layer_3);
        $soilModel->addGeologicalLayer($layer_3);

        $layer_4 = GeologicalLayerFactory::setOwnerNameAndPublic($user, 'SC2_L4', $public);
        $entityManager->persist($layer_4);
        $soilModel->addGeologicalLayer($layer_4);

        /** @var ModFlowModel $model */
        $model = ModFlowModelFactory::create();
        $entityManager->persist($model);
        $model->setName("ModFlowModel Scenario 2");
        $model->setOwner($user);
        $model->setDescription("ModFlowModel Scenario 2 Description");
        $model->setSoilModel($soilModel);
        $model->setArea($area);
        
        /** @var StressPeriod $stressPeriod */
        $stressPeriod = StressPeriodFactory::create();
        $stressPeriod->setDateTimeBegin(new \DateTime('01-01-2005'));
        $stressPeriod->setDateTimeEnd(new \DateTime('31-01-2005'));
        $model->addStressPeriod($stressPeriod);

        $stressPeriod = StressPeriodFactory::create();
        $stressPeriod->setDateTimeBegin(new \DateTime('01-02-2005'));
        $stressPeriod->setDateTimeEnd(new \DateTime('28-02-2005'));
        $model->addStressPeriod($stressPeriod);

        $stressPeriod = StressPeriodFactory::create();
        $stressPeriod->setDateTimeBegin(new \DateTime('01-03-2005'));
        $stressPeriod->setDateTimeEnd(new \DateTime('31-03-2005'));
        $model->addStressPeriod($stressPeriod);

        $properties = $model->getCalculationProperties();
        $properties['grid_size'] = array(
            'rows' => 50,
            'cols' => 50
        );
        $model->setCalculationProperties($properties);
        $entityManager->flush();

        /**
         * boreholes
         * format csv
         * values: name, x, z, top elevation, elevation layer 1 bottom, elevation layer 2 bottom, elevation layer 3 bottom, elevation layer 4 bottom
         */
        $boreholes = array(
            array('SC2_GU1', 11771882.34, 2392544.12, 4.55, 3, -37.44, -38.45, -71.95),
            array('SC2_GU2', 11789082.18, 2389714.82, 8.15, -1, -19.84, -20.85, -107.85),
            array('SC2_GU3', 11778857.62, 2391711.98, 6.97, 1, -18.03, -31.03, -65.03),
            array('SC2_GU4', 11784193.77, 2394196.31, 9.26, -1.75, -11.75, -13.75, -66.25),
            array('SC2_GU5', 11781568.57, 2392545.18, 5.45, 0.45, -24.55, -38.05, -75.55),
            array('SC2_GU6', 11777013.85, 2400404.42, 9.25, -1.4, -20.75, -26.75, -68.75),
            array('SC2_GU7', 11783051.08, 2395101.75, 3.21, -11.79, -34.59, -38.29, -90.79),
            array('SC2_GU8', 11777309.40, 2390254.19, 6.41, 1, -39.58, -40.59, -73.59),
            array('SC2_GU9', 11784512.97, 2393046.65, 8.34, -0.66, -25.65, -26.66, -66.66),
            array('SC2_GU10', 11778452.98, 2390393.68, 6.3, 2, -7.7, -32.2, -71.7),
            array('SC2_GU11', 11778745.08, 2399607.08, 7.48, -1, -13.51, -14.52, -84.02),
            array('SC2_GU12', 11778807.54, 2396471.02, 6.78, -3.73, -30.72, -31.73, -65.23),
            array('SC2_GU13', 11772850.45, 2386662.03, 5.05, 1, -9.95, -29.95, -59.95),
            array('SC2_GU14', 11781833.25, 2394756.26, 6.87, 2, -7.33, -13.13, -71.13),
            array('SC2_GU15', 11785022.12, 2395765.18, 11.47, -1, -8.92, -9.93, -113.13),
            array('SC2_GU16', 11775146.75, 2398847.69, 5.86, 0.2, -5.64, -24.14, -48.14),
            array('SC2_GU17', 11781244.76, 2397032.49, 6.88, 2, -25.21, -26.22, -81.32),
            array('SC2_GU18', 11777209.26, 2402770.9, 8.52, 0.7, -17.47, -18.48, -81.48),
            array('SC2_GU19', 11783628.45, 2390521.59, 5.51, -7.09, -24.99, -37.99, -79.99),
            array('SC2_GU20', 11787952.59, 2391352.68, 10.23, -1, -3.17, -13.77, -60.27),
            array('SC2_GU21', 11772535.98, 2391516.61, 5.06, -1.44, -21.44, -34.64, -88.54),
            array('SC2_GU22', 11779155.03, 2396640.9, 7.71, -1.5, -24.29, -27.79, -47.29),
            array('SC2_GU23', 11760714.53, 2397939.64, 6.03, 1, -18.47, -32.47, -66.07),
            array('SC2_GU24', 11774649.17, 2399215.18, 6.51, 3, -25.5, -30.5, -49.5),
            array('SC2_GU25', 11782792.24, 2384025.09, 5.26, 1.76, -21.34, -33.64, -59.24),
            array('SC2_GU26', 11780072.96, 2396064.94, 6.41, -3.19, -24.09, -31.09, -49.09),
            array('SC2_GU27', 11777813.99, 2386822.58, 4, 0.2, -19.9, -33.2, -59.2),
            array('SC2_GU28', 11786910.46, 2387406.18, 7.51, 2, -15.99, -32.29, -56.49),
            array('SC2_GU29', 11788382.99, 2388557.67, 8.46, 0.86, -37.54, -41.74, -62.74),
            array('SC2_GU30', 11781544.58, 2399809.73, 9.92, 1, -19.09, -24.09, -55.99),
            array('SC2_GU31', 11779912.77, 2401723.79, 10.27, 0.5, -11.52, -12.53, -55.03),
            array('SC2_GU32', 11778716.08, 2402222.88, 7.98, 0.2, -1, -7.02, -58.02),
            array('SC2_GU33', 11782681.56, 2398443.64, 12.6, -4.4, -13.9, -16.4, -55.2),
            array('SC2_GU34', 11782711.76, 2383219.36, 5.72, 1, -14.28, -35.58, -92.28),
            array('SC2_GU35', 11782877.61, 2387087.35, 4.855, -10.15, -18.15, -33.15, -59.15),
            array('SC2_GU36', 11780837.05, 2392172.81, 5.81, 0.5, -6.19, -37.19, -49.09),
            array('SC2_GU37', 11775298.68, 2396584.49, 6.92, 0.5, 0.92, -12.58, -53.08),
            array('SC2_GU38', 11771588.05, 2400278.11, 7.238, -8.76, -17.76, -26.76, -50.96),
            array('SC2_GU39', 11786863.07, 2387774.93, 7.86, -4.14, -15.64, -31.14, -58.54),
            array('SC2_GU40', 11785494.76, 2387728.64, 5.58, -2, -23.42, -36.22, -52.92),
            array('SC2_GU41', 11785359.45, 2388446.5, 5.24, -1, -24.76, -29.76, -56.76),
            array('SC2_GU42', 11783834.59, 2393475.89, 6.67, 0.47, -7.83, -25.33, -66.83),
            array('SC2_GU43', 11778042.14, 2393748.39, 6.72, -0.58, -24.77, -25.78, -66.28),
            array('SC2_GU44', 11774767.25, 2397966.02, 7.18, -0.5, -18.32, -19.32, -58.82),
            array('SC2_GU45', 11778459.29, 2399719.84, 6.87, -2, -23.72, -24.73, -72.13),
            array('SC2_GU46', 11779001.31, 2392931.32, 6.42, -2, -22.57, -23.58, -72.48),
            array('SC2_GU47', 11787253.39, 2398790.31, 7.32, -11.18, -26.37, -27.38, -72.68),
            array('SC2_GU48', 11779321.83, 2394682.34, 6.25, -1, -25.75, -27.55, -55.75),
            array('SC2_GU49', 11788397.99, 2396629.41, 4.6, -5.1, -16.69, -17.7, -77.7),
            array('SC2_GU50', 11776362.25, 2398212.2, 6.97, 0.47, -20.52, -21.53, -49.03),
            array('SC2_GU51', 11780153.2, 2399710.99, 6.44, -1.7, -16.55, -17.56, -68.56),
            array('SC2_GU52', 11775049.53, 2401787.45, 6.88, -1.4, -22.12, -26.52, -78.72),
            array('SC2_GU53', 11773006.52, 2397389.94, 5.98, 2, -32.01, -33.02, -63.02),
            array('SC2_GU54', 11775636.91, 2391945.87, 4.61, 1.01, -18.39, -22.89, -80.99),
            array('SC2_GU55', 11782808.67, 2397713.81, 10.49, 3, -24.11, -30.01, -75.51),
            array('SC2_GU56', 11782239.75, 2397303.54, 13.11, 2, -17.88, -18.89, -69.19),
            array('SC2_GU57', 11778341.11, 2386909.75, 5.35, -2.65, -39.65, -44.65, -70.85),
            array('SC2_GU58', 11777301.77, 2396625.85, 6.62, 0.3, -19.38, -20.38, -65.88),
            array('SC2_GU59', 11778384.57, 2397052.79, 7, 2, -29.99, -31, -68.6),
            array('SC2_GU60', 11781117.72, 2394046.04, 7.19, -9.41, -28.3, -29.31, -69.31),
            array('SC2_GU61', 11781602.56, 2395825.27, 7.82, 3, -25.17, -26.18, -56.18),
            array('SC2_GU62', 11784169.97, 2395592.91, 9.66, 2, -11.34, -27.34, -102.34),
            array('SC2_GU63', 11781035.36, 2397295.63, 7.09, -3, -19.9, -20.91, -85.11),
            array('SC2_GU64', 11782599.06, 2394228.74, 6.05, 3, -2.95, -25.45, -80.95),
            array('SC2_GU65', 11784219.85, 2393183.29, 5.25, 2, -1.75, -29.75, -72.75),
            array('SC2_GU66', 11784938.17, 2393227.68, 10.54, -1.2, -9.45, -10.46, -80.46),
            array('SC2_GU67', 11782485.51, 2392841.91, 5.52, 3, -14.48, -31.48, -79.48),
            array('SC2_GU68', 11783589.92, 2392750.53, 5.59, 1.09, -24.41, -25.41, -72.41),
            array('SC2_GU69', 11777852.62, 2384147.89, 5.79, 3, -31.71, -37.21, -62.21),
            array('SC2_GU70', 11778108.29, 2391057.06, 5.63, 0.13, -29.37, -36.37, -68.37),
            array('SC2_GU71', 11783363.26, 2390073.25, 5.5, 0.5, -22.5, -48, -79.5),
            array('SC2_GU72', 11778272.54, 2397633.23, 7.48, -1, -14.51, -15.52, -64.22),
            array('SC2_GU73', 11771647.38, 2392411.6, 5.45, -4.55, -26.55, -30.55, -78.55),
            array('SC2_GU74', 11776094.56, 2389455.36, 5.7, 0.5, -11.3, -33.3, -76.8),
            array('SC2_GU75', 11788517.19, 2390860.62, 9.1, -2, -13.89, -14.9, -61.4),
            array('SC2_GU76', 11777147.1, 2402553.12, 7.57, -0.43, -21.42, -22.43, -81.43),
            array('SC2_GU77', 11786277.06, 2394518.28, 10.4, -0.5, -9.1, -20.1, -104.6),
            array('SC2_GU78', 11785882.41, 2389731.92, 5.83, -1, -17.67, -33.17, -86.97),
            array('SC2_GU79', 11775388.22, 2394326.9, 5.09, -6.91, -28.9, -29.91, -60.91),
            array('SC2_GU80', 11783396.36, 2390930.73, 5.91, 3, -24.59, -37.59, -79.59),
            array('SC2_GU81', 11783318.42, 2379920.67, 4.33, -0.67, -33.66, -34.67, -86.67),
            array('SC2_GU82', 11770462, 2403116.55, 9.89, -2.5, -12.1, -13.11, -65.11),
            array('SC2_GU83', 11783103.2, 2397142.16, 11.68, 3, -21.32, -23.82, -84.32),
            array('SC2_GU84', 11776546.92, 2391893.9, 6.42, 2.62, -33.58, -34.58, -66.78),
            array('SC2_GU85', 11780517.15, 2385713.39, 5.61, -1.89, -20.69, -39.39, -60.39),
            array('SC2_GU86', 11782769.78, 2387640.47, 5.23, 2, -24.77, -37.17, -59.77),
            array('SC2_GU87', 11776760.16, 2404465.83, 9.98, -2, -20.02, -29.02, -76.32),
            array('SC2_GU88', 11766470.1, 2391498.39, 7.716, -3.78, -29.77, -30.78, -60.58),
            array('SC2_GU89', 11775192.52, 2388842.32, 4.719, -23.28, -36.27, -37.28, -76.28),
            array('SC2_GU90', 11772988.05, 2386432.76, 5.43, -9.57, -38.06, -39.07, -65.97)
        );

        foreach ($boreholes as $borehole)
        {
            echo "Persisting ".$borehole[0]."\r\n";
            $geologicalPoint = GeologicalPointFactory::create()
                ->setOwner($user)
                ->setName($borehole[0])
                ->setPoint(new Point($borehole[1], $borehole[2], 3857))
                ->setPublic($public);
            $entityManager->persist($geologicalPoint);

            $geologicalUnit = GeologicalUnitFactory::create()
                ->setOwner($user)
                ->setName($borehole[0].'.1')
                ->setPublic($public)
                ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue($borehole[3]))
                ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue($borehole[4]));

            $geologicalPoint->addGeologicalUnit($geologicalUnit);
            $layer_1->addGeologicalUnit($geologicalUnit);
            $entityManager->persist($layer_1);

            $geologicalUnit = GeologicalUnitFactory::create()
                ->setOwner($user)
                ->setName($borehole[0].'.2')
                ->setPublic($public)
                ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue($borehole[4]))
                ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue($borehole[5]));

            $geologicalPoint->addGeologicalUnit($geologicalUnit);
            $layer_2->addGeologicalUnit($geologicalUnit);
            $entityManager->persist($layer_2);

            $geologicalUnit = GeologicalUnitFactory::create()
                ->setOwner($user)
                ->setName($borehole[0].'.3')
                ->setPublic($public)
                ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue($borehole[5]))
                ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue($borehole[6]));

            $geologicalPoint->addGeologicalUnit($geologicalUnit);
            $layer_3->addGeologicalUnit($geologicalUnit);
            $entityManager->persist($layer_3);

            $geologicalUnit = GeologicalUnitFactory::create()
                ->setOwner($user)
                ->setName($borehole[0].'.4')
                ->setPublic($public)
                ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue($borehole[6]))
                ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue($borehole[7]));

            $geologicalPoint->addGeologicalUnit($geologicalUnit);
            $layer_4->addGeologicalUnit($geologicalUnit);
            $entityManager->persist($layer_4);
            $soilModel->addGeologicalPoint($geologicalPoint);
            $entityManager->flush();
        }

        // Add properties to Geological Layers
        /**
         * geologicalLayers-properties
         * format array csv
         * values: layer_name, property_Name, property_type_abbreviation, value
         */
        $geologicalLayerProperties = array(
            array('SC2_L1','Layer_1_Top_Elevation','et',10),
            array('SC2_L1','Layer_1_Bottom_Elevation','eb',5),
            array('SC2_L1','Layer_1_Hydraulic_Conductivity','hc',10),
            array('SC2_L1','Layer_1_Horizontal_Anisotropy','ha',10),
            array('SC2_L1','Layer_1_Vertical_Anisotropy','va',1),
            array('SC2_L2','Layer_2_Top_Elevation','et',5),
            array('SC2_L2','Layer_2_Bottom_Elevation','eb',-15),
            array('SC2_L2','Layer_2_Hydraulic_Conductivity','hc',7),
            array('SC2_L2','Layer_2_Horizontal_Anisotropy','ha',7),
            array('SC2_L2','Layer_2_Vertical_Anisotropy','va',0.7),
            array('SC2_L3','Layer_3_Top_Elevation','et',-15),
            array('SC2_L3','Layer_3_Bottom_Elevation','eb',-30),
            array('SC2_L3','Layer_3_Hydraulic_Conductivity','hc',0.001),
            array('SC2_L3','Layer_3_Horizontal_Anisotropy','ha',0.001),
            array('SC2_L3','Layer_3_Vertical_Anisotropy','va',0.0001),
            array('SC2_L4','Layer_4_Top_Elevation','et',-30),
            array('SC2_L4','Layer_4_Bottom_Elevation','eb',-33),
            array('SC2_L4','Layer_4_Hydraulic_Conductivity','hc',50),
            array('SC2_L4','Layer_4_Horizontal_Anisotropy','ha',50),
            array('SC2_L4','Layer_4_Vertical_Anisotropy','va',5),
        );

        foreach ($geologicalLayerProperties as $geologicalLayerProperty)
        {
            echo "Persisting Property ".$geologicalLayerProperty[1]."\r\n";

            $geologicalLayer = $entityManager->getRepository('AppBundle:GeologicalLayer')
                ->findOneBy(array(
                    'name' => $geologicalLayerProperty[0]
                ));

            $property = PropertyFactory::create();
            $property->setName($geologicalLayerProperty[1]);
            $propertyType = $entityManager->getRepository('AppBundle:PropertyType')
                ->findOneBy(array(
                    'abbreviation' => $geologicalLayerProperty[2]
                ));

            if (!$propertyType) {
                throw new NotFoundHttpException();
            }
            $property->setPropertyType($propertyType);
            $value = PropertyValueFactory::create()->setValue($geologicalLayerProperty[3]);
            $property->addValue($value);
            $geologicalLayer->addProperty($property);

            $entityManager->persist($value);
            $entityManager->persist($property);
            $entityManager->persist($geologicalLayer);
            $entityManager->flush();
        }

        // Add properties to Geological Units
        /**
         * geologicalunit-properties
         * format array csv
         * values: name, Hydraulic conductivity, Horizontal anisotropy, Vertical anisotropy
         */
        $geologicalUnitProperties = array(
            array('SC2_GU1.1',10,10,1),
            array('SC2_GU2.1',10,10,1),
            array('SC2_GU3.1',10,10,1),
            array('SC2_GU4.1',10,10,1),
            array('SC2_GU5.1',10,10,1),
            array('SC2_GU6.1',10,10,1),
            array('SC2_GU7.1',10,10,1),
            array('SC2_GU8.1',10,10,1),
            array('SC2_GU9.1',10,10,1),
            array('SC2_GU10.1',10,10,1),
            array('SC2_GU11.1',10,10,1),
            array('SC2_GU12.1',10,10,1),
            array('SC2_GU13.1',10,10,1),
            array('SC2_GU14.1',10,10,1),
            array('SC2_GU15.1',10,10,1),
            array('SC2_GU16.1',10,10,1),
            array('SC2_GU17.1',10,10,1),
            array('SC2_GU18.1',10,10,1),
            array('SC2_GU19.1',10,10,1),
            array('SC2_GU20.1',10,10,1),
            array('SC2_GU21.1',10,10,1),
            array('SC2_GU22.1',10,10,1),
            array('SC2_GU23.1',30,30,3),
            array('SC2_GU24.1',10,10,1),
            array('SC2_GU25.1',10,10,1),
            array('SC2_GU26.1',10,10,1),
            array('SC2_GU27.1',10,10,1),
            array('SC2_GU28.1',10,10,1),
            array('SC2_GU29.1',10,10,1),
            array('SC2_GU30.1',10,10,1),
            array('SC2_GU31.1',10,10,1),
            array('SC2_GU32.1',10,10,1),
            array('SC2_GU33.1',10,10,1),
            array('SC2_GU34.1',10,10,1),
            array('SC2_GU35.1',10,10,1),
            array('SC2_GU36.1',10,10,1),
            array('SC2_GU37.1',10,10,1),
            array('SC2_GU38.1',10,10,1),
            array('SC2_GU39.1',10,10,1),
            array('SC2_GU40.1',10,10,1),
            array('SC2_GU41.1',10,10,1),
            array('SC2_GU42.1',10,10,1),
            array('SC2_GU43.1',10,10,1),
            array('SC2_GU44.1',10,10,1),
            array('SC2_GU45.1',10,10,1),
            array('SC2_GU46.1',10,10,1),
            array('SC2_GU47.1',10,10,1),
            array('SC2_GU48.1',10,10,1),
            array('SC2_GU49.1',10,10,1),
            array('SC2_GU50.1',10,10,1),
            array('SC2_GU51.1',10,10,1),
            array('SC2_GU52.1',10,10,1),
            array('SC2_GU53.1',10,10,1),
            array('SC2_GU54.1',10,10,1),
            array('SC2_GU55.1',10,10,1),
            array('SC2_GU56.1',10,10,1),
            array('SC2_GU57.1',10,10,1),
            array('SC2_GU58.1',10,10,1),
            array('SC2_GU59.1',10,10,1),
            array('SC2_GU60.1',10,10,1),
            array('SC2_GU61.1',10,10,1),
            array('SC2_GU62.1',10,10,1),
            array('SC2_GU63.1',10,10,1),
            array('SC2_GU64.1',10,10,1),
            array('SC2_GU65.1',10,10,1),
            array('SC2_GU66.1',10,10,1),
            array('SC2_GU67.1',10,10,1),
            array('SC2_GU68.1',10,10,1),
            array('SC2_GU69.1',10,10,1),
            array('SC2_GU70.1',10,10,1),
            array('SC2_GU71.1',10,10,1),
            array('SC2_GU72.1',10,10,1),
            array('SC2_GU73.1',10,10,1),
            array('SC2_GU74.1',10,10,1),
            array('SC2_GU75.1',10,10,1),
            array('SC2_GU76.1',10,10,1),
            array('SC2_GU77.1',10,10,1),
            array('SC2_GU78.1',10,10,1),
            array('SC2_GU79.1',10,10,1),
            array('SC2_GU80.1',10,10,1),
            array('SC2_GU81.1',10,10,1),
            array('SC2_GU82.1',10,10,1),
            array('SC2_GU83.1',10,10,1),
            array('SC2_GU84.1',10,10,1),
            array('SC2_GU85.1',10,10,1),
            array('SC2_GU86.1',10,10,1),
            array('SC2_GU87.1',20,20,2),
            array('SC2_GU88.1',30,30,3),
            array('SC2_GU89.1',10,10,1),
            array('SC2_GU90.1',10,10,1),
            array('SC2_GU1.2',20,20,2),
            array('SC2_GU2.2',20,20,2),
            array('SC2_GU3.2',7,7,0.7),
            array('SC2_GU4.2',0.2,0.2,0.02),
            array('SC2_GU5.2',0.2,0.2,0.02),
            array('SC2_GU6.2',20,20,2),
            array('SC2_GU7.2',0.2,0.2,0.02),
            array('SC2_GU8.2',7,7,0.7),
            array('SC2_GU9.2',0.2,0.2,0.02),
            array('SC2_GU10.2',7,7,0.7),
            array('SC2_GU11.2',20,20,2),
            array('SC2_GU12.2',7,7,0.7),
            array('SC2_GU13.2',20,20,2),
            array('SC2_GU14.2',0.2,0.2,0.02),
            array('SC2_GU15.2',40,40,4),
            array('SC2_GU16.2',7,7,0.7),
            array('SC2_GU17.2',20,20,2),
            array('SC2_GU18.2',20,20,2),
            array('SC2_GU19.2',0.2,0.2,0.02),
            array('SC2_GU20.2',20,20,2),
            array('SC2_GU21.2',20,20,2),
            array('SC2_GU22.2',7,7,0.7),
            array('SC2_GU23.2',40,40,4),
            array('SC2_GU24.2',20,20,2),
            array('SC2_GU25.2',20,20,2),
            array('SC2_GU26.2',7,7,0.7),
            array('SC2_GU27.2',7,7,0.7),
            array('SC2_GU28.2',20,20,2),
            array('SC2_GU29.2',20,20,2),
            array('SC2_GU30.2',20,20,2),
            array('SC2_GU31.2',15,15,1.5),
            array('SC2_GU32.2',15,15,1.5),
            array('SC2_GU33.2',20,20,2),
            array('SC2_GU34.2',20,20,2),
            array('SC2_GU35.2',7,7,0.7),
            array('SC2_GU36.2',0.2,0.2,0.02),
            array('SC2_GU37.2',7,7,0.7),
            array('SC2_GU38.2',20,20,2),
            array('SC2_GU39.2',20,20,2),
            array('SC2_GU40.2',20,20,2),
            array('SC2_GU41.2',20,20,2),
            array('SC2_GU42.2',0.2,0.2,0.02),
            array('SC2_GU43.2',7,7,0.7),
            array('SC2_GU44.2',7,7,0.7),
            array('SC2_GU45.2',20,20,2),
            array('SC2_GU46.2',7,7,0.7),
            array('SC2_GU47.2',40,40,4),
            array('SC2_GU48.2',7,7,0.7),
            array('SC2_GU49.2',40,40,4),
            array('SC2_GU50.2',7,7,0.7),
            array('SC2_GU51.2',20,20,2),
            array('SC2_GU52.2',20,20,2),
            array('SC2_GU53.2',7,7,0.7),
            array('SC2_GU54.2',7,7,0.7),
            array('SC2_GU55.2',20,20,2),
            array('SC2_GU56.2',20,20,2),
            array('SC2_GU57.2',7,7,0.7),
            array('SC2_GU58.2',7,7,0.7),
            array('SC2_GU59.2',7,7,0.7),
            array('SC2_GU60.2',0.2,0.2,0.02),
            array('SC2_GU61.2',20,20,2),
            array('SC2_GU62.2',20,20,2),
            array('SC2_GU63.2',20,20,2),
            array('SC2_GU64.2',0.2,0.2,0.02),
            array('SC2_GU65.2',0.2,0.2,0.02),
            array('SC2_GU66.2',20,20,2),
            array('SC2_GU67.2',0.2,0.2,0.02),
            array('SC2_GU68.2',0.2,0.2,0.02),
            array('SC2_GU69.2',10,10,1),
            array('SC2_GU70.2',7,7,0.7),
            array('SC2_GU71.2',20,20,2),
            array('SC2_GU72.2',7,7,0.7),
            array('SC2_GU73.2',20,20,2),
            array('SC2_GU74.2',7,7,0.7),
            array('SC2_GU75.2',20,20,2),
            array('SC2_GU76.2',20,20,2),
            array('SC2_GU77.2',40,40,4),
            array('SC2_GU78.2',20,20,2),
            array('SC2_GU79.2',7,7,0.7),
            array('SC2_GU80.2',0.2,0.2,0.02),
            array('SC2_GU81.2',20,20,2),
            array('SC2_GU82.2',20,20,2),
            array('SC2_GU83.2',20,20,2),
            array('SC2_GU84.2',7,7,0.7),
            array('SC2_GU85.2',7,7,0.7),
            array('SC2_GU86.2',7,7,0.7),
            array('SC2_GU87.2',15,15,1.5),
            array('SC2_GU88.2',40,40,4),
            array('SC2_GU89.2',20,20,2),
            array('SC2_GU90.2',20,20,2),
            array('SC2_GU1.3',0.001,0.001,0.0001),
            array('SC2_GU2.3',0.1,0.1,0.01),
            array('SC2_GU3.3',0.001,0.001,0.0001),
            array('SC2_GU4.3',0.001,0.001,0.0001),
            array('SC2_GU5.3',0.0001,0.0001,1.00E-05),
            array('SC2_GU6.3',0.1,0.1,0.01),
            array('SC2_GU7.3',0.1,0.1,0.01),
            array('SC2_GU8.3',0.001,0.001,0.0001),
            array('SC2_GU9.3',0.1,0.1,0.01),
            array('SC2_GU10.3',0.001,0.001,0.0001),
            array('SC2_GU11.3',0.01,0.01,0.001),
            array('SC2_GU12.3',0.001,0.001,0.0001),
            array('SC2_GU13.3',0.001,0.001,0.0001),
            array('SC2_GU14.3',0.001,0.001,0.0001),
            array('SC2_GU15.3',0.2,0.2,0.02),
            array('SC2_GU16.3',0.001,0.001,0.0001),
            array('SC2_GU17.3',0.1,0.1,0.01),
            array('SC2_GU18.3',0.1,0.1,0.01),
            array('SC2_GU19.3',0.1,0.1,0.01),
            array('SC2_GU20.3',0.1,0.1,0.01),
            array('SC2_GU21.3',0.001,0.001,0.0001),
            array('SC2_GU22.3',0.001,0.001,0.0001),
            array('SC2_GU23.3',0.0001,0.0001,1.00E-05),
            array('SC2_GU24.3',0.001,0.001,0.0001),
            array('SC2_GU25.3',1.00E-08,1.00E-08,1.00E-09),
            array('SC2_GU26.3',0.001,0.001,0.0001),
            array('SC2_GU27.3',0.001,0.001,0.0001),
            array('SC2_GU28.3',0.1,0.1,0.01),
            array('SC2_GU29.3',0.1,0.1,0.01),
            array('SC2_GU30.3',0.1,0.1,0.01),
            array('SC2_GU31.3',0.1,0.1,0.01),
            array('SC2_GU32.3',0.1,0.1,0.01),
            array('SC2_GU33.3',0.1,0.1,0.01),
            array('SC2_GU34.3',0.1,0.1,0.01),
            array('SC2_GU35.3',1.00E-08,1.00E-08,1.00E-09),
            array('SC2_GU36.3',0.0001,0.0001,1.00E-05),
            array('SC2_GU37.3',0.001,0.001,0.0001),
            array('SC2_GU38.3',0.1,0.1,0.01),
            array('SC2_GU39.3',0.1,0.1,0.01),
            array('SC2_GU40.3',0.1,0.1,0.01),
            array('SC2_GU41.3',0.1,0.1,0.01),
            array('SC2_GU42.3',0.001,0.001,0.0001),
            array('SC2_GU43.3',0.001,0.001,0.0001),
            array('SC2_GU44.3',0.001,0.001,0.0001),
            array('SC2_GU45.3',0.01,0.01,0.001),
            array('SC2_GU46.3',0.001,0.001,0.0001),
            array('SC2_GU47.3',0.2,0.2,0.02),
            array('SC2_GU48.3',0.001,0.001,0.0001),
            array('SC2_GU49.3',0.2,0.2,0.02),
            array('SC2_GU50.3',0.001,0.001,0.0001),
            array('SC2_GU51.3',0.1,0.1,0.01),
            array('SC2_GU52.3',0.1,0.1,0.01),
            array('SC2_GU53.3',0.001,0.001,0.0001),
            array('SC2_GU54.3',0.01,0.01,0.001),
            array('SC2_GU55.3',0.1,0.1,0.01),
            array('SC2_GU56.3',0.1,0.1,0.01),
            array('SC2_GU57.3',0.001,0.001,0.0001),
            array('SC2_GU58.3',0.01,0.01,0.001),
            array('SC2_GU59.3',0.001,0.001,0.0001),
            array('SC2_GU60.3',0.001,0.001,0.0001),
            array('SC2_GU61.3',0.1,0.1,0.01),
            array('SC2_GU62.3',0.1,0.1,0.01),
            array('SC2_GU63.3',0.1,0.1,0.01),
            array('SC2_GU64.3',0.001,0.001,0.0001),
            array('SC2_GU65.3',0.1,0.1,0.01),
            array('SC2_GU66.3',0.1,0.1,0.01),
            array('SC2_GU67.3',0.001,0.001,0.0001),
            array('SC2_GU68.3',0.001,0.001,0.0001),
            array('SC2_GU69.3',0.1,0.1,0.01),
            array('SC2_GU70.3',0.001,0.001,0.0001),
            array('SC2_GU71.3',0.1,0.1,0.01),
            array('SC2_GU72.3',0.001,0.001,0.0001),
            array('SC2_GU73.3',0.001,0.001,0.0001),
            array('SC2_GU74.3',0.001,0.001,0.0001),
            array('SC2_GU75.3',0.1,0.1,0.01),
            array('SC2_GU76.3',0.1,0.1,0.01),
            array('SC2_GU77.3',0.2,0.2,0.02),
            array('SC2_GU78.3',0.1,0.1,0.01),
            array('SC2_GU79.3',0.001,0.001,0.0001),
            array('SC2_GU80.3',0.1,0.1,0.01),
            array('SC2_GU81.3',0.1,0.1,0.01),
            array('SC2_GU82.3',0.1,0.1,0.01),
            array('SC2_GU83.3',0.1,0.1,0.01),
            array('SC2_GU84.3',0.001,0.001,0.0001),
            array('SC2_GU85.3',1.00E-08,1.00E-08,1.00E-09),
            array('SC2_GU86.3',1.00E-08,1.00E-08,1.00E-09),
            array('SC2_GU87.3',0.1,0.1,0.01),
            array('SC2_GU88.3',0.1,0.1,0.01),
            array('SC2_GU89.3',0.001,0.001,0.0001),
            array('SC2_GU90.3',0.001,0.001,0.0001),
            array('SC2_GU1.4',40,40,4),
            array('SC2_GU2.4',40,40,4),
            array('SC2_GU3.4',10,10,1),
            array('SC2_GU4.4',40,40,4),
            array('SC2_GU5.4',40,40,4),
            array('SC2_GU6.4',40,40,4),
            array('SC2_GU7.4',40,40,4),
            array('SC2_GU8.4',7,7,0.7),
            array('SC2_GU9.4',40,40,4),
            array('SC2_GU10.4',7,7,0.7),
            array('SC2_GU11.4',50,50,5),
            array('SC2_GU12.4',10,10,1),
            array('SC2_GU13.4',40,40,4),
            array('SC2_GU14.4',70,70,7),
            array('SC2_GU15.4',40,40,4),
            array('SC2_GU16.4',10,10,1),
            array('SC2_GU17.4',50,50,5),
            array('SC2_GU18.4',50,50,5),
            array('SC2_GU19.4',40,40,4),
            array('SC2_GU20.4',40,40,4),
            array('SC2_GU21.4',40,40,4),
            array('SC2_GU22.4',40,40,4),
            array('SC2_GU23.4',10,10,1),
            array('SC2_GU24.4',10,10,1),
            array('SC2_GU25.4',40,40,4),
            array('SC2_GU26.4',40,40,4),
            array('SC2_GU27.4',40,40,4),
            array('SC2_GU28.4',40,40,4),
            array('SC2_GU29.4',40,40,4),
            array('SC2_GU30.4',50,50,5),
            array('SC2_GU31.4',50,50,5),
            array('SC2_GU32.4',50,50,5),
            array('SC2_GU33.4',50,50,5),
            array('SC2_GU34.4',40,40,4),
            array('SC2_GU35.4',20,20,2),
            array('SC2_GU36.4',40,40,4),
            array('SC2_GU37.4',10,10,1),
            array('SC2_GU38.4',40,40,4),
            array('SC2_GU39.4',40,40,4),
            array('SC2_GU40.4',20,20,2),
            array('SC2_GU41.4',20,20,2),
            array('SC2_GU42.4',40,40,4),
            array('SC2_GU43.4',10,10,1),
            array('SC2_GU44.4',10,10,1),
            array('SC2_GU45.4',10,10,1),
            array('SC2_GU46.4',10,10,1),
            array('SC2_GU47.4',40,40,4),
            array('SC2_GU48.4',10,10,1),
            array('SC2_GU49.4',40,40,4),
            array('SC2_GU50.4',10,10,1),
            array('SC2_GU51.4',50,50,5),
            array('SC2_GU52.4',50,50,5),
            array('SC2_GU53.4',40,40,4),
            array('SC2_GU54.4',10,10,1),
            array('SC2_GU55.4',50,50,5),
            array('SC2_GU56.4',50,50,5),
            array('SC2_GU57.4',40,40,4),
            array('SC2_GU58.4',50,50,5),
            array('SC2_GU59.4',10,10,1),
            array('SC2_GU60.4',70,70,7),
            array('SC2_GU61.4',70,70,7),
            array('SC2_GU62.4',40,40,4),
            array('SC2_GU63.4',50,50,5),
            array('SC2_GU64.4',70,70,7),
            array('SC2_GU65.4',40,40,4),
            array('SC2_GU66.4',40,40,4),
            array('SC2_GU67.4',40,40,4),
            array('SC2_GU68.4',50,50,5),
            array('SC2_GU69.4',30,30,3),
            array('SC2_GU70.4',7,7,0.7),
            array('SC2_GU71.4',40,40,4),
            array('SC2_GU72.4',10,10,1),
            array('SC2_GU73.4',40,40,4),
            array('SC2_GU74.4',40,40,4),
            array('SC2_GU75.4',40,40,4),
            array('SC2_GU76.4',50,50,5),
            array('SC2_GU77.4',40,40,4),
            array('SC2_GU78.4',20,20,2),
            array('SC2_GU79.4',10,10,1),
            array('SC2_GU80.4',40,40,4),
            array('SC2_GU81.4',40,40,4),
            array('SC2_GU82.4',50,50,5),
            array('SC2_GU83.4',50,50,5),
            array('SC2_GU84.4',10,10,1),
            array('SC2_GU85.4',20,20,2),
            array('SC2_GU86.4',20,20,2),
            array('SC2_GU87.4',50,50,5),
            array('SC2_GU88.4',10,10,1),
            array('SC2_GU89.4',40,40,4),
            array('SC2_GU90.4',40,40,4)
        );

        foreach ($geologicalUnitProperties as $geologicalUnitProperty)
        {
            $geologicalUnit = $this->entityManager
                ->getRepository('AppBundle:GeologicalUnit')
                ->findOneBy(array(
                    'name' => $geologicalUnitProperty[0]
                ));

            if ($geologicalUnit)
            {
                echo 'Add properties to '.$geologicalUnit->getName()."\n";

                $propertyType = $this->getPropertyType($this->entityManager, 'hc');
                $property = PropertyFactory::create()->setPropertyType($propertyType);
                $propertyValue = PropertyValueFactory::create()->setValue($geologicalUnitProperty[1]);
                $property->setName('Hydraulic conductivity'.' '.$geologicalUnit->getName());
                $property->addValue($propertyValue);
                $geologicalUnit->addProperty($property);
                $this->entityManager->persist($property);

                $propertyType = $this->getPropertyType($this->entityManager, 'ha');
                $property = PropertyFactory::create()->setPropertyType($propertyType);
                $propertyValue = PropertyValueFactory::create()->setValue($geologicalUnitProperty[2]);
                $property->setName('Horizontal anisotropy'.' '.$geologicalUnit->getName());
                $property->addValue($propertyValue);
                $geologicalUnit->addProperty($property);
                $this->entityManager->persist($property);

                $propertyType = $this->getPropertyType($this->entityManager, 'va');
                $property = PropertyFactory::create()->setPropertyType($propertyType);
                $propertyValue = PropertyValueFactory::create()->setValue($geologicalUnitProperty[3]);
                $property->setName('Vertical anisotropy'.' '.$geologicalUnit->getName());
                $property->addValue($propertyValue);
                $geologicalUnit->addProperty($property);
                $this->entityManager->persist($property);

                $this->entityManager->flush();
            }
        }

        // Add Boundary and ObservationPoints
        $boundary = BoundaryFactory::create()
            ->setOwner($user)
            ->setName('SC2_B1')
            ->setPublic(true)
        ;

        $converter = new PostgreSql();
        $geometryText = "LineString (11777056.49104572273790836 2403440.17028302047401667, 11777973.9436037577688694 2403506.49811625294387341, 11780228.12698311358690262 2402856.2682070448063314, 11781703.59880801662802696 2401713.22520185634493828, 11782192.89715446159243584 2400859.20254275016486645, 11782678.03379831649363041 2399224.82580633740872145, 11782955.64566324092447758 2398372.03099954081699252, 11783586.59488865174353123 2397659.24991086078807712, 11784427.14815393835306168 2396590.66674219723790884, 11784914.27011025696992874 2395382.18267500726506114, 11785330.82068796083331108 2394174.15454542031511664, 11785536.96124399080872536 2393180.11378513323143125, 11786097.1273522675037384 2392467.84464810928329825, 11787011.69080197438597679 2392108.19440084183588624, 11787715.90038010291755199 2391962.42985267844051123, 11788487.82464707084000111 2391319.86146369902417064, 11789680.65233467146754265 2390320.33801258727908134, 11789747.53923093341290951 2389681.79035578016191721, 11789176.05731181986629963 2388337.88133400911465287, 11788252.26803966984152794 2386996.03587882174178958, 11787540.82363948784768581 2385794.83458124194294214, 11783036.01740818470716476 2386882.81766726961359382, 11777486.37431096099317074 2390598.53498441586270928, 11775189.21765423379838467 2396638.4036272126249969, 11777056.49104572273790836 2403440.17028302047401667)";

        /** @var AbstractSpatialType $lineStringType */
        $lineStringType = Type::getType('linestring');

        /** @var LineString $lineString */
        $lineString = $converter->convertStringToPHPValue($lineStringType, $geometryText);
        $lineString->setSrid(3857);
        $boundary->setGeometry($lineString);
        $boundary->addGeologicalLayer($layer_1);
        $boundary->addGeologicalLayer($layer_2);
        $boundary->addGeologicalLayer($layer_3);
        $boundary->addGeologicalLayer($layer_4);
        $entityManager->persist($boundary);
        $entityManager->flush();

        // Add ObservationPoints
        $observationPointPoints = array(
            array('name' => 'SC2_HTTP_4', 'point' => new Point(11777056.49104572273790836, 2403440.17028302047401667, 3857)),
            array('name' => 'SC2_one_more', 'point' => new Point(11784427.14815393835306168, 2396590.66674219723790884, 3857)),
            array('name' => 'SC2_H9', 'point' => new Point(11787540.82363948784768581, 2385794.83458124194294214, 3857)),
            array('name' => 'SC2_Q68', 'point' => new Point(11783036.01740818470716476, 2386882.81766726961359382, 3857)),
            array('name' => 'SC2_Q_62', 'point' => new Point(11775189.21765423379838467, 2396638.40362721262499690, 3857)),
        );

        foreach ($observationPointPoints as $observationPointPoint)
        {
            $observationPoint = ObservationPointFactory::setOwnerNameAndPoint($user, $observationPointPoint['name'], $observationPointPoint['point'], $public);
            $boundary->addObservationPoint($observationPoint);

            $geologicalLayer = $entityManager->getRepository('AppBundle:GeologicalLayer')
                ->findOneBy(array(
                    'name' => "SC2_L4"
                ));

            if (!$geologicalLayer)
            {
                throw new NotFoundHttpException();
            }

            $boundary->addGeologicalLayer($geologicalLayer);
            $model->addBoundary($boundary);
            $entityManager->persist($boundary);
            $entityManager->persist($observationPoint);
            $entityManager->flush();

            $filename = 'scenario_2_observationPoint_'.str_replace('SC2_', '', $observationPoint->getName()).'_properties.csv';
            $this->addModelObjectPropertiesFromCSVFile($observationPoint, __DIR__.'/'.$filename, ';');
        }

        $wells = array(
            array(1, 'CD10', 11777809.79499545693397522, 2401995.67269986681640148, -30, -60, 4320, -4900, 11777809.79, 2401995.67),
            array(2, 'CD11', 11778088.30947495624423027, 2401994.25986256683245301, -30, -60, 4320, -4900, 11778088.31, 2401994.26),
            array(3, 'CD12', 11778345.50155015662312508, 2401992.95128001738339663, -30, -60, 4320, -4100, 11778345.5, 2401992.95),
            array(4, 'CD13', 11778881.09251317754387856, 2401990.21416973834857345, -30, -60, 4320, -4000, 11778881.09, 2401990.21),
            array(5, 'CD17', 11780226.6936198603361845, 2401185.61681587109342217, -30, -60, 4320, -4900, 11780226.69, 2401185.62),
            array(6, 'CD18', 11780226.6936198603361845, 2401185.61681587109342217, -30, -60, 4320, -4900, 11780226.69, 2401185.62),
            array(7, 'CD19', 11780482.3135919813066721, 2400904.04203267069533467, -30, -60, 4320, -4900, 11780482.31, 2400904.04),
            array(8, 'CD20', 11780482.3135919813066721, 2400904.04203267069533467, -30, -60, 4320, -4900, 11780482.31, 2400904.04),
            array(9, 'CD7', 11777017.11587653681635857, 2401999.66968260146677494, -30, -60, 4320, -4900, 11777017.12, 2401999.67),
            array(10, 'CD8', 11777274.20259966887533665, 2401998.3772620651870966, -30, -60, 4320, -4900, 11777274.2, 2401998.38),
            array(11, 'CD9', 11777552.71745001152157784, 2401996.97288660053163767, -30, -60, 4320, -4900, 11777552.72, 2401996.97),
            array(12, 'HDI10', 11778803.9348050132393837, 2389919.65258358465507627, -40, -70, 4320, -2800, 11778803.93, 2389919.65),
            array(13, 'HDI13', 11778287.28281443752348423, 2390424.27275150641798973, -40, -70, 4320, -2800, 11778287.28, 2390424.27),
            array(14, 'HDI5', 11778565.60789936594665051, 2390422.85955950664356351, -40, -70, 4320, -2800, 11778565.61, 2390422.86),
            array(15, 'HDI6', 11778628.7308922503143549, 2390196.31113039888441563, -40, -70, 4320, -2800, 11778628.73, 2390196.31),
            array(16, 'HDI7', 11778821.00705778226256371, 2390119.93042483413591981, -40, -70, 4320, -2800, 11778821.01, 2390119.93),
            array(17, 'HDI8', 11778822.20144898630678654, 2390356.91003846284002066, -40, -70, 4320, -2800, 11778822.2, 2390356.91),
            array(18, 'KGIANG1', 11779098.08642016164958477, 2389956.93314105411991477, -32.27, -68.83, 4320, -3000, 11779098.09, 2389956.93),
            array(19, 'KTN42', 11777146.73212774097919464, 2402246.94875892251729965, -39.88, -66.35, 4320, -80, 11777146.73, 2402246.95),
            array(20, 'KTN80', 11787797.60370349697768688, 2391202.97728021768853068, -32.19, -88.36, 4320, -600, 11787797.6, 2391202.98),
            array(21, 'LN1', 11787613.29082772321999073, 2386865.27748037222772837, -40, -70, 4320, -2135, 11787613.29, 2386865.28),
            array(22, 'LN10', 11788726.25647358968853951, 2388753.58681372459977865, -40, -70, 4320, -2135, 11788726.26, 2388753.59),
            array(23, 'LN11', 11788984.59457647800445557, 2389010.63655604887753725, -40, -70, 4320, -2135, 11788984.59, 2389010.64),
            array(24, 'LN12', 11788984.59457647800445557, 2389010.63655604887753725, -40, -70, 4320, -2135, 11788984.59, 2389010.64),
            array(25, 'LN13', 11788986.16430903784930706, 2389290.6827991041354835, -40, -70, 4320, -2135, 11788986.16, 2389290.68),
            array(26, 'LN14', 11789267.49526369944214821, 2389826.59154414804652333, -40, -70, 4320, -2135, 11789267.5, 2389826.59),
            array(27, 'LN15', 11788989.17762009054422379, 2389828.17262880643829703, -40, -70, 4320, -2135, 11788989.18, 2389828.17),
            array(28, 'LN16', 11788990.6272040531039238, 2390086.69249562220647931, -40, -70, 4320, -2135, 11788990.63, 2390086.69),
            array(29, 'LN2', 11787347.26407890021800995, 2387145.71913112327456474, -40, -70, 4320, -2135, 11787347.26, 2387145.72),
            array(30, 'LN20', 11788459.96383132226765156, 2390908.39469485450536013, -40, -70, 4320, -2135, 11788459.96, 2390908.39),
            array(31, 'LN21', 11788203.05212100967764854, 2390909.84320783708244562, -40, -70, 4320, -2135, 11788203.05, 2390909.84),
            array(32, 'LN22', 11788204.4910411573946476, 2391168.37900674156844616, -40, -70, 4320, -2135, 11788204.49, 2391168.38),
            array(33, 'LN23', 11787638.65069332718849182, 2391451.64576283935457468, -40, -70, 4320, -2135, 11787638.65, 2391451.65),
            array(34, 'LN24', 11787638.65069332718849182, 2391451.64576283935457468, -40, -70, 4320, -2135, 11787638.65, 2391451.65),
            array(35, 'LN25', 11787372.4459209144115448, 2391711.6866044644266367, -40, -70, 4320, -2135, 11787372.45, 2391711.69),
            array(36, 'LN3', 11787350.23137721605598927, 2387684.22437982214614749, -40, -70, 4320, -2135, 11787350.23, 2387684.22),
            array(37, 'LN6', 11787906.80211838521063328, 2387681.1122775818221271, -40, -70, 4320, -2135, 11787906.8, 2387681.11),
            array(38, 'LN7', 11787908.23473594710230827, 2387939.60222975071519613, -40, -70, 4320, -2135, 11787908.23, 2387939.6),
            array(39, 'LN8', 11788188.08032611012458801, 2388218.06966029526665807, -40, -70, 4320, -2135, 11788188.08, 2388218.07),
            array(40, 'LN9', 11788724.69117069430649281, 2388473.54868405824527144, -40, -70, 4320, -2135, 11788724.69, 2388473.55),
            array(41, 'LY10', 11785599.57843398675322533, 2392497.18166037555783987, -40, -70, 4320, -3125, 11785599.58, 2392497.18),
            array(42, 'LY11', 11785534.08516378700733185, 2392271.29036100627854466, -40, -70, 4320, -3125, 11785534.09, 2392271.29),
            array(43, 'LY12', 11785258.63217497803270817, 2392810.39757040143013, -40,-70, 4320, -3125, 11785258.63, 2392810.4),
            array(44, 'LY13', 11785536.99871129170060158, 2392808.87313925800845027, -40, -70, 4320,- 3125,11785537, 2392808.87),
            array(45, 'LY14', 11785791.0269112978130579, 2392269.8797018863260746, -40, -70, 4320, -3125, 11785791.03, 2392269.88),
            array(46, 'LY15', 11785324.00764934346079826, 2393014.74322587111964822, -40, -70, 4320, -3125, 11785324.01, 2393014.74),
            array(47, 'LY16', 11785228.59817047603428364, 2393198.42720729578286409, -40, -70, 4320, -3125, 11785228.6, 2393198.43),
            array(48, 'LY17', 11785133.36124992743134499, 2393414.42730379290878773, -40, -70, 4320, -3125, 11785133.36, 2393414.43),
            array(49, 'LY3', 11784735.13751051388680935, 2393017.95607117516919971, -40, -70, 4320, -3125, 11784735.14, 2393017.96),
            array(50, 'LY4', 11784840.97543985024094582, 2392801.90504860132932663, -40, -70, 4320, -3125, 11784840.98, 2392801.91),
            array(51, 'LY5', 11784863.37823952175676823, 2392984.94311415310949087, -40, -70, 4320, -3125, 11784863.38, 2392984.94),
            array(52, 'LY6', 11784563.76478518545627594, 2393018.88738845149055123, -40, -70, 4320, -3125, 11784563.76, 2393018.89),
            array(53, 'LY7', 11784734.03711271658539772, 2392813.25852541066706181, -40, -70, 4320, -3125, 11784734.04, 2392813.26),
            array(54, 'LY8', 11784562.60855001211166382, 2392803.41852059355005622, -40, -70, 4320, -3125, 11784562.61, 2392803.42),
            array(55, 'LY9', 11785536.99871129170060158, 2392808.87313925800845027, -40, -70, 4320,- 3125,11785537, 2392808.87),
            array(56, 'MD7', 11775916.97566106915473938, 2396090.40221136016771197, -40, -70, 4320, -2500, 11775916.98, 2396090.4),
            array(57, 'MD8', 11775639.81231772527098656, 2396350.39451547199860215, -40, -70, 4320, -2500, 11775639.81, 2396350.39),
            array(58, 'MD9', 11775641.17792554758489132, 2396630.57373226108029485, -40, -70, 4320, -2500, 11775641.18, 2396630.57),
            array(59, 'NH10', 11778853.89063355140388012, 2396614.43721777061000466, -40, -70, 4320, -3333, 11778853.89, 2396614.44),
            array(60, 'NH11', 11778595.36439158022403717, 2396335.5729674156755209, -40, -70, 4320, -3333, 11778595.36, 2396335.57),
            array(61, 'NH12', 11778852.47466489300131798, 2396334.26022379705682397, -40, -70, 4320, -3333, 11778852.47, 2396334.26),
            array(62, 'NH13', 11779390.52857245318591595, 2396870.31426244927570224, -40, -70, 4320, -3333, 11779390.53, 2396870.31),
            array(63, 'NH14', 11779133.52573507465422153, 2396871.6345574907027185, -40, -70, 4320, -3333, 11779133.53, 2396871.63),
            array(64, 'NH4', 11779321.49263433180749416, 2395997.80848255380988121, -40, -70, 4320, -3333, 11779321.49, 2395997.81),
            array(65, 'NH5', 11779215.84146180190145969, 2396203.09262026753276587, -40, -70, 4320, -3333, 11779215.84, 2396203.09),
            array(66, 'NH6', 11779130.79418394342064857, 2396332.83497229684144258, -40, -70, 4320, -3333, 11779130.79, 2396332.83),
            array(67, 'NH7', 11779132.21451539173722267, 2396613.01176991406828165, -40, -70, 4320, -3333, 11779132.21, 2396613.01),
            array(68, 'NH8', 11778851.16781957447528839, 2396075.64495939249172807, -40, -70, 4320, -3333, 11778851.17, 2396075.64),
            array(69, 'NH9', 11778856.61411543004214764, 2397153.23475438728928566, -40, -70, 4320, -3333, 11778856.61, 2397153.23),
            array(70, 'NSL14', 11782118.79915492050349712, 2394335.68804973131045699, -40, -70, 4320, -4545, 11782118.8, 2394335.69),
            array(71, 'NSL16', 11781778.66265136003494263, 2394800.80602123867720366, -40, -70, 4320, -4545, 11781778.66, 2394800.81),
            array(72, 'NSL17', 11781745.51815875805914402, 2394607.02874239487573504, -40, -70, 4320, -4545, 11781745.52, 2394607.03),
            array(73, 'NSL18', 11782066.36777259036898613, 2394529.91463655466213822, -40, -70, 4320, -4545, 11782066.37, 2394529.91),
            array(74, 'NSL21', 11782142.87746953777968884, 2394841.98270549112930894, -40, -70, 4320, -4545, 11782142.88, 2394841.98),
            array(75, 'NSL22', 11781684.6754087470471859, 2395264.62336773658171296, -40, -70, 4320, -4545, 11781684.68, 2395264.62),
            array(76, 'NSL23', 11781800.94027045369148254, 2394983.85897833714261651, -40, -70, 4320, -4545, 11781800.94, 2394983.86),
            array(77, 'NSL24', 11781744.39507952891290188, 2394391.53954998357221484, -40, -70, 4320, -4545, 11781744.4, 2394391.54),
            array(78, 'NSL25', 11781850.61456234194338322, 2394240.13197004934772849, -40, -70, 4320, -4545, 11781850.61, 2394240.13),
            array(79, 'NSL27', 11781928.57555761933326721, 2394810.78727500885725021, -40, -70, 4320, -4545, 11781928.58, 2394810.79),
            array(80, 'NSL9', 11782142.08785763010382652, 2394691.13602665718644857, -40, -70, 4320, -4545, 11782142.09, 2394691.14),
            array(81, 'PV1', 11782822.18435899540781975, 2387590.4185093673877418, -40, -70, 4320, -2808, 11782822.18, 2387590.42),
            array(82, 'PV2', 11782821.33491864986717701, 2387428.86434731679037213, -40, -70, 4320, -2808, 11782821.33, 2387428.86),
            array(83, 'PV3', 11782906.42668719962239265, 2387342.24314445350319147, -40, -70, 4320, -2808, 11782906.43, 2387342.24),
            array(84, 'PV6', 11783090.38631781563162804, 2387707.46468133293092251, -40, -70, 4320, -2808, 11783090.39, 2387707.46),
            array(85, 'PV7', 11783089.13685310818254948, 2387470.51122804777696729, -40, -70, 4320, -2808, 11783089.14, 2387470.51),
            array(86, 'TM10', 11783188.79112967662513256, 2390140.21012788079679012, -45, -75, 4320, -3125, 11783188.79, 2390140.21),
            array(87, 'TM11', 11783639.82684437558054924, 2390396.32815032452344894, -45, -75, 4320, -3125, 11783639.83, 2390396.33),
            array(88, 'TM14', 11783641.19957356713712215, 2390654.85946180252358317, -45, -75, 4320, -3125, 11783641.2, 2390654.86),
            array(89, 'TM15', 11783916.66560793109238148, 2390114.75327363051474094, -45, -75, 4320, -3125, 11783916.67, 2390114.75),
            array(90, 'TM16', 11783337.80219421535730362, 2389967.06175833381712437, -45, -75, 4320, -3125, 11783337.8, 2389967.06),
            array(91, 'TM18', 11783670.46493076905608177, 2390116.07785767177119851, -45, -75, 4320, -3125, 11783670.46, 2390116.08),
            array(92, 'TM19', 11783918.15691797249019146, 2390394.83077630028128624, -45, -75, 4320, -3125, 11783918.16, 2390394.83),
            array(93, 'TM8', 11783381.43655735999345779, 2390117.62849007733166218, -45, -75, 4320, -3125, 11783381.44, 2390117.63),
            array(94, 'UCF22', 11783788.51202009432017803, 2390120.82815810898318887, -2.96, -19.94, 4320, -360, 11783788.51, 2390120.83),
            array(95, 'YP10', 11782881.68859566561877728, 2396852.00869912700727582, -45, -75, 4320, -4400, 11782881.69, 2396852.01),
            array(96, 'YP12', 11783141.60782415792346001, 2397400.20711772469803691, -45, -75, 4320, -4400, 11783141.61, 2397400.21),
            array(97, 'YP15', 11782885.9567362554371357, 2397660.21421966049820185, -45, -75, 4320, -4400, 11782885.96, 2397660.21),
            array(98, 'YP16', 11782884.59073513932526112, 2397401.58236767631024122, -45, -75, 4320, -4400, 11782884.59, 2397401.58),
            array(99, 'YP17', 11783141.60782415792346001, 2397400.20711772469803691, -45, -75, 4320, -4400, 11783141.61, 2397400.21),
            array(100, 'YP18', 11782606.15094231255352497, 2397403.06802417244762182, -45, -75, 4320, -4400, 11782606.15, 2397403.07),
            array(101, 'YP19', 11782883.16801407374441624, 2397132.18036714708432555, -45, -75, 4320, -4400, 11782883.17, 2397132.18),
            array(102, 'YP20', 11782604.73241920955479145, 2397133.66582690924406052, -45, -75, 4320, -4400, 11782604.73, 2397133.67),
            array(103, 'YP22', 11782349.14232590608298779, 2397404.4354342189617455, -45, -75, 4320, -4400, 11782349.14, 2397404.44),
            array(104, 'YP26', 11783141.60782415792346001, 2397400.20711772469803691, -45, -75, 4320, -4400, 11783141.61, 2397400.21),
            array(105, 'YP27', 11783140.18122811987996101, 2397130.80529930861666799, -45, -75, 4320, -4400, 11783140.18, 2397130.81),
            array(106, 'YP29', 11782607.5129128210246563, 2397661.7000650349073112, -45, -75, 4320, -4400, 11782607.51, 2397661.7),
            array(107, 'YP30', 11782885.9567362554371357, 2397660.21421966049820185, -45, -75, 4320, -4400, 11782885.96, 2397660.21),
            array(108, 'YP31', 11782353.32513551786541939, 2398200.81470863055437803, -45, -75, 4320, -4400, 11782353.33, 2398200.81),
            array(109, 'YP32', 11782353.32513551786541939, 2398200.81470863055437803, -45, -75, 4320, -4400, 11782353.33, 2398200.81),
            array(110, 'YP33', 11782354.79713419266045094, 2398481.00762987695634365, -45, -75, 4320, -4400, 11782354.8, 2398481.01),
            array(111, 'YP34', 11782077.69510736502707005, 2398741.13343313755467534, -45, -75, 4320, -4400, 11782077.7, 2398741.13),
            array(112, 'YP35', 11782077.69510736502707005, 2398741.13343313755467534, -45, -75, 4320, -4400, 11782077.7, 2398741.13),
            array(113, 'YP36', 11781727.0486772432923317, 2399281.86090355273336172, -45, -75, 4320, -4400, 11781727.05, 2399281.86),
            array(114, 'YP37', 11781822.13090667873620987, 2399022.69560982333496213, -45, -75, 4320, -4400, 11781822.13, 2399022.7),
            array(115, 'YP38', 11781822.13090667873620987, 2399022.69560982333496213, -45, -75, 4320, -4400, 11781822.13, 2399022.7),
            array(116, 'YP39', 11781546.47275182791054249, 2399563.04242969118058681, -45, -75, 4320, -4400, 11781546.47, 2399563.04),
            array(117, 'YP40', 11781375.76468020677566528, 2399704.0468372106552124, -45, -75, 4320, -4400, 11781375.76, 2399704.05),
            array(118, 'YP41', 11781130.26916185207664967, 2399867.00588304502889514, -45, -75, 4320, -4400, 11781130.27, 2399867.01),
            array(119, 'YP42', 11780758.02947362139821053, 2400364.75818847771733999, -45, -75, 4320, -4400, 11780758.03, 2400364.76)
        );

        $header = array('id', 'name', 'wkt_x', 'wkt_y', 'ztop', 'zbot', 'stoptime', 'pumpingrate', 'x', 'y');
        foreach ($wells as $row) {
            $well = array_combine($header, $row);

            $model->addWellBoundary(WellBoundaryFactory::create()
                ->setOwner($user)
                ->setName($well['name'])
                ->setPublic($public)
                ->setPoint(new Point($well['x'], $well['y'], 3857))
                ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue($well['ztop']))
                ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue($well['zbot']))
                ->addValue($propertyTypePumpingRate, PropertyValueFactory::create()->setValue($well['pumpingrate']))
            );

            $this->entityManager->persist($model);
            $this->entityManager->flush();
        }


        return 1;
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

            foreach ($data as $dataPoint)
            {
                $propertyTimeValue = PropertyTimeValueFactory::setDateTimeAndValue(new \DateTime($dataPoint[$dataFields[0]]), (float)$dataPoint[$dataFields[$i]]);
                $baseElement->addValue($propertyType, $propertyTimeValue);
                $this->entityManager->persist($propertyTimeValue);

                echo $counter++."\n";

                if ($counter % 20 == 0)
                {
                    $this->entityManager->flush();
                }
            }

            $this->entityManager->flush();
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
            throw new NotFoundHttpException();
        }

        return $propertyType;
    }
}