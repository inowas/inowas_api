<?php

namespace AppBundle\DataFixtures\ORM\Scenarios\Scenario_6_Summer_School;

use AppBundle\Entity\AddBoundaryEvent;
use AppBundle\Entity\GeologicalLayer;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\EventFactory;
use AppBundle\Model\GeneralHeadBoundaryFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\GridSize;
use AppBundle\Model\ModelScenarioFactory;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\Point;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\RechargeBoundaryFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\StreamBoundaryFactory;
use AppBundle\Model\StressPeriodFactory;
use AppBundle\Model\WellBoundaryFactory;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy2DArray;
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
        $geoTools = $this->container->get('inowas.geotools');
        $userManager = $this->container->get('fos_user.user_manager');

        $userListHeads = array('username', 'email', 'password');
        $userList = array(
            array('inowas', 'inowas@inowas.com', 'inowas'),
            array('ralf.junghanns', 'ralf.junghanns@tu-dresden.de', 'inowas'),
            array('jana.ringleb', 'jana.ringleb@tu-dresden.de', 'inowas'),
            array('jana.sallwey', 'jana.sallwey@tu-dresden.de', 'inowas'),
            array('catalin.stefan', 'catalin.stefan@tu-dresden.de', 'inowas'),
            array('aakanni', 'aakanni@gast', 'ahcoecie'),
            array('akhalil', 'akhalil@gast', 'oneiraem'),
            array('akamamia', 'akamamia@gast', 'thiezozo'),
            array('cteusch', 'cteusch@gast', 'ieshaing'),
            array('fworku', 'fworku@gast', 'soopeich'),
            array('fluna', 'fluna@gast', 'dahshuoy'),
            array('fwalter', 'fwalter@gast', 'quohhoev'),
            array('glabrecque', 'glabrecque@gast', 'fuaquaeg'),
            array('hzhang', 'hzhang@gast', 'xahtiech'),
            array('ahoang', 'ahoang@gast', 'jiichaer'),
            array('kwilson', 'kwilson@gast', 'aitaichi'),
            array('lstaedtler', 'lstaedtler@gast', 'eediegha'),
            array('mhaeberle', 'mhaeberle@gast', 'peehaida'),
            array('mvillanueva', 'mvillanueva@gast', 'eereinoh'),
            array('mabbaker', 'mabbaker@gast', 'iegeicos'),
            array('mmahmood', 'mmahmood@gast', 'quegaing'),
            array('onegoita', 'onegoita@gast', 'jeibohgh'),
            array('obrowne', 'obrowne@gast', 'mohsohhi'),
            array('rshandilya', 'rshandilya@gast', 'zeicicei'),
            array('rbengul', 'rbengul@gast', 'oocohcis'),
            array('skazemi', 'skazemi@gast', 'eghaeshu'),
            array('ssantato', 'ssantato@gast', 'laeseque'),
            array('sghahfarokhi', 'sghahfarokhi@gast', 'leiceequ'),
            array('sjimenez', 'sjimenez@gast', 'oongaeno'),
            array('vhoecker', 'vhoecker@gast', 'weupohng'),
            array('yzhou', 'yzhou@gast', 'moojieyi')
        );

        foreach ($userList as $item){
            $item = array_combine($userListHeads, $item);

            $user = $userManager->findUserByUsername($item['username']);
            if (!$user) {
                // Add new User
                $user = $userManager->createUser();
                $user->setUsername($item['username']);
                $user->setEmail($item['email']);
                $user->setPlainPassword($item['password']);
                $user->setEnabled(true);
                $userManager->updateUser($user);
            }
        }

        $user = $userManager->findUserByUsername('inowas');
        $user->addRole('ROLE_ADMIN');
        $userManager->updateUser($user);
        $public = true;

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
                        array(-63.687336, -31.313615),
                        array(-63.687336, -31.367449),
                        array(-63.569260, -31.367449),
                        array(-63.569260, -31.313615),
                        array(-63.687336, -31.313615)
                    )), 4326)))
            ->setSoilModel(SoilModelFactory::create()
                ->setName('Soilmodel_Scenario_6')
                ->setPublic($public)
                ->setOwner($user)
            )
            ->setGridSize(new GridSize(75, 40))
            ->setBoundingBox($geoTools->transformBoundingBox(
                new BoundingBox(-63.569260, -63.687336, -31.313615, -31.367449, 4326), 4326)
            )
        ;

        $layer_1 = GeologicalLayerFactory::create()
            ->setName("Layer_1")
            ->setOrder(GeologicalLayer::TOP_LAYER)
            ->setOwner($user)
            ->setPublic($public);

        $model->getSoilModel()->addGeologicalLayer($layer_1);

        $geologicalPointProperties = array(
            array(new Point(-63.64698, -31.32741, 4326), 'GP1', 465, 392),
            array(new Point(-63.64630, -31.34237, 4326), 'GP2', 460, 390),
            array(new Point(-63.64544, -31.35967, 4326), 'GP3', 467, 395),
            array(new Point(-63.61591, -31.32404, 4326), 'GP4', 463, 392),
            array(new Point(-63.61420, -31.34383, 4326), 'GP5', 463, 394),
            array(new Point(-63.61506, -31.36011, 4326), 'GP6', 465, 392),
            array(new Point(-63.58536, -31.32653, 4326), 'GP7', 465, 393),
            array(new Point(-63.58261, -31.34266, 4326), 'GP8', 460, 392),
            array(new Point(-63.58459, -31.35573, 4326), 'GP9', 460, 390)
        );

        foreach ($geologicalPointProperties as $gpp){
            $model->getSoilModel()->addGeologicalPoint(GeologicalPointFactory::create()
                ->setName($gpp[1])
                ->setPoint($gpp[0])
                ->addGeologicalUnit(GeologicalUnitFactory::create()
                    ->setName($gpp[1].'.1')
                    ->addValue(PropertyTypeFactory::create(PropertyType::TOP_ELEVATION), PropertyValueFactory::create()->setValue($gpp[2]))
                    ->addValue(PropertyTypeFactory::create(PropertyType::BOTTOM_ELEVATION), PropertyValueFactory::create()->setValue($gpp[3]))
                    ->addValue(PropertyTypeFactory::create(PropertyType::KX), PropertyValueFactory::create()->setValue(10))
                    ->addValue(PropertyTypeFactory::create(PropertyType::KY), PropertyValueFactory::create()->setValue(10))
                    ->addValue(PropertyTypeFactory::create(PropertyType::KZ), PropertyValueFactory::create()->setValue(1))
                ));

            $entityManager->persist($model);
            $entityManager->flush();
        }
        foreach ($model->getSoilModel()->getGeologicalUnits() as $geologicalUnit){
            $layer_1->addGeologicalUnit($geologicalUnit);
        }

        $entityManager->persist($model);
        $entityManager->flush();

        /** Constant Head West */
        $model->addBoundary(GeneralHeadBoundaryFactory::create()
            ->setOwner($user)
            ->setPublic($public)
            ->setGeometry(new LineString(array(
                array($model->getBoundingBox()->getXMin(), $model->getBoundingBox()->getYMin()),
                array($model->getBoundingBox()->getXMin(), $model->getBoundingBox()->getYMax())
                ), $model->getBoundingBox()->getSrid())
            )
            ->addStressPeriod(StressPeriodFactory::createGhb()
                ->setDateTimeBegin(new \DateTime('1.1.2015'))
                ->setDateTimeEnd(new \DateTime('31.12.2015'))
                ->setStage(450)
                ->setCond(100)
            )
        );

        /** Constant Head East */
        $model->addBoundary(GeneralHeadBoundaryFactory::create()
            ->setOwner($user)
            ->setPublic($public)
            ->setGeometry(new LineString(array(
                    array($model->getBoundingBox()->getXMax(), $model->getBoundingBox()->getYMin()),
                    array($model->getBoundingBox()->getXMax(), $model->getBoundingBox()->getYMax())
                ), $model->getBoundingBox()->getSrid())
            )
            ->addStressPeriod(StressPeriodFactory::createGhb()
                ->setDateTimeBegin(new \DateTime('1.1.2015'))
                ->setDateTimeEnd(new \DateTime('31.12.2015'))
                ->setStage(440)
                ->setCond(100)
            )
        );

        /** River */
        $model->addBoundary(StreamBoundaryFactory::create()
            ->setOwner($user)
            ->setPublic($public)
            ->setGeometry(new LineString(array(
                array(-63.676586151123,-31.367415770489),
                array(-63.673968315125,-31.366206539217),
                array(-63.67280960083,-31.364704139298),
                array(-63.67169380188,-31.363788030001),
                array(-63.670706748962,-31.363641451685),
                array(-63.669762611389,-31.364154474791),
                array(-63.668003082275,-31.365070580517),
                array(-63.666973114014,-31.364814071814),
                array(-63.666501045227,-31.363788030001),
                array(-63.664870262146,-31.362248946282),
                array(-63.662981987,-31.360783128836),
                array(-63.661994934082,-31.35942722735),
                array(-63.66156578064,-31.357741484721),
                array(-63.661437034607,-31.355835826222),
                array(-63.66014957428,-31.353123861001),
                array(-63.658862113953,-31.352500830916),
                array(-63.656415939331,-31.352061042488),
                array(-63.654913902283,-31.352354235002),
                array(-63.653645516024,-31.351764794584),
                array(-63.651242256747,-31.349749064959),
                array(-63.645467759343,-31.347546983301),
                array(-63.64392280695,-31.346594055584),
                array(-63.640060425969,-31.342415720095),
                array(-63.639030457707,-31.341096207173),
                array(-63.637914658757,-31.340949593483),
                array(-63.634138108464,-31.341389433866),
                array(-63.629417420598,-31.341242820633),
                array(-63.627786637517,-31.341829272192),
                array(-63.626585007878,-31.343295385094),
                array(-63.626070023747,-31.345347904772),
                array(-63.625984193059,-31.346374147817),
                array(-63.624610902043,-31.346887265141),
                array(-63.622636796208,-31.347327077762),
                array(-63.621606827946,-31.34813339556),
                array(-63.621349335881,-31.349746010418),
                array(-63.621349335881,-31.351285298808),
                array(-63.620491028996,-31.35238477509),
                array(-63.619375230046,-31.352677966594),
                array(-63.618345261784,-31.352824562004),
                array(-63.616971970769,-31.352604668804),
                array(-63.616285325261,-31.351798389339),
                array(-63.614997864934,-31.351358597627),
                array(-63.612852097722,-31.351798389339),
                array(-63.611049653264,-31.351065402009),
                array(-63.60898971674,-31.349086307681),
                array(-63.607530595036,-31.347473681512),
                array(-63.605556489201,-31.346154239536),
                array(-63.604955674382,-31.344028432977),
                array(-63.60504150507,-31.342928859011),
                array(-63.607530595036,-31.341096207173),
                array(-63.60959053156,-31.339190211392),
                array(-63.608732224675,-31.337650725074),
                array(-63.60787391779,-31.336037902868),
                array(-63.606586457463,-31.334864923902),
                array(-63.60452652094,-31.334718300503),
                array(-63.602552415105,-31.335451415212),
                array(-63.601608277531,-31.336917627498),
                array(-63.600063325139,-31.338237199022),
                array(-63.598260880681,-31.338383816938),
                array(-63.59602928278,-31.338677052084),
                array(-63.595342637273,-31.337724034517),
                array(-63.595771790715,-31.336184524211),
                array(-63.595771790715,-31.334864923902),
                array(-63.595085145207,-31.333691930314),
                array(-63.594226838322,-31.332738862259),
                array(-63.592767716618,-31.332518922106),
                array(-63.591480256291,-31.333471992389),
                array(-63.59096527216,-31.334938235515),
                array(-63.590793610783,-31.336477766211),
                array(-63.590192795964,-31.337870653233),
                array(-63.589162827702,-31.338237199022),
                array(-63.587446213933,-31.338603743383),
                array(-63.585729600163,-31.338310508009),
                array(-63.584098817082,-31.337504106016),
                array(-63.58255386469,-31.337504106016),
                array(-63.580493928166,-31.337577415573),
                array(-63.578691483708,-31.336257834797),
                array(-63.576998711214,-31.334611387837),
                array(-63.575305938721,-31.33296491207),
                array(-63.572559356689,-31.332231777991),
                array(-63.569641113281,-31.331205380684)
            ), 4326))
            ->addStressPeriod(StressPeriodFactory::createRiv()
                ->setDateTimeBegin(new \DateTime('1.1.2015'))
                ->setDateTimeEnd(new \DateTime('31.12.2015'))
                ->setStage(446)
                ->setCond(200)
                ->setRbot(444)
            )
        );

        /** Irrigation Well 1 */
        $model->addBoundary(WellBoundaryFactory::createIndustrialWell()
            ->setName('Irrigation Well 1')
            ->setGeometry(new Point(-63.671125, -31.325009, 4326))
            ->setLayer($layer_1)
            ->addStressPeriod(StressPeriodFactory::createWel()
                ->setDateTimeBegin(new \DateTime('1.1.2015'))
                ->setDateTimeEnd(new \DateTime('31.12.2015'))
                ->setFlux(-5000)
            )
        );

        /** Irrigation Well 2 */
        $model->addBoundary(WellBoundaryFactory::createIndustrialWell()
            ->setName('Irrigation Well 2')
            ->setGeometry(new Point(-63.659952, -31.330144, 4326))
            ->setLayer($layer_1)
            ->addStressPeriod(StressPeriodFactory::createWel()
                ->setDateTimeBegin(new \DateTime('1.1.2015'))
                ->setDateTimeEnd(new \DateTime('31.12.2015'))
                ->setFlux(-5000)
            )
        );

        /** Irrigation Well 3 */
        $model->addBoundary(WellBoundaryFactory::createIndustrialWell()
            ->setName('Irrigation Well 3')
            ->setGeometry(new Point(-63.674691, -31.342506, 4326))
            ->setLayer($layer_1)
            ->addStressPeriod(StressPeriodFactory::createWel()
                ->setDateTimeBegin(new \DateTime('1.1.2015'))
                ->setDateTimeEnd(new \DateTime('31.12.2015'))
                ->setFlux(-5000)
            )
        );

        /** Irrigation Well 4 */
        $model->addBoundary(WellBoundaryFactory::createIndustrialWell()
            ->setName('Irrigation Well 4')
            ->setGeometry(new Point(-63.637379, -31.359613, 4326))
            ->setLayer($layer_1)
            ->addStressPeriod(StressPeriodFactory::createWel()
                ->setDateTimeBegin(new \DateTime('1.1.2015'))
                ->setDateTimeEnd(new \DateTime('31.12.2015'))
                ->setFlux(-5000)
            )
        );

        /** Irrigation Well 5 */
        $model->addBoundary(WellBoundaryFactory::createIndustrialWell()
            ->setName('Irrigation Well 5')
            ->setGeometry(new Point(-63.582069, -31.324063, 4326))
            ->setLayer($layer_1)
            ->addStressPeriod(StressPeriodFactory::createWel()
                ->setDateTimeBegin(new \DateTime('1.1.2015'))
                ->setDateTimeEnd(new \DateTime('31.12.2015'))
                ->setFlux(-5000)
            )
        );

        /** Public Well 1 */
        $model->addBoundary(WellBoundaryFactory::createPublicWell()
            ->setName('Public Well 1')
            ->setGeometry(new Point(-63.625402, -31.329897, 4326))
            ->setLayer($layer_1)
            ->addStressPeriod(StressPeriodFactory::createWel()
                ->setDateTimeBegin(new \DateTime('1.1.2015'))
                ->setDateTimeEnd(new \DateTime('31.12.2015'))
                ->setFlux(-5000)
            )
        );

        /** Public Well 2 */
        $model->addBoundary(WellBoundaryFactory::createPublicWell()
            ->setName('Public Well 2')
            ->setGeometry(new Point(-63.623027, -31.331184, 4326))
            ->setLayer($layer_1)
            ->addStressPeriod(StressPeriodFactory::createWel()
                ->setDateTimeBegin(new \DateTime('1.1.2015'))
                ->setDateTimeEnd(new \DateTime('31.12.2015'))
                ->setFlux(-5000)
            )
        );

        /** Recharge */
        $model->addBoundary(RechargeBoundaryFactory::create()
            ->setOwner($user)
            ->setName('RechargeBoundary')
            ->setPublic(true)
            ->setGeometry($model->getArea()->getGeometry())
            ->addStressPeriod(StressPeriodFactory::createRch()
                ->setDateTimeBegin(new \DateTime('1.1.2015'))
                ->setDateTimeEnd(new \DateTime('31.12.2015'))
                ->setRech(Flopy2DArray::fromValue(3.29e-4))
            )
        );

        $entityManager->persist($model);
        $entityManager->flush();

        $model->setActiveCells($geoTools->getActiveCells($model->getArea(), $model->getBoundingBox(), $model->getGridSize()));
        foreach ($model->getModelObjects() as $mo){
            #preg_match('/[^\\]+$/', get_class($mo), $classNames);
            echo sprintf("Set activeCells for %s %s\r\n",
                get_class($mo),
                $mo->getId());
            $mo->setActiveCells($geoTools->getActiveCells($mo, $model->getBoundingBox(), $model->getGridSize()));
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

                $soilModelService->interpolateLayerByProperty(
                    $layer,
                    $propertyType,
                    array(Interpolation::TYPE_IDW, Interpolation::TYPE_MEAN)
                );
            }
        }

        foreach ($userList as $item) {
            $item = array_combine($userListHeads, $item);
            $user = $userManager->findUserByUsername($item['username']);

            echo (sprintf("Generating Scenarios for User %s \r\n", $user->getUsername()));

            $scenario_0 = ModelScenarioFactory::create($model);
            $scenario_0->setOwner($user);
            $scenario_0->setPublic(false);
            $scenario_0->setName('Scenario 0: Rio Primero 2020');
            $scenario_0->setDescription('Future Prediction for the year 2020');
            $scenario_0->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 6')
                    ->setGeometry(new Point(-63.62383, -31.34, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_0->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 7')
                    ->setGeometry(new Point(-63.65101, -31.33516, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_0->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 8')
                    ->setGeometry(new Point(-63.64792, -31.33546, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_0->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 9')
                    ->setGeometry(new Point(-63.66714, -31.34513, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_0->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 10')
                    ->setGeometry(new Point(-63.6644, -31.34513, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_0->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 11')
                    ->setGeometry(new Point(-63.60363, -31.32578, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_0->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 12')
                    ->setGeometry(new Point(-63.59367, -31.35803, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_0->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 13')
                    ->setGeometry(new Point(-63.60123, -31.32578, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_0->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 14')
                    ->setGeometry(new Point(-63.58852, -31.35803, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_0->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewPublicWell()
                    ->setName('Public Well 3')
                    ->setGeometry(new Point(-63.62383, -31.34, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_0->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewPublicWell()
                    ->setName('Public Well 4')
                    ->setGeometry(new Point(-63.6216, -31.34162, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));

            /** @var AddBoundaryEvent $event */
            foreach ($scenario_0->getEvents() as $event) {
                $boundary = $event->getBoundary();
                echo sprintf("Set activeCells for %s %s\r\n",
                    get_class($boundary),
                    $boundary->getId());
                $boundary->setActiveCells($geoTools->getActiveCells($boundary, $model->getBoundingBox(), $model->getGridSize()));
            }

            $entityManager->persist($scenario_0);
            $entityManager->flush();

            $scenario_1 = ModelScenarioFactory::create($model);
            $scenario_1->setOwner($user);
            $scenario_1->setPublic(false);
            $scenario_1->setName('Scenario 1: River bank filtration');
            $scenario_1->setDescription('Move the wells next to the river');
            $scenario_1->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 6')
                    ->setGeometry(new Point(-63.62383, -31.34, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_1->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 7')
                    ->setGeometry(new Point(-63.65101, -31.33516, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_1->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 8')
                    ->setGeometry(new Point(-63.64792, -31.33546, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_1->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 9')
                    ->setGeometry(new Point(-63.66714, -31.34513, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_1->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 10')
                    ->setGeometry(new Point(-63.6644, -31.34513, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_1->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 11')
                    ->setGeometry(new Point(-63.60363, -31.32578, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_1->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 12')
                    ->setGeometry(new Point(-63.59367, -31.35803, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_1->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 13')
                    ->setGeometry(new Point(-63.60123, -31.32578, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_1->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 14')
                    ->setGeometry(new Point(-63.58852, -31.35803, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_1->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewPublicWell()
                    ->setName('Public Well 3')
                    ->setGeometry(new Point(-63.62383, -31.34, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_1->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewPublicWell()
                    ->setName('Public Well 4')
                    ->setGeometry(new Point(-63.6216, -31.34162, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));

            /** @var AddBoundaryEvent $event */
            foreach ($scenario_1->getEvents() as $event) {
                $boundary = $event->getBoundary();
                echo sprintf("Set activeCells for %s %s\r\n",
                    get_class($boundary),
                    $boundary->getId());
                $boundary->setActiveCells($geoTools->getActiveCells($boundary, $model->getBoundingBox(), $model->getGridSize()));
            }

            $entityManager->persist($scenario_1);
            $entityManager->flush();

            $scenario_2 = ModelScenarioFactory::create($model);
            $scenario_2->setOwner($user);
            $scenario_2->setPublic(false);
            $scenario_2->setName('Scenario 2: Infiltration wells');
            $scenario_2->setDescription('Add infiltration wells around the urban area');
            $scenario_2->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 6')
                    ->setGeometry(new Point(-63.62383, -31.34, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_2->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 7')
                    ->setGeometry(new Point(-63.65101, -31.33516, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_2->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 8')
                    ->setGeometry(new Point(-63.64792, -31.33546, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_2->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 9')
                    ->setGeometry(new Point(-63.66714, -31.34513, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_2->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 10')
                    ->setGeometry(new Point(-63.6644, -31.34513, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_2->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 11')
                    ->setGeometry(new Point(-63.60363, -31.32578, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_2->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 12')
                    ->setGeometry(new Point(-63.59367, -31.35803, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_2->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 13')
                    ->setGeometry(new Point(-63.60123, -31.32578, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_2->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 14')
                    ->setGeometry(new Point(-63.58852, -31.35803, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_2->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewPublicWell()
                    ->setName('Public Well 3')
                    ->setGeometry(new Point(-63.62383, -31.34, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_2->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewPublicWell()
                    ->setName('Public Well 4')
                    ->setGeometry(new Point(-63.6216, -31.34162, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));

            /** InfiltrationWells */
            $scenario_2->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewInfiltrationWell()
                    ->setName('Infiltration Well 1')
                    ->setGeometry(new Point(-63.571, -31.315, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(5000)
                    )
            ));
            $scenario_2->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewInfiltrationWell()
                    ->setName('Infiltration Well 2')
                    ->setGeometry(new Point(-63.571, -31.3175, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(5000)
                    )
            ));
            $scenario_2->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewInfiltrationWell()
                    ->setName('Infiltration Well 3')
                    ->setGeometry(new Point(-63.571, -31.32, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(5000)
                    )
            ));
            $scenario_2->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewInfiltrationWell()
                    ->setName('Infiltration Well 4')
                    ->setGeometry(new Point(-63.571, -31.3225, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(5000)
                    )
            ));

            /** @var AddBoundaryEvent $event */
            foreach ($scenario_2->getEvents() as $event) {
                $boundary = $event->getBoundary();
                echo sprintf("Set activeCells for %s %s\r\n",
                    get_class($boundary),
                    $boundary->getId());
                $boundary->setActiveCells($geoTools->getActiveCells($boundary, $model->getBoundingBox(), $model->getGridSize()));
            }

            $entityManager->persist($scenario_2);
            $entityManager->flush();

            $scenario_3 = ModelScenarioFactory::create($model);
            $scenario_3->setOwner($user);
            $scenario_3->setPublic(false);
            $scenario_3->setName('Scenario 3: Infiltration wells and ri ver bank filtration');
            $scenario_3->setDescription('Move wells next to the river and add infiltration wells around the urban area.');
            $scenario_3->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 6')
                    ->setGeometry(new Point(-63.62383, -31.34, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_3->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 7')
                    ->setGeometry(new Point(-63.65101, -31.33516, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_3->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 8')
                    ->setGeometry(new Point(-63.64792, -31.33546, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_3->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 9')
                    ->setGeometry(new Point(-63.66714, -31.34513, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_3->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 10')
                    ->setGeometry(new Point(-63.6644, -31.34513, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_3->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 11')
                    ->setGeometry(new Point(-63.60363, -31.32578, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_3->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 12')
                    ->setGeometry(new Point(-63.59367, -31.35803, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_3->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 13')
                    ->setGeometry(new Point(-63.60123, -31.32578, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_3->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewIndustrialWell()
                    ->setName('Irrigation Well 14')
                    ->setGeometry(new Point(-63.58852, -31.35803, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_3->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewPublicWell()
                    ->setName('Public Well 3')
                    ->setGeometry(new Point(-63.62383, -31.34, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));
            $scenario_3->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewPublicWell()
                    ->setName('Public Well 4')
                    ->setGeometry(new Point(-63.6216, -31.34162, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(-5000)
                    )
            ));

            /** InfiltrationWells */
            $scenario_3->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewInfiltrationWell()
                    ->setName('Infiltration Well 1')
                    ->setGeometry(new Point(-63.571, -31.315, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(5000)
                    )
            ));
            $scenario_3->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewInfiltrationWell()
                    ->setName('Infiltration Well 2')
                    ->setGeometry(new Point(-63.571, -31.3175, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(5000)
                    )
            ));
            $scenario_3->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewInfiltrationWell()
                    ->setName('Infiltration Well 3')
                    ->setGeometry(new Point(-63.571, -31.32, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(5000)
                    )
            ));
            $scenario_3->addEvent(EventFactory::createAddBoundaryEvent(
                WellBoundaryFactory::createNewInfiltrationWell()
                    ->setName('Infiltration Well 4')
                    ->setGeometry(new Point(-63.571, -31.3225, 4326))
                    ->setLayer($layer_1)
                    ->addStressPeriod(StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('1.1.2015'))
                        ->setDateTimeEnd(new \DateTime('31.12.2015'))
                        ->setFlux(5000)
                    )
            ));

            /** @var AddBoundaryEvent $event */
            foreach ($scenario_3->getEvents() as $event) {
                $boundary = $event->getBoundary();
                echo sprintf("Set activeCells for %s %s\r\n",
                    get_class($boundary),
                    $boundary->getId());
                $boundary->setActiveCells($geoTools->getActiveCells($boundary, $model->getBoundingBox(), $model->getGridSize()));
            }

            $entityManager->persist($scenario_3);
            $entityManager->flush();
        }

        return 1;
    }
}