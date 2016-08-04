<?php

namespace AppBundle\DataFixtures\ORM\Scenarios\Scenario_5;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\GeologicalUnit;
use AppBundle\Entity\User;
use AppBundle\Entity\WellBoundary;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\ConstantHeadBoundaryFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\Point;
use AppBundle\Model\PropertyTimeValueFactory;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\RechargeBoundaryFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\WellBoundaryFactory;
use Inowas\PyprocessingBundle\Service\Interpolation;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadScenario_5 implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

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
        $geoTools = $this->container->get('inowas.geotools');

        $public = true;
        $username = 'inowas';
        $email = 'inowas@inowas.com';
        $password = 'inowas';

        $user = $entityManager->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $username
            ));

        if (!$user) {
            // Add new User
            $user = new User();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setEnabled(true);
            $entityManager->persist($user);
        }

        // Load PropertyTypes
        $propertyTypeGwHead = PropertyTypeFactory::create(PropertyType::HYDRAULIC_HEAD);
        $propertyTypeTopElevation = PropertyTypeFactory::create(PropertyType::TOP_ELEVATION);
        $propertyTypeBottomElevation = PropertyTypeFactory::create(PropertyType::BOTTOM_ELEVATION);
        $propertyTypeHydraulicConductivity = PropertyTypeFactory::create(PropertyType::HYDRAULIC_CONDUCTIVITY);
        $propertyTypePrecipitation = PropertyTypeFactory::create(PropertyType::PRECIPITATION);

        $model = ModFlowModelFactory::create()
            ->setName("Inowas Pirna")
            ->setOwner($user)
            ->setPublic($public)
            ->setArea(AreaFactory::create()
                ->setName('Pirna Area')
                ->setOwner($user)
                ->setPublic($public)
                ->setAreaType('SC5_AT1')
                ->setGeometry(new Polygon(array(
                    array(
                        array(5424432.87775928, 5648432.35452681),
                        array(5424571.86808835, 5648439.56337455),
                        array(5424571.86808835, 5648295.08683128),
                        array(5424568.87220358, 5648294.93703704),
                        array(5424568.42282086, 5648294.78724280),
                        array(5424568.57261510, 5648297.33374486),
                        array(5424561.38249165, 5648297.33374486),
                        array(5424561.53228588, 5648294.93703704),
                        array(5424557.33804720, 5648295.23662552),
                        array(5424557.18825296, 5648293.73868313),
                        array(5424554.94133938, 5648293.73868313),
                        array(5424555.39072210, 5648288.94526750),
                        array(5424555.09113362, 5648287.74691358),
                        array(5424553.44339700, 5648287.59711935),
                        array(5424553.74298547, 5648285.20041153),
                        array(5424551.04668918, 5648285.50000001),
                        array(5424551.34627765, 5648282.65390947),
                        array(5424549.39895255, 5648283.55267490),
                        array(5424549.24915831, 5648281.00617284),
                        array(5424543.25738877, 5648281.60534980),
                        array(5424543.55697724, 5648278.90905350),
                        array(5424539.66232704, 5648279.20864198),
                        array(5424539.81212128, 5648276.96172840),
                        array(5424527.52899370, 5648277.26131688),
                        array(5424527.67878794, 5648274.86460906),
                        array(5424519.44010482, 5648275.16419754),
                        array(5424519.44010482, 5648272.76748972),
                        array(5424517.34298547, 5648273.06707820),
                        array(5424517.64257395, 5648271.11975309),
                        array(5424509.25409658, 5648271.11975309),
                        array(5424508.05574268, 5648270.96995885),
                        array(5424508.50512539, 5648273.51646091),
                        array(5424507.30677148, 5648273.36666667),
                        array(5424507.30677148, 5648271.11975309),
                        array(5424505.20965214, 5648271.41934157),
                        array(5424505.20965214, 5648268.87283951),
                        array(5424501.46479617, 5648269.02263375),
                        array(5424500.26644226, 5648268.87283951),
                        array(5424500.26644226, 5648271.41934157),
                        array(5424491.42858218, 5648271.11975309),
                        array(5424491.27878794, 5648268.87283951),
                        array(5424483.18989905, 5648269.17242799),
                        array(5424483.33969329, 5648266.77572017),
                        array(5424469.25903485, 5648267.37489712),
                        array(5424469.25903485, 5648265.27777778),
                        array(5424453.98002251, 5648265.72716050),
                        array(5424453.08125708, 5648265.72716050),
                        array(5424453.38084556, 5648263.33045268),
                        array(5424443.34463156, 5648263.63004116),
                        array(5424443.19483732, 5648261.68271606),
                        array(5424437.35286202, 5648261.83251029),
                        array(5424437.20306778, 5648260.33456791),
                        array(5424432.87775928, 5648432.35452681)
                    )), 31469)))
            ->setSoilModel(SoilModelFactory::create()
                ->setName('Soilmodel_Scenario_5')
                ->setPublic($public)
                ->setOwner($user)
            )
            ->setGridSize(new GridSize(70, 90))
        ;

        if ($model->getArea()->getBoundingBox() instanceof BoundingBox) {
            $model->setBoundingBox($geoTools->transformBoundingBox($model->getArea()->getBoundingBox(), 4326));
        }

        $entityManager->persist($model);
        $entityManager->flush();

        // Create new geological layers
        $layer_1 = GeologicalLayerFactory::create()
            ->setOwner($user)
            ->setName('SC5_L1')
            ->setPublic($public);
        $layer_1->setOrder(GeologicalLayer::TOP_LAYER);
        $model->getSoilModel()->addGeologicalLayer($layer_1);

        $layer_2 = GeologicalLayerFactory::create()
            ->setOwner($user)
            ->setName('SC5_L2')
            ->setPublic($public);
        $layer_2->setOrder(GeologicalLayer::TOP_LAYER+1);
        $entityManager->persist($layer_2);
        $model->getSoilModel()->addGeologicalLayer($layer_2);

        $layer_3 = GeologicalLayerFactory::create()
            ->setOwner($user)
            ->setName('SC5_L3')
            ->setPublic($public);
        $layer_3->setOrder(GeologicalLayer::TOP_LAYER+2);
        $entityManager->persist($layer_3);
        $model->getSoilModel()->addGeologicalLayer($layer_3);

        $layer_4 = GeologicalLayerFactory::create()
            ->setOwner($user)
            ->setName('SC5_L4')
            ->setPublic($public);
        $layer_4->setOrder(GeologicalLayer::TOP_LAYER+3);
        $entityManager->persist($layer_4);
        $model->getSoilModel()->addGeologicalLayer($layer_4);

        $layer_5 = GeologicalLayerFactory::create()
            ->setOwner($user)
            ->setName('SC5_L5')
            ->setPublic($public);
        $layer_5->setOrder(GeologicalLayer::TOP_LAYER+4);
        $entityManager->persist($layer_5);
        $model->getSoilModel()->addGeologicalLayer($layer_5);
        $entityManager->flush();

        $boreholes = array(
            array('name', 'x', 'y', 'srid', 'layer_1_top', 'layer_1_bottom', 'layer_2_bottom', 'layer_3_bottom', 'layer_4_bottom', 'layer_5_bottom', 'layer_1_kf',  'layer_2_kf',  'layer_3_kf',  'layer_4_kf',  'layer_5_kf'),
            array('G01', 5424455.92734763, 5648391.03004116, 31469, 119.478, 112.468, 108.808, 107.288, 105.768, 105.628, 7.9E-04, 4.9E-04, 6.5E-04, 2.7E-03, 1.5E-03),
            array('G02', 5424474.00000000, 5648390.00000000, 31469, 119.000, 111.690, 108.030, 106.810, 105.590, 104.980, 8.1E-04, 7.9E-04, 2.6E-03, 3.1E-03, 1.6E-03),
            array('G05', 5424457.00000000, 5648402.00000000, 31469, 119.340, 112.030, 108.670, 107.150, 105.930, 105.320, 7.7E-04, 1.5E-04, 2.7E-03, 3.2E-03, 9.4E-04),
            array('G10', 5424502.02652459, 5648417.43127574, 31469, 119.227, 112.527, 109.027, 107.527, 106.027, 105.427, 6.7E-04, 6.7E-04, 2.2E-04, 2.6E-03, 1.6E-03),
            array('G11', 5424540.44874682, 5648379.15884776, 31469, 116.939, 112.369, 108.409, 105.939, 104.449, 103.529, 8.0E-04, 1.2E-04, 1.3E-03, 3.4E-03, 2.3E-04),
            array('G13', 5424466.45039291, 5648370.17119344, 31469, 116.963, 111.783, 109.343, 107.513, 103.253, 102.333, 7.8E-04, 2.9E-04, 2.5E-04, 2.5E-03, 4.4E-04),
            array('G15', 5424561.64463159, 5648363.73004118, 31469, 116.076, 112.116, 108.156, 106.326, 103.886, 102.976, 7.6E-04, 1.5E-04, 2.4E-03, 3.4E-03, 2.1E-03),
            array('G17', 5424523.89648345, 5648354.51769550, 31469, 115.709, 110.829, 108.089, 105.649, 104.129, 102.299, 5.4E-04, 2.6E-04, 3.8E-03, 3.8E-03, 1.7E-04)
        );

        $header = null;
        foreach ($boreholes as $borehole){
            if (is_null($header)){
                $header = $borehole;
                continue;
            }

            $borehole = array_combine($header, $borehole);
            echo "Persisting Borehole ".$borehole['name']."\r\n";
            $geologicalPoint = GeologicalPointFactory::create()
                ->setOwner($user)
                ->setName($borehole['name'])
                ->setPublic($public)
                ->setPoint($geoTools->transformPoint(new Point($borehole['x'], $borehole['y'], 31469), 4326))
                ;

            $model->getSoilModel()->addGeologicalPoint($geologicalPoint);

            $geologicalUnit = GeologicalUnitFactory::create()
                    ->setOwner($user)
                    ->setName($borehole['name'].'.U1')
                    ->setOrder(GeologicalUnit::TOP_LAYER)
                    ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue($borehole['layer_1_top']))
                    ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue($borehole['layer_1_bottom']))
                    ->addValue($propertyTypeHydraulicConductivity, PropertyValueFactory::create()->setValue($borehole['layer_1_kf']*60*60*24));

            $geologicalPoint->addGeologicalUnit($geologicalUnit);
            $layer_1->addGeologicalUnit($geologicalUnit);

            $geologicalUnit = GeologicalUnitFactory::create()
                    ->setOwner($user)
                    ->setOrder(GeologicalUnit::TOP_LAYER+1)
                    ->setName($borehole['name'].'.U2')
                    ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue($borehole['layer_2_bottom']))
                    ->addValue($propertyTypeHydraulicConductivity, PropertyValueFactory::create()->setValue($borehole['layer_2_kf']*60*60*24));

            $geologicalPoint->addGeologicalUnit($geologicalUnit);
            $layer_2->addGeologicalUnit($geologicalUnit);

            $geologicalUnit = GeologicalUnitFactory::create()
                    ->setOwner($user)
                    ->setOrder(GeologicalUnit::TOP_LAYER+2)
                    ->setName($borehole['name'].'.U3')
                    ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue($borehole['layer_3_bottom']))
                    ->addValue($propertyTypeHydraulicConductivity, PropertyValueFactory::create()->setValue($borehole['layer_3_kf']*60*60*24));

            $geologicalPoint->addGeologicalUnit($geologicalUnit);
            $layer_3->addGeologicalUnit($geologicalUnit);


            $geologicalUnit = GeologicalUnitFactory::create()
                    ->setOwner($user)
                    ->setOrder(GeologicalUnit::TOP_LAYER+3)
                    ->setName($borehole['name'].'.U4')
                    ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue($borehole['layer_4_bottom']))
                    ->addValue($propertyTypeHydraulicConductivity, PropertyValueFactory::create()->setValue($borehole['layer_4_kf']*60*60*24));

            $geologicalPoint->addGeologicalUnit($geologicalUnit);
            $layer_4->addGeologicalUnit($geologicalUnit);

            $geologicalUnit = GeologicalUnitFactory::create()
                    ->setOwner($user)
                    ->setOrder(GeologicalUnit::TOP_LAYER+4)
                    ->setName($borehole['name'].'.U5')
                    ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue($borehole['layer_5_bottom']))
                    ->addValue($propertyTypeHydraulicConductivity, PropertyValueFactory::create()->setValue($borehole['layer_5_kf']*60*60*24));

            $geologicalPoint->addGeologicalUnit($geologicalUnit);
            $layer_5->addGeologicalUnit($geologicalUnit);
            $entityManager->flush();
        }

        $soilModelService = $this->container->get('inowas.soilmodel');
        $soilModelService->setModflowModel($model);
        $layers = $model->getSoilModel()->getGeologicalLayers();

        /** @var GeologicalLayer $layer */
        foreach ($layers as $layer) {
            $propertyTypes = $soilModelService->getAllPropertyTypesFromLayer($layer);
            /** @var PropertyType $propertyType */
            foreach ($propertyTypes as $propertyType){
                if ($propertyType->getAbbreviation() == PropertyType::TOP_ELEVATION && $layer->getOrder() != GeologicalLayer::TOP_LAYER) {
                    continue;
                }

                echo (sprintf("Interpolating Layer %s, Property %s\r\n", $layer->getName(), $propertyType->getDescription()));
                $output = $soilModelService->interpolateLayerByProperty(
                    $layer,
                    $propertyType,
                    array(Interpolation::TYPE_GAUSSIAN, Interpolation::TYPE_IDW, Interpolation::TYPE_MEAN)
                );

                echo ($output);
            }
        }

        echo "Calculate active Cells-Array\r\n";
        $model->setActiveCells($geoTools->getActiveCells($model->getArea(), $model->getBoundingBox(), $model->getGridSize()));
        $entityManager->persist($model);
        $entityManager->flush();

        $timeValues = array(
            array('date','river_stage_max','river_stage_min','recharge_rate', 'precipitation', 'upper_boundary_max', 'upper_boundary_min'),
            array('02.06.2014',110.659,110.619,5.00E-09,0.0, 110.66,  110.575),
            array('03.06.2014',110.490,110.450,5.00E-09,0.0, 110.575, 110.51),
            array('04.06.2014',110.391,110.351,5.00E-09,0.0, 110.51,  110.44),
            array('05.06.2014',110.292,110.252,5.00E-09,0.0, 110.44,  109.89),
            array('06.06.2014',110.234,110.194,5.00E-09,0.0, 109.89,  109.815),
            array('10.06.2014',109.458,109.418,5.00E-09,0.0, 109.815, 109.71),
            array('11.06.2014',109.389,109.349,5.00E-09,0.0, 109.71,  109.655),
            array('12.06.2014',109.290,109.250,5.00E-09,0.0, 109.655, 109.475),
            array('13.06.2014',109.241,109.201,5.00E-09,0.0, 109.475, 109.445),
            array('16.06.2014',109.102,109.062,5.00E-09,0.0, 109.445, 109.43),
            array('17.06.2014',109.132,109.092,5.00E-09,0.0, 109.43,  109.41),
            array('18.06.2014',109.118,109.078,5.00E-09,0.0, 109.41,  109.4),
            array('19.06.2014',109.120,109.080,5.00E-09,0.0, 109.4,   109.335),
            array('20.06.2014',109.112,109.072,5.00E-09,2.6, 109.335, 109.35),
            array('23.06.2014',109.015,108.975,5.00E-09,0.0, 109.35,  109.345),
            array('24.06.2014',109.138,109.098,5.00E-09,0.0, 109.345, 109.36),
            array('25.06.2014',109.089,109.049,5.00E-09,9.4, 109.36,  109.42),
            array('26.06.2014',109.200,109.160,5.00E-09,2.0, 109.42,  109.415),
            array('27.06.2014',109.245,109.205,5.00E-09,0.2, 109.415, 109.415),
            array('30.06.2014',109.197,109.157,5.00E-09,4.0, 109.415, 109.41),
            array('01.07.2014',109.211,109.171,5.00E-09,0.0, 109.41,  109.405),
            array('02.07.2014',109.177,109.137,5.00E-09,0.0, 109.405, 109.32),
            array('03.07.2014',109.174,109.134,5.00E-09,0.0, 109.32,  109.305),
            array('07.07.2014',109.036,108.996,5.00E-09,0.0, 109.305, 109.41),
            array('08.07.2014',109.126,109.086,5.00E-09,18.8, 109.41,  109.575),
            array('09.07.2014',109.446,109.406,5.00E-09,3.0, 109.575, 109.71),
            array('10.07.2014',109.682,109.642,5.00E-09,18.6, 109.71,  109.685),
            array('11.07.2014',109.790,109.750,5.00E-09,4.8, 109.685, 109.61),
            array('14.07.2014',109.392,109.352,5.00E-09,0.6, 109.61,  109.57),
            array('15.07.2014',109.318,109.278,5.00E-09,0.8, 109.57,  109.515),
            array('16.07.2014',109.273,109.233,5.00E-09,0.0, 109.515, 109.46),
            array('17.07.2014',109.180,109.140,5.00E-09,1.8, 109.46,  109.385),
            array('18.07.2014',109.167,109.127,5.00E-09,0.0, 109.385, 109.37),
            array('21.07.2014',109.109,109.069,5.00E-09,2.8, 109.37,  109.5),
            array('22.07.2014',109.160,109.120,5.00E-09,0.0, 109.5,   109.58),
            array('23.07.2014',109.570,109.530,5.00E-09,1.2, 109.58,  109.575),
            array('24.07.2014',109.466,109.426,5.00E-09,5.8, 109.575, 109.56),
            array('25.07.2014',109.413,109.373,5.00E-09,0.6, 109.56,  109.495),
            array('26.07.2014',109.366,109.326,5.00E-09,0.2, 109.495, 109.48),
            array('28.07.2014',109.256,109.216,5.00E-09,1.6, 109.48,  109.51),
            array('29.07.2014',109.267,109.227,5.00E-09,0.6, 109.51,  109.56),
            array('30.07.2014',109.426,109.386,5.00E-09,0.0, 109.56,  109.505),
            array('31.07.2014',109.420,109.380,5.00E-09,0.2, 109.505, 109.535),
            array('01.08.2014',109.289,109.249,5.00E-09,0.0, 109.535, 109.565),
            array('02.08.2014',109.405,109.365,5.00E-09,1.0, 109.565, 109.56),
            array('04.08.2014',109.354,109.314,5.00E-09,3.0, 109.56,  109.625),
            array('05.08.2014',109.499,109.459,5.00E-09,2.8, 109.625, 109.67),
            array('06.08.2014',109.615,109.575,5.00E-09,0.2, 109.67,  109.615),
            array('07.08.2014',109.564,109.524,5.00E-09,0.0, 109.615, 109.535),
            array('08.08.2014',109.336,109.296,5.00E-09,0.0, 109.535, 109.495),
            array('09.08.2014',109.253,109.213,5.00E-09,0.0, 109.495, 109.445),
            array('10.08.2014',109.229,109.189,5.00E-09,0.0, 109.445, 109.425),
            array('11.08.2014',109.156,109.116,5.00E-09,1.6, 109.425, 109.46),
            array('12.08.2014',109.256,109.216,5.00E-09,1.6, 109.46,  109.43),
            array('13.08.2014',109.256,109.216,5.00E-09,7.6, 109.43,  109.42),
            array('14.08.2014',109.249,109.209,5.00E-09,0.6, 109.42,  109.455),
            array('15.08.2014',109.248,109.208,5.00E-09,0.8, 109.455, 109.445),
            array('16.08.2014',109.322,109.282,5.00E-09,12.4, 109.445, 109.465),
            array('17.08.2014',109.294,109.254,5.00E-09,0.2, 109.465, 109.48),
            array('18.08.2014',109.302,109.262,5.00E-09,1.8, 109.48,  109.46),
            array('19.08.2014',109.312,109.272,5.00E-09,0.2, 109.46,  109.45),
            array('20.08.2014',109.296,109.256,5.00E-09,0.2, 109.45,  109.395),
            array('21.08.2014',109.243,109.203,5.00E-09,0.0, 109.395, 109.41),
            array('25.08.2014',109.194,109.154,5.00E-09,0.4, 109.41,  109.42),
            array('26.08.2014',109.255,109.215,5.00E-09,4.2, 109.42,  109.44),
            array('27.08.2014',109.247,109.207,5.00E-09,6.0, 109.44,  109.42),
            array('28.08.2014',109.341,109.301,5.00E-09,0.2, 109.42,  109.52),
            array('29.08.2014',109.378,109.338,5.00E-09,0.4, 109.52,  109.53),
            array('30.08.2014',109.415,109.375,5.00E-09,0.2, 109.53,  109.56),
            array('01.09.2014',109.384,109.344,5.00E-09,2.2, 109.56,  109.66),
            array('02.09.2014',109.548,109.508,5.00E-09,0.6, 109.66,  109.775),
            array('03.09.2014',109.724,109.684,5.00E-09,0.2, 109.775, 109.805),
            array('04.09.2014',109.944,109.904,5.00E-09,0.0, 109.805, 109.825),
            array('05.09.2014',109.801,109.761,5.00E-09,0.0, 109.825, 109.83),
            array('09.09.2014',109.782,109.742,5.00E-09,0.0, 109.83,  109.845),
            array('10.09.2014',109.753,109.713,5.00E-09,0.0, 109.845, 109.83),
            array('11.09.2014',109.778,109.738,5.00E-09,11.0, 109.83,  110.23),
            array('12.09.2014',109.772,109.732,5.00E-09,3.2, 110.23,  110.285),
            array('15.09.2014',110.455,110.415,5.00E-09,0.0, 110.285, 110.415),
            array('16.09.2014',110.577,110.537,5.00E-09,0.6, 110.415, 110.41),
            array('17.09.2014',110.691,110.651,5.00E-09,0.2, 110.41,  110.38),
            array('18.09.2014',110.456,110.416,5.00E-09,0.2, 110.38,  110.41),
            array('22.09.2014',110.406,110.366,5.00E-09,8.6, 110.41,  110.445),
            array('23.09.2014',110.475,110.435,5.00E-09,2.0, 110.445, 110.43),
            array('24.09.2014',110.493,110.453,5.00E-09,0.0, 110.43,  110.415),
            array('25.09.2014',110.438,110.398,5.00E-09,1.6, 110.415, 110.26),
            array('26.09.2014',110.403,110.363,5.00E-09,4.4, 110.26,  110.245),
            array('29.09.2014',109.981,109.941,5.00E-09,0.2, 110.245, 110.235),
            array('30.09.2014',110.147,110.107,5.00E-09,0.2, 110.235, 110.225),
            array('01.10.2014',110.159,110.119,5.00E-09,2.6, 110.225, 110.26),
            array('02.10.2014',110.138,110.098,5.00E-09,0.0, 110.26,  110.255),
            array('06.10.2014',110.262,110.222,5.00E-09,0.0, 110.255, 110.055),
            array('07.10.2014',109.910,109.870,5.00E-09,0.2, 110.055, 109.935),
            array('08.10.2014',109.654,109.614,5.00E-09,1.2, 109.935, 109.92),
            array('09.10.2014',109.699,109.659,5.00E-09,0.0, 109.92,  109.84),
            array('10.10.2014',109.657,109.617,5.00E-09,0.2, 109.84,  109.865),
            array('13.10.2014',109.692,109.652,5.00E-09,0.0, 109.865, 109.935),
            array('14.10.2014',109.763,109.723,5.00E-09,10.0, 109.935, 109.96),
            array('15.10.2014',109.909,109.869,5.00E-09,0.0, 109.96,  110.005),
            array('16.10.2014',109.928,109.888,5.00E-09,0.2, 110.005, 110.26),
            array('17.10.2014',109.975,109.935,5.00E-09,2.2, 110.26,  110.17),
            array('21.10.2014',110.147,110.107,5.00E-09,2.4, 110.17,  110.26),
            array('22.10.2014',110.163,110.123,5.00E-09,16.8, 110.26,  110.35),
            array('23.10.2014',110.332,110.292,5.00E-09,0.6, 110.35,  110.72),
            array('24.10.2014',110.675,110.635,5.00E-09,0.2, 110.72,  110.715),
            array('27.10.2014',110.917,110.877,5.00E-09,0.0, 110.715, 110.71),
            array('28.10.2014',110.777,110.737,5.00E-09,0.0, 110.71,  110.64),
            array('29.10.2014',110.736,110.696,5.00E-09,0.0, 110.64,  110.48),
            array('30.10.2014',110.495,110.455,5.00E-09,0.0, 110.48,  110.31),
            array('01.11.2014',110.276,110.236,5.00E-09,0.0, 110.31,  110.29),
            array('03.11.2014',109.933,109.893,5.00E-09,0.0, 110.29,  110.185),
            array('04.11.2014',109.936,109.896,5.00E-09,0.0, 110.185, 110.12),
            array('05.11.2014',109.920,109.880,5.00E-09,0.0, 110.12,  110.065),
            array('06.11.2014',109.879,109.839,5.00E-09,0.0, 110.065, 110.00),
            array('07.11.2014',109.829,109.789,5.00E-09,0.0, 110.000, 110.00),
            array('10.11.2014',109.847,109.807,5.00E-09,0.0, 110.00,  110.03),
            array('11.11.2014',109.847,109.807,5.00E-09,0.0, 110.03,  110.04),
            array('12.11.2014',109.924,109.884,5.00E-09,0.0, 110.04,  110.015),
            array('13.11.2014',109.929,109.889,5.00E-09,0.0, 110.015, 109.99),
            array('14.11.2014',109.900,109.860,5.00E-09,0.0, 109.99,  109.97),
            array('17.11.2014',109.815,109.775,5.00E-09,0.0, 109.97,  110.085),
            array('18.11.2014',109.810,109.770,5.00E-09,0.0, 110.085, 110.14),
            array('20.11.2014',110.107,110.067,5.00E-09,0.0, 110.14,  110.07),
            array('21.11.2014',110.177,110.137,5.00E-09,0.0, 110.07,  110.02),
            array('24.11.2014',109.831,109.791,5.00E-09,0.0, 110.02,  109.995),
            array('25.11.2014',109.807,109.767,5.00E-09,0.0, 109.995, 109.99),
            array('26.11.2014',109.821,109.781,5.00E-09,0.0, 109.99,  109.955),
            array('27.11.2014',109.829,109.789,5.00E-09,0.0, 109.955, 109.82),
            array('28.11.2014',109.786,109.746,5.00E-09,0.0, 109.82,  109.81),
            array('01.12.2014',109.595,109.555,5.00E-09,0.0, 109.81,  109.86),
            array('02.12.2014',109.661,109.621,5.00E-09,0.0, 109.86,  109.86),
            array('03.12.2014',109.744,109.704,5.00E-09,0.0, 109.86,  109.86),
            array('04.12.2014',109.682,109.642,5.00E-09,0.0, 109.86,  109.86),
        );

        echo "Add CHD-Boundary for Elbe River\r\n";
        $chd_river = ConstantHeadBoundaryFactory::create()
            ->setOwner($user)
            ->setName('CHD-Elbe-River')
            ->setGeometry(new LineString(array(
                array(5424572.50471400, 5648296.32263380),
                array(5424567.41170989, 5648300.36707825),
                array(5424565.46438479, 5648300.66666672),
                array(5424562.31870577, 5648298.12016467),
                array(5424554.22981689, 5648294.82469142),
                array(5424552.73187450, 5648288.98271611),
                array(5424549.13681277, 5648284.63868318),
                array(5424545.54175104, 5648282.99094656),
                array(5424542.69566051, 5648283.59012351),
                array(5424535.20594857, 5648277.89794244),
                array(5424532.21006380, 5648279.84526755),
                array(5424528.01582512, 5648278.64691364),
                array(5424525.61911730, 5648277.14897125),
                array(5424523.22240948, 5648278.19753092),
                array(5424518.87837656, 5648276.69958853),
                array(5424513.93516668, 5648272.65514409),
                array(5424509.89072223, 5648274.15308648),
                array(5424508.39277985, 5648275.95061734),
                array(5424502.70059878, 5648271.00740746),
                array(5424497.90718314, 5648274.45267495),
                array(5424487.27179219, 5648271.15720170),
                array(5424469.44627779, 5648268.01152269),
                array(5424452.96891154, 5648265.91440335),
                array(5424446.37796503, 5648265.31522639),
                array(5424432.74668931, 5648261.42057619),
            ), 31469))
            ->addGeologicalLayer($layer_1)
            ->addGeologicalLayer($layer_2)
            ->addGeologicalLayer($layer_3)
            ->addGeologicalLayer($layer_4)
            ->addGeologicalLayer($layer_5)
        ;

        $header = null;
        foreach ($timeValues as $timeValue){
            if (is_null($header)){
                $header = $timeValue;
                continue;
            }
            $timeValue = array_combine($header, $timeValue);
            $chd_river->addValue($propertyTypeGwHead, PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime($timeValue['date']), (($timeValue['river_stage_max']+ $timeValue['river_stage_max'])/2) ));
        }

        $model->addBoundary($chd_river);

        echo "Add CHD-Boundary for Upper Boundary\r\n";
        $chd_upper_boundary = ConstantHeadBoundaryFactory::create()
            ->setOwner($user)
            ->setName('CHD-Upper-Boundary')
            ->setGeometry(new LineString(array(
                array(5424432.76541351, 5648430.38847740),
                array(5424572.05533120, 5648438.30884777)
            ), 31469))
            ->addGeologicalLayer($layer_1)
            ->addGeologicalLayer($layer_2)
            ->addGeologicalLayer($layer_3)
            ->addGeologicalLayer($layer_4)
            ->addGeologicalLayer($layer_5)
        ;

        $header = null;
        foreach ($timeValues as $timeValue){
            if (is_null($header)){
                $header = $timeValue;
                continue;
            }
            $timeValue = array_combine($header, $timeValue);
            $chd_upper_boundary->addValue($propertyTypeGwHead, PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime($timeValue['date']), (($timeValue['upper_boundary_max']+ $timeValue['upper_boundary_min'])/2) ));
        }

        $model->addBoundary($chd_upper_boundary);
        $entityManager->flush();

        echo "Add RCH-Boundary for precipitation\r\n";
        $rch = RechargeBoundaryFactory::create()
            ->setName('Precipitation')
            ->setOwner($user)
            ->setPublic($public)
            ->setGeometry($model->getArea()->getGeometry());

        $header = null;
        foreach ($timeValues as $timeValue){
            if (is_null($header)){
                $header = $timeValue;
                continue;
            }
            $timeValue = array_combine($header, $timeValue);
            $rch->addValue($propertyTypePrecipitation, PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime($timeValue['date']), ($timeValue['precipitation']/1000)));
        }
        
        $model->addBoundary($rch);
        $entityManager->flush();

        $wells = array(
            array('name', 'x', 'y', 'srid'),
            array('G01', 5424455.92734763, 5648391.03004116, 31469),
            array('G10', 5424502.02652459, 5648417.43127574, 31469),
            array('G11', 5424540.44874682, 5648379.15884776, 31469),
            array('G12', 5424499.92940525, 5648373.54156381, 31469),
            array('G13', 5424466.45039291, 5648370.17119344, 31469),
            array('G15', 5424561.64463159, 5648363.73004118, 31469),
            array('G17', 5424523.89648345, 5648354.51769550, 31469),
            array('G19', 5424490.49236822, 5648346.20411525, 31469),
            array('G21', 5424453.11870567, 5648335.86831278, 31469),
            array('G23', 5424440.19895257, 5648430.65061730, 31469)
        );

        $timeValues = array(
            array('date','G01','G04','G10','G11','G12','G13','G15','G17','G19','G21','G23'),
            array('02.06.2014',110.738,110.72,110.757,110.739,110.742,110.743,110.736,110.744,110.737,110.734,110.74),
            array('03.06.2014',110.658,110.64,110.672,110.659,110.657,110.648,110.646,110.649,110.677,110.634,110.66),
            array('04.06.2014',110.563,110.54,110.577,110.564,110.562,110.553,110.551,110.554,110.552,110.544,110.575),
            array('05.06.2014',110.498,110.46,110.512,110.484,110.487,110.478,110.476,110.479,110.497,110.474,110.51),
            array('06.06.2014',110.428,110.41,110.442,110.429,110.427,110.423,110.421,110.419,110.417,110.409,110.44),
            array('10.06.2014',109.863,109.825,109.877,109.834,109.847,109.843,109.821,109.829,109.817,109.819,109.89),
            array('11.06.2014',109.783,109.745,109.792,109.754,109.762,109.763,109.746,109.754,109.752,109.749,109.815),
            array('12.06.2014',109.673,109.63,109.687,109.649,109.652,109.653,109.631,109.634,109.632,109.634,109.71),
            array('13.06.2014',109.623,109.59,109.647,109.599,109.602,109.613,109.581,109.584,109.587,109.584,109.655),
            array('16.06.2014',109.443,109.41,109.462,109.429,109.427,109.433,109.411,109.419,109.417,109.419,109.475),
            array('17.06.2014',109.418,109.385,109.432,109.399,109.407,109.408,109.391,109.399,109.387,109.394,109.445),
            array('18.06.2014',109.403,109.37,109.422,109.389,109.392,109.393,109.376,109.384,109.382,109.384,109.43),
            array('19.06.2014',109.383,109.35,109.397,109.359,109.372,109.373,109.356,109.364,109.362,109.364,109.41),
            array('20.06.2014',109.378,109.34,109.392,109.364,109.362,109.363,109.351,109.359,109.352,109.354,109.4),
            array('23.06.2014',109.303,109.265,109.317,109.289,109.292,109.293,109.276,109.284,109.277,109.279,109.335),
            array('24.06.2014',109.323,109.29,109.342,109.319,109.317,109.318,109.306,109.269,109.307,109.309,109.35),
            array('25.06.2014',109.318,109.285,109.337,109.309,109.312,109.313,109.296,109.264,109.297,109.299,109.345),
            array('26.06.2014',109.338,109.31,109.352,109.339,109.337,109.338,109.326,109.334,109.332,109.329,109.36),
            array('27.06.2014',109.403,109.385,109.417,109.404,109.407,109.408,109.396,109.399,109.397,109.399,109.42),
            array('30.06.2014',109.398,109.37,109.412,109.394,109.397,109.388,109.386,109.389,109.382,109.384,109.415),
            array('01.07.2014',109.398,109.37,109.412,109.394,109.397,109.398,109.386,109.394,109.387,109.389,109.415),
            array('02.07.2014',109.383,109.355,109.402,109.389,109.382,109.383,109.371,109.379,109.377,109.369,109.41),
            array('03.07.2014',109.388,109.36,109.402,109.379,109.382,109.383,109.376,109.379,109.367,109.374,109.405),
            array('07.07.2014',109.293,109.26,109.307,109.284,109.287,109.288,109.271,109.279,109.277,109.274,109.32),
            array('08.07.2014',109.283,109.25,109.297,109.274,109.277,109.273,109.261,109.269,109.262,109.264,109.305),
            array('09.07.2014',109.383,109.355,109.402,109.389,109.382,109.383,109.371,109.379,109.377,109.369,109.41),
            array('10.07.2014',109.583,109.57,109.587,109.604,109.597,109.593,109.606,109.609,109.587,109.594,109.575),
            array('11.07.2014',109.718,109.705,109.722,109.739,109.737,109.728,109.741,109.739,109.732,109.724,109.71),
            array('14.07.2014',109.663,109.63,109.682,109.649,109.652,109.653,108.766,109.269,109.462,109.634,109.685),
            array('15.07.2014',109.583,109.555,109.602,109.569,109.577,109.578,109.561,109.569,109.562,109.564,109.61),
            array('16.07.2014',109.548,109.51,109.562,109.534,109.532,109.538,109.516,109.529,109.527,109.519,109.57),
            array('17.07.2014',109.488,109.455,109.502,109.474,109.477,109.478,109.461,109.469,109.462,109.464,109.515),
            array('18.07.2014',109.433,109.405,109.452,109.424,109.427,109.423,109.411,109.419,109.402,109.404,109.46),
            array('21.07.2014',109.358,109.33,109.372,109.344,109.352,109.353,109.336,109.344,109.337,109.339,109.385),
            array('22.07.2014',109.343,109.31,109.357,109.334,109.332,109.333,109.321,109.329,109.322,109.319,109.37),
            array('23.07.2014',109.503,109.49,109.602,109.524,109.517,109.518,109.526,109.524,109.477,109.549,109.5),
            array('24.07.2014',109.568,109.545,109.582,109.569,109.572,109.568,109.566,109.569,109.562,109.554,109.58),
            array('25.07.2014',109.488,109.54,109.582,109.569,109.572,109.568,109.556,109.569,109.562,109.514,109.575),
            array('26.07.2014',109.543,109.52,109.557,109.544,109.537,109.538,109.536,109.534,109.532,109.534,109.56),
            array('28.07.2014',109.473,109.445,109.487,109.464,109.467,109.468,109.456,109.459,109.457,109.459,109.495),
            array('29.07.2014',109.463,109.435,109.477,109.454,109.457,109.463,109.451,109.449,109.447,109.449,109.48),
            array('30.07.2014',109.498,109.475,109.512,109.499,109.497,109.498,109.486,109.499,109.492,109.489,109.51),
            array('31.07.2014',109.543,109.52,109.557,109.544,109.542,109.543,109.536,109.539,109.537,109.529,109.56),
            array('01.08.2014',109.478,109.45,109.497,109.469,109.472,109.473,109.461,109.464,109.457,109.464,109.505),
            array('02.08.2014',109.523,109.495,109.532,109.519,109.522,109.518,109.516,109.519,109.517,109.514,109.535),
            array('04.08.2014',109.548,109.525,109.557,109.554,109.557,109.553,109.551,109.559,109.557,109.559,109.565),
            array('05.08.2014',109.543,109.52,109.552,109.544,109.547,109.543,109.536,109.544,109.532,109.539,109.56),
            array('06.08.2014',109.623,109.59,109.627,109.624,109.622,109.623,109.626,109.624,109.622,109.614,109.625),
            array('07.08.2014',109.663,109.64,109.677,109.669,109.667,109.668,null,109.664,109.662,109.659,109.67),
            array('08.08.2014',109.588,109.56,109.602,109.579,109.582,109.583,null,109.579,109.567,109.569,109.615),
            array('09.08.2014',109.513,109.48,109.527,109.499,109.502,109.503,null,null,109.492,109.489,109.535),
            array('10.08.2014',109.428,109.445,109.487,109.464,109.467,109.463,null,null,109.452,109.454,109.495),
            array('11.08.2014',109.423,109.395,109.437,109.409,109.412,109.413,null,null,109.397,109.399,109.445),
            array('12.08.2014',109.408,109.38,109.417,109.399,109.402,109.403,null,null,109.397,109.394,109.425),
            array('13.08.2014',109.443,109.415,109.452,109.434,109.437,109.438,null,null,109.432,109.424,109.46),
            array('14.08.2014',109.413,109.385,109.422,109.404,109.407,109.408,null,null,109.397,109.399,109.43),
            array('15.08.2014',109.408,109.38,109.422,109.399,109.407,109.398,null,null,109.392,109.394,109.42),
            array('16.08.2014',109.438,109.41,109.447,109.429,109.432,109.433,null,null,109.422,109.424,109.455),
            array('17.08.2014',109.433,109.41,109.362,109.439,109.437,109.433,null,null,109.427,109.429,109.445),
            array('18.08.2014',109.448,109.42,109.462,109.449,109.447,109.448,null,null,109.437,109.439,109.465),
            array('19.08.2014',109.463,109.435,109.477,109.459,109.462,109.463,null,null,109.452,109.454,109.48),
            array('20.08.2014',109.448,109.42,109.457,109.444,109.437,109.443,null,null,109.442,109.434,109.46),
            array('21.08.2014',109.423,109.405,109.442,109.434,109.427,109.513,109.421,109.429,109.422,109.409,109.45),
            array('25.08.2014',109.378,109.35,109.392,109.369,109.367,109.373,109.366,109.369,109.362,109.359,109.395),
            array('26.08.2014',109.398,109.375,109.412,109.389,109.387,109.383,109.386,109.394,109.377,109.384,109.41),
            array('27.08.2014',109.408,109.38,109.417,109.404,109.397,109.398,109.401,109.409,109.397,109.399,109.42),
            array('28.08.2014',109.428,109.41,109.437,109.429,109.427,109.433,109.426,109.434,109.427,109.424,109.44),
            array('29.08.2014',109.453,109.45,109.462,109.479,109.482,109.453,109.476,109.474,109.432,109.454,109.42),
            array('30.08.2014',109.503,109.48,109.517,109.504,109.502,109.503,109.501,109.509,109.497,109.499,109.52),
            array('01.09.2014',109.513,109.49,109.527,109.509,109.512,109.513,109.506,109.509,109.502,109.504,109.53),
            array('02.09.2014',109.553,109.535,109.567,109.564,109.562,109.553,109.561,109.564,109.557,109.554,109.56),
            array('03.09.2014',109.658,109.645,109.672,109.679,109.677,109.673,109.676,109.679,109.677,109.669,109.66),
            array('04.09.2014',109.788,109.785,109.797,109.819,109.797,109.803,109.821,109.819,109.817,109.799,109.775),
            array('05.09.2014',109.823,109.81,109.832,109.834,109.817,109.843,109.836,109.839,109.832,109.834,109.805),
            array('09.09.2014',109.823,109.81,109.837,109.839,109.822,109.843,109.836,109.839,109.837,109.829,109.825),
            array('10.09.2014',109.818,109.805,109.837,109.829,109.827,109.818,109.821,109.829,109.822,109.824,109.83),
            array('11.09.2014',109.833,109.82,109.852,109.844,109.842,109.838,109.841,109.844,109.837,109.829,109.845),
            array('12.09.2014',109.813,109.81,109.847,109.834,109.832,109.833,109.831,109.839,109.822,109.814,109.83),
            array('15.09.2014',110.278,110.255,110.267,110.284,110.262,110.263,110.286,110.284,110.282,110.269,110.23),
            array('16.09.2014',110.323,110.29,110.312,110.339,110.332,110.313,110.341,110.344,110.337,110.324,110.285),
            array('17.09.2014',110.433,110.43,110.437,110.464,110.462,110.448,110.466,110.474,110.467,110.454,110.415),
            array('18.09.2014',110.388,110.385,110.412,110.409,110.407,110.403,110.416,110.419,110.417,110.404,110.41),
            array('22.09.2014',110.383,110.38,110.392,110.394,110.397,110.388,110.396,110.399,110.382,110.374,110.38),
            array('23.09.2014',110.413,110.41,110.427,110.424,110.432,110.423,110.431,110.439,110.432,110.424,110.41),
            array('24.09.2014',110.443,110.435,110.457,110.459,110.457,110.468,110.456,110.459,110.457,110.444,110.445),
            array('25.09.2014',110.43,110.425,110.447,110.446,110.444,110.453,110.441,110.449,110.444,110.429,110.43),
            array('26.09.2014',110.418,110.415,110.437,110.434,110.432,110.438,110.426,110.439,110.432,110.414,110.415),
            array('29.09.2014',110.238,110.225,110.267,110.229,110.242,110.233,110.221,110.229,110.227,110.219,110.26),
            array('30.09.2014',110.238,110.225,110.252,110.254,110.252,110.243,110.251,110.254,110.247,110.239,110.245),
            array('01.10.2014',110.243,110.215,110.247,110.239,110.237,110.228,110.236,110.239,110.232,110.224,110.235),
            array('02.10.2014',110.223,110.2,110.237,110.224,110.227,110.223,110.216,110.219,110.212,110.204,110.225),
            array('06.10.2014',110.238,110.23,110.257,110.249,110.237,110.263,110.246,110.249,110.237,110.234,110.26),
            array('07.10.2014',110.233,110.215,110.257,110.229,110.232,110.223,110.211,110.219,110.212,110.204,110.255),
            array('08.10.2014',110.008,109.98,110.037,109.989,109.992,110.003,109.976,109.984,109.977,109.981,110.055),
            array('09.10.2014',109.923,109.875,109.922,109.889,109.902,109.893,109.876,109.874,109.872,109.874,109.935),
            array('10.10.2014',109.918,109.87,109.922,109.889,109.897,109.893,109.881,109.879,109.872,109.874,109.92),
            array('13.10.2014',109.828,109.795,109.802,109.824,109.822,109.823,109.816,109.819,109.817,109.809,109.84),
            array('14.10.2014',109.878,109.835,109.872,109.859,109.857,109.853,109.846,109.849,109.847,109.844,109.865),
            array('15.10.2014',109.953,109.91,109.942,109.939,109.937,109.933,109.936,109.939,109.932,109.929,109.935),
            array('16.10.2014',109.953,109.93,109.967,109.949,109.957,109.953,109.951,109.959,109.947,109.949,109.96),
            array('17.10.2014',110.023,109.985,110.017,110.019,110.012,110.008,110.011,110.014,110.012,110.004,110.005),
            array('21.10.2014',110.168,110.155,110.182,110.179,110.177,110.173,110.176,110.179,110.177,110.169,110.26),
            array('22.10.2014',110.158,110.15,110.177,110.169,110.167,110.163,110.166,110.169,110.167,110.164,110.17),
            array('23.10.2014',110.263,110.26,110.277,110.289,110.2845,110.278,110.2935,110.2965,110.292,110.2815,110.26),
            array('24.10.2014',110.368,110.37,110.377,110.409,110.402,110.393,110.421,110.424,110.417,110.399,110.35),
            array('27.10.2014',110.743,110.73,110.727,110.759,110.767,110.753,110.776,110.759,110.767,110.734,110.72),
            array('28.10.2014',110.708,110.715,110.752,110.749,110.742,110.733,110.746,110.544,110.737,110.729,110.715),
            array('29.10.2014',110.723,110.725,110.727,110.739,110.702,110.713,110.726,null,110.727,110.714,110.71),
            array('30.10.2014',110.628,110.63,110.657,110.639,110.657,110.643,110.636,110.639,110.627,110.624,110.64),
            array('01.11.2014',null,null,110.492,110.464,110.472,110.463,110.456,null,null,110.444,110.48),
            array('03.11.2014',null,null,110.297,110.259,110.257,110.263,110.176,null,null,110.234,110.31),
            array('04.11.2014',null,null,110.297,110.189,110.167,110.143,110.136,null,null,110.114,110.29),
            array('05.11.2014',null,null,110.177,110.149,110.167,110.153,110.136,null,null,110.139,110.185),
            array('06.11.2014',null,null,110.127,110.094,110.102,110.098,110.086,null,null,110.084,110.12),
            array('07.11.2014',null,null,110.062,110.034,110.037,110.033,110.026,null,null,110.019,110.065),
            array('10.11.2014',null,null,109.997,109.979,109.982,109.978,109.971,null,null,109.974,110),
            array('11.11.2014',null,null,110.002,109.989,109.987,109.983,109.976,null,null,109.974,110),
            array('12.11.2014',null,null,110.032,109.014,110.022,110.013,110.011,null,null,110.004,110.03),
            array('13.11.2014',null,null,110.037,110.029,110.027,110.023,110.011,null,null,110.014,110.04),
            array('14.11.2014',null,null,110.012,109.989,109.997,109.993,109.986,null,null,109.984,110.015),
            array('17.11.2014',null,null,110.007,109.939,109.967,109.923,109.961,null,null,109.954,109.99),
            array('18.11.2014',null,null,109.977,109.969,109.957,109.963,109.956,null,null,109.944,109.97),
            array('20.11.2014',null,null,110.097,110.099,110.097,110.083,110.091,null,null,110.079,110.085),
            array('21.11.2014',null,null,110.147,110.149,110.147,110.143,110.151,null,null,110.139,110.14),
            array('24.11.2014',null,null,110.057,110.039,110.037,110.033,110.026,null,null,109.984,110.07),
            array('25.11.2014',null,null,110.017,109.989,109.997,109.993,109.981,null,null,109.974,110.02),
            array('26.11.2014',null,null,109.997,109.979,109.977,109.978,109.966,null,null,109.964,109.995),
            array('27.11.2014',null,null,109.982,109.959,109.972,109.963,109.956,null,null,109.954,109.99),
            array('28.11.2014',null,null,109.952,109.859,109.937,109.913,109.926,null,null,109.919,109.955),
            array('01.12.2014',null,null,109.817,109.739,109.687,109.763,109.756,null,null,109.774,109.82),
            array('02.12.2014',null,null,109.807,109.789,109.787,109.793,109.791,null,null,109.784,109.81),
            array('03.12.2014',null,null,109.867,109.854,109.857,109.853,109.851,null,null,109.844,109.86),
            array('04.12.2014',null,null,109.857,109.839,109.842,109.838,109.831,null,null,109.824,109.86),
        );

        $header = null;
        foreach ($wells as $wellData) {
            if (is_null($header)){
                $header = $wellData;
                continue;
            }

            $wellData = array_combine($header, $wellData);
            echo "Add Observation-Well ".$wellData['name']."\r\n";
            $well = WellBoundaryFactory::create()
                ->setWellType(WellBoundary::TYPE_OBSERVATION_WELL)
                ->setName($wellData['name'])
                ->setOwner($user)
                ->setPublic($public)
                ->setPoint(new Point($wellData['x'], $wellData['y'], $wellData['srid']));

            $tvHeader = null;
            foreach ($timeValues as $timeValue){
                if (is_null($tvHeader)){
                    $tvHeader = $timeValue;
                    continue;
                }

                $timeValue = array_combine($tvHeader, $timeValue);

                if (!is_null($timeValue[$wellData['name']])){
                    $well->addValue($propertyTypeGwHead, PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime($timeValue['date']), $timeValue[$wellData['name']]));
                }
            }

            $model->addBoundary($well);
            $entityManager->flush();
        }

        return 1;
    }
}