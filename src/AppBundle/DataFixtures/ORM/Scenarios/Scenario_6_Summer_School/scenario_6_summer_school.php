<?php

namespace AppBundle\DataFixtures\ORM\Scenarios\Scenario_6_Summer_School;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\StreamBoundary;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\GridSize;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\Point;
use AppBundle\Model\PropertyTimeValueFactory;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\StreamBoundaryFactory;
use AppBundle\Model\StressPeriodFactory;
use AppBundle\Model\WellBoundaryFactory;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inowas\PyprocessingBundle\Service\Interpolation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadScenario_6 implements FixtureInterface, ContainerAwareInterface
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


        $userManager = $this->container->get('fos_user.user_manager');

        $public = true;
        $username = 'inowas';
        $email = 'inowas@inowas.com';
        $password = 'inowas';

        $user = $userManager->findUserByUsername($username);

        if (!$user) {
            // Add new User
            $user = $userManager->createUser();
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPlainPassword($password);
            $user->setEnabled(true);
            $userManager->updateUser($user);
        }

        $geoTools = $this->container->get('inowas.geotools');


        // Load PropertyTypes
        $propertyTypeTopElevation = PropertyTypeFactory::create(PropertyType::TOP_ELEVATION);
        $propertyTypeBottomElevation = PropertyTypeFactory::create(PropertyType::BOTTOM_ELEVATION);
        $propertyTypeHydraulicConductivity = PropertyTypeFactory::create(PropertyType::HYDRAULIC_CONDUCTIVITY);
        $propertyTypeRiverStage = PropertyTypeFactory::create(PropertyType::RIVER_STAGE);

        $model = ModFlowModelFactory::create()
            ->setName("Inowas Rio Primero")
            ->setOwner($user)
            ->setPublic($public)
            ->setArea(AreaFactory::create()
                ->setName('Rio Primero Area')
                ->setOwner($user)
                ->setPublic($public)
                ->setAreaType('SC6_AT1')
                ->setGeometry(new Polygon(array(
                    array(
                        array(-63.65374923159833, -31.364459841376334),
                        array(-63.65374923159833, -31.318130051738194),
                        array(-63.57684493472333, -31.318130051738194),
                        array(-63.57684493472333, -31.364459841376334),
                        array(-63.65374923159833, -31.364459841376334)
                    )), 4326)))
            ->setSoilModel(SoilModelFactory::create()
                ->setName('Soilmodel_Scenario_6')
                ->setPublic($public)
                ->setOwner($user)
            )
            ->setGridSize(new GridSize(75, 40))
            ->setBoundingBox($geoTools->transformBoundingBox(new BoundingBox(-63.65374923159833, -63.57684493472333, -31.364459841376334, -31.318130051738194, 4326), 4326))
            ->addStressPeriod(StressPeriodFactory::create()
                ->setDateTimeBegin(new \DateTime('1/1/2015'))
                ->setDateTimeEnd(new \DateTime('1/15/2015'))
                ->setNumberOfTimeSteps(2)
                ->setSteady(true)
            );

        $model->addBoundary(StreamBoundaryFactory::create()
            ->setGeometry(new LineString(array(
                array(-63.65373, -31.35192),
                array(-63.65364551602398, -31.351764794584323),
                array(-63.65124225674662, -31.349749064959326),
                array(-63.645467759342864, -31.347546983301353),
                array(-63.64392280695028, -31.346594055584216),
                array(-63.64006042596885, -31.342415720094518),
                array(-63.639030457707115, -31.341096207172697),
                array(-63.63791465875693, -31.340949593483458),
                array(-63.63413810846396, -31.341389433865768),
                array(-63.62941742059774, -31.34124282063347),
                array(-63.62778663751668, -31.34182927219181),
                array(-63.62658500787803, -31.34329538509414),
                array(-63.62607002374716, -31.345347904772055),
                array(-63.62598419305868, -31.346374147816963),
                array(-63.62461090204307, -31.34688726514081),
                array(-63.622636796208106, -31.347327077761967),
                array(-63.62160682794637, -31.348133395559536),
                array(-63.62134933588095, -31.34974601041793),
                array(-63.62134933588095, -31.351285298807873),
                array(-63.620491028996184, -31.352384775090155),
                array(-63.61937523004599, -31.352677966594495),
                array(-63.61834526178426, -31.352824562003878),
                array(-63.6169719707686, -31.3526046688041),
                array(-63.616285325260826, -31.351798389339223),
                array(-63.61499786493368, -31.351358597626604),
                array(-63.61285209772177, -31.351798389339223),
                array(-63.611049653263755, -31.351065402008917),
                array(-63.60898971674032, -31.34908630768142),
                array(-63.60753059503622, -31.34747368151201),
                array(-63.60555648920126, -31.346154239535593),
                array(-63.60495567438193, -31.344028432977225),
                array(-63.6050415050704, -31.342928859010566),
                array(-63.60753059503622, -31.341096207172697),
                array(-63.6095905315596, -31.3391902113917),
                array(-63.60873222467489, -31.337650725073754),
                array(-63.60787391779013, -31.336037902868117),
                array(-63.60658645746298, -31.334864923901513),
                array(-63.60452652093954, -31.33471830050265),
                array(-63.602552415104576, -31.335451415212418),
                array(-63.60160827753134, -31.33691762749804),
                array(-63.60006332513876, -31.338237199022036),
                array(-63.59826088068075, -31.338383816937952),
                array(-63.596029282780364, -31.338677052084403),
                array(-63.59534263727256, -31.33772403451719),
                array(-63.595771790714934, -31.336184524210914),
                array(-63.595771790714934, -31.334864923901513),
                array(-63.59508514520712, -31.333691930314163),
                array(-63.59422683832235, -31.33273886225903),
                array(-63.59276771661825, -31.33251892210642),
                array(-63.59148025629110, -31.33347199238887),
                array(-63.59096527216024, -31.33493823551526),
                array(-63.59079361078329, -31.33647776621113),
                array(-63.59019279596396, -31.337870653232724),
                array(-63.58916282770224, -31.338237199022036),
                array(-63.58744621393271, -31.33860374338346),
                array(-63.58572960016317, -31.338310508008558),
                array(-63.584098817082115, -31.337504106015533),
                array(-63.582553864689544, -31.337504106015533),
                array(-63.5804939281661, -31.337577415573193),
                array(-63.5786914837081, -31.336257834796644),
                array(-63.57684, -31.33429)
            ), 4326))
            ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue(400))
            ->addValue($propertyTypeHydraulicConductivity, PropertyValueFactory::create()->setValue(100))
            ->addValue($propertyTypeRiverStage, PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime('2015-01-01'), 410))
            ->addValue($propertyTypeRiverStage, PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime('2015-02-01'), 411))
            ->addValue($propertyTypeRiverStage, PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime('2015-03-01'), 412))
            ->addValue($propertyTypeRiverStage, PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime('2015-04-01'), 408))
            ->addValue($propertyTypeRiverStage, PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime('2015-05-01'), 404))
            ->addValue($propertyTypeRiverStage, PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime('2015-06-01'), 412))
            ->addValue($propertyTypeRiverStage, PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime('2015-07-01'), 408))
            ->addValue($propertyTypeRiverStage, PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime('2015-08-01'), 403))
            ->addValue($propertyTypeRiverStage, PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime('2015-09-01'), 402))
            ->addValue($propertyTypeRiverStage, PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime('2015-10-01'), 401))
            ->addValue($propertyTypeRiverStage, PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime('2015-11-01'), 400))
            ->addValue($propertyTypeRiverStage, PropertyTimeValueFactory::createWithTimeAndValue(new \DateTime('2015-12-01'), 399))
        );

        $model->addBoundary(WellBoundaryFactory::createIndustrialWell()->setPoint(new Point(-63.63377, -31.32991, 4326)));
        $model->addBoundary(WellBoundaryFactory::createIndustrialWell()->setPoint(new Point(-63.60939, -31.33108, 4326)));
        $model->addBoundary(WellBoundaryFactory::createIndustrialWell()->setPoint(new Point(-63.61952, -31.34002, 4326)));
        $model->addBoundary(WellBoundaryFactory::createPrivateWell()->setPoint(new Point(-63.63651, -31.3541, 4326)));
        $model->addBoundary(WellBoundaryFactory::createPrivateWell()->setPoint(new Point(-63.64492, -31.3604, 4326)));
        $model->addBoundary(WellBoundaryFactory::createPrivateWell()->setPoint(new Point(-63.61008, -31.35849, 4326)));
        $model->addBoundary(WellBoundaryFactory::createPrivateWell()->setPoint(new Point(-63.58175, -31.35849, 4326)));
        $model->addBoundary(WellBoundaryFactory::createPrivateWell()->setPoint(new Point(-63.58416, -31.34427, 4326)));

        $entityManager->persist($model);
        $entityManager->flush();


        $model->setActiveCells($geoTools->getActiveCells($model->getArea(), $model->getBoundingBox(), $model->getGridSize()));
        foreach ($model->getModelObjects() as $mo){
            /** @var StreamBoundary $boundary */
            if ($mo instanceof StreamBoundary){
                $mo->setActiveCells($geoTools->getActiveCells($mo, $model->getBoundingBox(), $model->getGridSize()));
            }
        }

        $entityManager->persist($model);
        $entityManager->flush();

        $layer_1 = GeologicalLayerFactory::create()
            ->setName("Layer_1")
            ->setOrder(GeologicalLayer::TOP_LAYER)
            ->setOwner($user)
            ->setPublic($public);

        $model->getSoilModel()->addGeologicalLayer($layer_1);

        $geologicalPointProperties = array(
            array(new Point(-63.64698, -31.32741, 4326), 'GP1', 415, 362),
            array(new Point(-63.64630, -31.34237, 4326), 'GP2', 410, 360),
            array(new Point(-63.64544, -31.35967, 4326), 'GP3', 417, 365),
            array(new Point(-63.61591, -31.32404, 4326), 'GP4', 403, 362),
            array(new Point(-63.61420, -31.34383, 4326), 'GP5', 397, 364),
            array(new Point(-63.61506, -31.36011, 4326), 'GP6', 415, 362),
            array(new Point(-63.58536, -31.32653, 4326), 'GP7', 395, 363),
            array(new Point(-63.58261, -31.34266, 4326), 'GP8', 380, 332),
            array(new Point(-63.58459, -31.35573, 4326), 'GP9', 410, 350)
        );

        foreach ($geologicalPointProperties as $gpp){
            $model->getSoilModel()->addGeologicalPoint(GeologicalPointFactory::create()
                ->setName($gpp[1])
                ->setPoint($gpp[0])
                ->addGeologicalUnit(GeologicalUnitFactory::create()
                    ->setName($gpp[1].'.1')
                    ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue($gpp[2]))
                    ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue($gpp[3]))
                ));

            $entityManager->persist($model);
            $entityManager->flush();
        }

        foreach ($model->getSoilModel()->getGeologicalUnits() as $geologicalUnit){
            $layer_1->addGeologicalUnit($geologicalUnit);
        }

        $entityManager->persist($model);
        $entityManager->flush();

        /* Interpolation of all layers */
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
                    array(Interpolation::TYPE_IDW, Interpolation::TYPE_MEAN)
                );

                echo ($output);
            }
        }

        return 1;
    }
}