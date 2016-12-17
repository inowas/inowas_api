<?php

namespace Inowas\AppBundle\DataFixtures\Modflow\Hanoi;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inowas\AppBundle\DataFixtures\Modflow\LoadScenarioBase;
use Inowas\ModflowBundle\Model\AreaFactory;
use Inowas\ModflowBundle\Model\Boundary\Boundary;
use Inowas\ModflowBundle\Model\Boundary\GeneralHeadBoundary;
use Inowas\ModflowBundle\Model\Boundary\ObservationPoint;
use Inowas\ModflowBundle\Model\Boundary\ObservationPointFactory;
use Inowas\ModflowBundle\Model\Boundary\RechargeBoundary;
use Inowas\ModflowBundle\Model\Boundary\RiverBoundary;
use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\BoundaryFactory;
use Inowas\ModflowBundle\Model\BoundingBox;
use Inowas\ModflowBundle\Model\GridSize;
use Inowas\ModflowBundle\Model\Modflow;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\StressPeriodFactory;
use Inowas\PyprocessingBundle\Service\Interpolation;
use Inowas\SoilmodelBundle\Factory\BoreHoleFactory;
use Inowas\SoilmodelBundle\Factory\LayerFactory;
use Inowas\SoilmodelBundle\Model\BoreHole;
use Inowas\SoilmodelBundle\Model\Layer;
use Inowas\SoilmodelBundle\Model\Property;
use Inowas\SoilmodelBundle\Model\PropertyType;
use Inowas\SoilmodelBundle\Model\PropertyValue;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RioPrimeroBaseModelDataFixture extends LoadScenarioBase implements FixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;


    /**
     * @param ContainerInterface|null $container
     * @return mixed
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     * @return mixed
     */
    public function load(ObjectManager $manager)
    {
        $geoTools = $this->container->get('inowas.geotools.geotools');

        $this->loadUsers($this->container->get('fos_user.user_manager'));

        // Add the SoilModel
        $soilModelManager = $this->container->get('inowas.soilmodel.soilmodelmanager');
        $soilmodel = $soilModelManager->create();
        $soilmodel->setName('SoilModel Río Primero');
        $soilmodel->addLayer(LayerFactory::create()->setName('Surface Layer')->setDescription('The one and only.'));
        $soilmodel->setBoundingBox(new BoundingBox(-63.687336, -63.569260, -31.367449, -31.313615, 4326));
        $soilmodel->setGridSize(new GridSize(75,40));
        $soilModelManager->update($soilmodel);

        $boreHoles = array(
            array('point', 'name', 'top', 'bot'),
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

        $header = null;
        foreach ($boreHoles as $borehole){
            if (is_null($header)){
                $header = $borehole;
                continue;
            }

            $borehole = array_combine($header, $borehole);
            echo sprintf("Add BoreHole %s to soilmodel %s.\r\n", $borehole['name'], $soilmodel->getId()->toString());

            $soilmodel->addBoreHole(
                BoreHoleFactory::create()
                    ->setName($borehole['name'])
                    ->setPoint($borehole['point'])
                    ->addLayer(LayerFactory::create()
                        ->setName($borehole['name'].'0')
                        ->addOrReplaceProperty(new Property(PropertyType::fromString(PropertyType::TOP_ELEVATION), PropertyValue::fromValue($borehole['top'])))
                        ->addOrReplaceProperty(new Property(PropertyType::fromString(PropertyType::BOTTOM_ELEVATION), PropertyValue::fromValue($borehole['bot'])))
                        ->addOrReplaceProperty(new Property(PropertyType::fromString(PropertyType::K_X), PropertyValue::fromValue(10)))
                        ->addOrReplaceProperty(new Property(PropertyType::fromString(PropertyType::K_Y), PropertyValue::fromValue(10)))
                        ->addOrReplaceProperty(new Property(PropertyType::fromString(PropertyType::K_Z), PropertyValue::fromValue(1)))
                    )
            );
            $soilModelManager->update($soilmodel);
        }

        // Add the ModflowModel
        $modelManager = $this->container->get('inowas.modflow.modelmanager');
        $model = $modelManager->create();
        $model->setName("Rio Primero Base Model")
            ->setDescription('Base Model for the scenario analysis 2020 Rio Primero')
            ->setArea(AreaFactory::create()
                ->setName('Area Model Río Primero')
                ->setGeometry(new Polygon(
                    array(
                        array(
                            array(-63.687336, -31.313615),
                            array(-63.687336, -31.367449),
                            array(-63.569260, -31.367449),
                            array(-63.569260, -31.313615),
                            array(-63.687336, -31.313615)
                        )
                    ), 4326
                ))
            )
            ->setSoilmodelId($soilmodel->getId())
            ->setBoundingBox(new BoundingBox(-63.687336, -63.569260, -31.367449, -31.313615, 4326))
            ->setGridSize(new GridSize(75,40))
            ->setStart(new \DateTime('1.1.2015'))
            ->setEnd(new \DateTime('31.12.2015'))
        ;

        $modelManager->update($model);

        // Add Boundaries
        /**
         * General Head West
         * @var GeneralHeadBoundary $ghbWest
         */
        $ghbWest = BoundaryFactory::createGhb()
            ->setName('Constant head boundary West')
            ->setGeometry(
                new LineString(array(
                    array($model->getBoundingBox()->getXMin(), $model->getBoundingBox()->getYMin()),
                    array($model->getBoundingBox()->getXMin(), $model->getBoundingBox()->getYMax())
                ), $model->getBoundingBox()->getSrid())
            );

        /** @var ObservationPoint $observationPoint */
        $observationPoint = ObservationPointFactory::create()
            ->setName('Observationpoint 1')
            ->setGeometry(new Point(
                $model->getBoundingBox()->getXMin(),
                $model->getBoundingBox()->getYMin(),
                $model->getBoundingBox()->getSrid()
            ))
            ->addStressPeriod(StressPeriodFactory::createGhb()
            ->setDateTimeBegin(new \DateTime('1.1.2015'))
            ->setStage(450)
            ->setConductivity(100)
        );

        $ghbWest->addObservationPoint($observationPoint);
        $model->addBoundary($ghbWest);
        $modelManager->update($model);

        unset($ghbWest);
        unset($observationPoint);

        /**
         * General Head East
         * @var GeneralHeadBoundary $ghbEast
         */
        $ghbEast = BoundaryFactory::createGhb()
            ->setName('Constant head boundary East')
            ->setGeometry(new LineString(
                array(
                    array($model->getBoundingBox()->getXMax(), $model->getBoundingBox()->getYMin()),
                    array($model->getBoundingBox()->getXMax(), $model->getBoundingBox()->getYMax())
                ), $model->getBoundingBox()->getSrid())
            );

        /**
         * @var ObservationPoint $observationPoint
         */
        $observationPoint = ObservationPointFactory::create()
            ->setName('Observationpoint 2')
            ->setGeometry(new Point(
                $model->getBoundingBox()->getXMax(),
                $model->getBoundingBox()->getYMin(),
                $model->getBoundingBox()->getSrid()
            ))
            ->addStressPeriod(StressPeriodFactory::createGhb()
                ->setDateTimeBegin(new \DateTime('1.1.2015'))
                ->setStage(440)
                ->setConductivity(100)
            );

        $ghbEast->addObservationPoint($observationPoint);
        $model->addBoundary($ghbEast);
        $modelManager->update($model);

        unset($ghbEast);
        unset($observationPoint);

        /**
         * River
         * @var RiverBoundary $riverBoundary
         */
        $riverBoundary = BoundaryFactory::createRiv()
            ->setName('Río Primero')
            ->setGeometry(new LineString(
                array(
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
            ->addObservationPoint(ObservationPointFactory::create()
                ->setName('ObservationPoint 3')
                ->setGeometry(new Point(-63.676586151123,-31.367415770489, 4326))
                ->addStressPeriod(StressPeriodFactory::createRiv()
                    ->setDateTimeBegin(new \DateTime('1.1.2015'))
                    ->setStage(446)
                    ->setConductivity(200)
                    ->setBottomElevation(444)
                )
            );

        $model->addBoundary($riverBoundary);
        $modelManager->update($model);
        echo sprintf("Add River-Boundary %s.\r\n", $riverBoundary->getName());
        unset($riverBoundary);

        /**
         * Irrigation Well 1
         * @var WellBoundary $well
         */
        $well = BoundaryFactory::createWel();
        $well->setName('Irrigation Well 1');
        $well->setWellType(WellBoundary::TYPE_INDUSTRIAL_WELL);
        $well->setLayerNumber(0);
        $well->setGeometry(new Point(-63.671125, -31.325009, 4326));
        $well->addStressPeriod(StressPeriodFactory::createWel()
            ->setDateTimeBegin(new \DateTime('1.1.2015'))
            ->setFlux(-5000)
        );

        $model->addBoundary($well);
        echo sprintf("Add well %s.\r\n", $well->getName());
        $modelManager->update($model);
        unset($well);

        /**
         * Irrigation Well 2
         * @var WellBoundary $well
         */
        $well = BoundaryFactory::createWel();
        $well->setName('Irrigation Well 2');
        $well->setWellType(WellBoundary::TYPE_INDUSTRIAL_WELL);
        $well->setLayerNumber(0);
        $well->setGeometry(new Point(-63.659952, -31.330144, 4326));
        $well->addStressPeriod(StressPeriodFactory::createWel()
            ->setDateTimeBegin(new \DateTime('1.1.2015'))
            ->setFlux(-5000)
        );

        $model->addBoundary($well);
        echo sprintf("Add well %s.\r\n", $well->getName());
        $modelManager->update($model);
        unset($well);

        /**
         * Irrigation Well 3
         * @var WellBoundary $well
         */
        $well = BoundaryFactory::createWel();
        $well->setName('Irrigation Well 3');
        $well->setWellType(WellBoundary::TYPE_INDUSTRIAL_WELL);
        $well->setLayerNumber(0);
        $well->setGeometry(new Point(-63.674691, -31.342506, 4326));
        $well->addStressPeriod(StressPeriodFactory::createWel()
            ->setDateTimeBegin(new \DateTime('1.1.2015'))
            ->setFlux(-5000)
        );

        $model->addBoundary($well);
        echo sprintf("Add well %s.\r\n", $well->getName());
        $modelManager->update($model);
        unset($well);

        /**
         * Irrigation Well 4
         * @var WellBoundary $well
         */
        $well = BoundaryFactory::createWel();
        $well->setName('Irrigation Well 4');
        $well->setWellType(WellBoundary::TYPE_INDUSTRIAL_WELL);
        $well->setLayerNumber(0);
        $well->setGeometry(new Point(-63.637379, -31.359613, 4326));
        $well->addStressPeriod(StressPeriodFactory::createWel()
            ->setDateTimeBegin(new \DateTime('1.1.2015'))
            ->setFlux(-5000)
        );

        $model->addBoundary($well);
        echo sprintf("Add well %s.\r\n", $well->getName());
        $modelManager->update($model);
        unset($well);

        /**
         * Irrigation Well 5
         * @var WellBoundary $well
         */
        $well = BoundaryFactory::createWel();
        $well->setName('Irrigation Well 5');
        $well->setWellType(WellBoundary::TYPE_INDUSTRIAL_WELL);
        $well->setLayerNumber(0);
        $well->setGeometry(new Point(-63.582069, -31.324063, 4326));
        $well->addStressPeriod(StressPeriodFactory::createWel()
            ->setDateTimeBegin(new \DateTime('1.1.2015'))
            ->setFlux(-5000)
        );

        $model->addBoundary($well);
        echo sprintf("Add well %s.\r\n", $well->getName());
        $modelManager->update($model);
        unset($well);

        /**
         * Public Well 1
         * @var WellBoundary $well
         */
        $well = BoundaryFactory::createWel();
        $well->setName('Public Well 1');
        $well->setWellType(WellBoundary::TYPE_PUBLIC_WELL);
        $well->setLayerNumber(0);
        $well->setGeometry(new Point(-63.625402, -31.329897, 4326));
        $well->addStressPeriod(StressPeriodFactory::createWel()
            ->setDateTimeBegin(new \DateTime('1.1.2015'))
            ->setFlux(-5000)
        );

        $model->addBoundary($well);
        echo sprintf("Add well %s.\r\n", $well->getName());
        $modelManager->update($model);
        unset($well);

        /**
         * Public Well 2
         * @var WellBoundary $well
         */
        $well = BoundaryFactory::createWel();
        $well->setName('Public Well 2');
        $well->setWellType(WellBoundary::TYPE_PUBLIC_WELL);
        $well->setLayerNumber(0);
        $well->setGeometry(new Point(-63.623027, -31.331184, 4326));
        $well->addStressPeriod(StressPeriodFactory::createWel()
            ->setDateTimeBegin(new \DateTime('1.1.2015'))
            ->setFlux(-5000)
        );


        $model->addBoundary($well);
        echo sprintf("Add well %s.\r\n", $well->getName());
        $modelManager->update($model);
        unset($well);

        /**
         * Recharge Boundary
         * @var RechargeBoundary $recharge
         */
        $recharge = BoundaryFactory::createRch();
        $recharge->setName('Recharge Boundary');
        $recharge->setGeometry($model->getArea()->getGeometry());
        $recharge->addStressPeriod(StressPeriodFactory::createRch()
            ->setDateTimeBegin(new \DateTime('1.1.2015'))
            ->setRecharge(3.29e-4)
        );

        $model->addBoundary($recharge);
        echo sprintf("Add recharge %s.\r\n", $recharge->getName());
        $modelManager->update($model);
        unset($recharge);

        echo sprintf("Set activeCells for ModelArea\r\n");
        $activeCells = $geoTools->getActiveCells($model->getArea(), $model->getBoundingBox(), $model->getGridSize());
        $model->getArea()->setActiveCells($activeCells);

        /** @var Boundary $boundary */
        foreach ($model->getBoundaries() as $boundary){
            echo sprintf("Set activeCells for %s.\r\n", get_class($boundary));
            $boundary->setActiveCells($geoTools->getActiveCells($boundary, $model->getBoundingBox(), $model->getGridSize()));
        }

        /* Interpolation of all layers */
        $soilModelService = $this->container->get('inowas.soilmodel.soilmodelservice');
        $soilModelService->setSoilModel($soilmodel);

        /** @var Layer $layer */
        foreach ($soilmodel->getLayers() as $layer){

            $propertyTypes = [];

            /** @var BoreHole $borehole */
            foreach ($soilmodel->getBoreHoles() as $borehole){
                /** @var Layer $boreholeLayer */
                foreach ($borehole->getLayers() as $boreholeLayer){
                    $propertyTypes = $boreholeLayer->getPropertyTypes();
                }
            }

            /** @var PropertyType $propertyType */
            foreach ($propertyTypes as $propertyType){

                echo (sprintf("Interpolating Layer %s, PropertyType %s\r\n", $layer->getName(), $propertyType->getType()));
                $soilModelService->interpolateLayerByProperty(
                    $layer,
                    $propertyType,
                    array(Interpolation::TYPE_IDW, Interpolation::TYPE_MEAN)
                );
            }
        }

        $modflow = new Modflow();
        $modflow->setUserId($this->getOwner()->getId());
        $modflow->setModflowModel($model);
        $this->container->get('doctrine.orm.default_entity_manager')->persist($modflow);
        $this->container->get('doctrine.orm.default_entity_manager')->flush();

        $this->loadScenarios($model);
        return 1;
    }

    private function loadScenarios(ModflowModel $model){
        $scenarioAnalysisManager = $this->container->get('inowas.scenarioanalysis.scenarioanalysismanager');
        $scenarioManager = $this->container->get('inowas.scenarioanalysis.scenariomanager');

        foreach ($this->getUserList() as $user){
            $scenarioAnalysis = $scenarioAnalysisManager->create($user, $model);
            $scenario = $scenarioManager->create($model);
            $scenarioManager->update($scenario);
            $scenarioAnalysisManager->update($scenarioAnalysis);
        }
    }
}
