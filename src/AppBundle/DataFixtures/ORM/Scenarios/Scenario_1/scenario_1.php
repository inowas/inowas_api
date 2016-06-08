<?php

namespace AppBundle\DataFixtures\ORM\Scenarios\Scenario_1;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\User;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\AreaTypeFactory;
use AppBundle\Model\GeneralHeadBoundaryFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\ObservationPointFactory;
use AppBundle\Model\Point;
use AppBundle\Model\PropertyTimeValueFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\StreamFactory;
use AppBundle\Model\UserFactory;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LoadScenario_1 implements FixtureInterface, ContainerAwareInterface
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
            $user = UserFactory::create();
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

        // Add new SoilModel
        $soilModel = SoilModelFactory::create();
        $soilModel->setOwner($user)->setName('SM Scenario 1');

        // Add Geological Profile 1
        $geologicalPoint_1 = GeologicalPointFactory::create()
            ->setOwner($user)
            ->setName('SC_GP1')
            ->setPoint(new Point(11772891.9650673, 2397519.89608855, 4326))
            ->setPublic($public)
            ->addGeologicalUnit(GeologicalUnitFactory::create()
                ->setName('SC1_GU1.1')
                ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue(100))
                ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue(70))
            )
            ->addGeologicalUnit(GeologicalUnitFactory::create()
                ->setName('SC1_GU1.2')
                ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue(70))
                ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue(40))
            )
            ->addGeologicalUnit(GeologicalUnitFactory::create()
                ->setName('SC1_GU1.3')
                ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue(40))
                ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue(0))
            )
        ;
        $entityManager->persist($geologicalPoint_1);
        $soilModel->addGeologicalPoint($geologicalPoint_1);
        $entityManager->persist($soilModel);
        $entityManager->flush();

        // Add Geological Profile 2
        $geologicalPoint_2 = GeologicalPointFactory::create()
            ->setOwner($user)
            ->setName('SC_GP2')
            ->setPoint(new Point(11786103.1301754, 2397138.80478736, 4326))
            ->setPublic($public)
            ->addGeologicalUnit(GeologicalUnitFactory::create()
                ->setName('SC1_GU2.1')
                ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue(100))
                ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue(70))
            )
            ->addGeologicalUnit(GeologicalUnitFactory::create()
                ->setName('SC1_GU2.2')
                ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue(70))
                ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue(40))
            )
            ->addGeologicalUnit(GeologicalUnitFactory::create()
                ->setName('SC1_GU2.3')
                ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue(40))
                ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue(0))
            )
        ;
        $entityManager->persist($geologicalPoint_2);
        $soilModel->addGeologicalPoint($geologicalPoint_2);
        $entityManager->persist($soilModel);
        $entityManager->flush();

        // Add Geological Profile 3
        $geologicalPoint_3 = GeologicalPointFactory::create()
            ->setOwner($user)
            ->setName('SC_GP3')
            ->setPoint(new Point(11779836.2954446, 2387061.05704468, 4326))
            ->setPublic($public)
            ->addGeologicalUnit(GeologicalUnitFactory::create()
                ->setName('SC1_GU3.1')
                ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue(100))
                ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue(70))
            )
            ->addGeologicalUnit(GeologicalUnitFactory::create()
                ->setName('SC1_GU3.2')
                ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue(70))
                ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue(40))
            )
            ->addGeologicalUnit(GeologicalUnitFactory::create()
                ->setName('SC1_GU3.3')
                ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue(40))
                ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue(0))
            )
        ;
        $entityManager->persist($geologicalPoint_3);
        $soilModel->addGeologicalPoint($geologicalPoint_3);
        $entityManager->persist($soilModel);
        $entityManager->flush();


        $geologicalLayer = GeologicalLayerFactory::setOwnerNameAndPublic($user, 'SC1_L1', $public);
        $geologicalLayer->setOrder(GeologicalLayer::TOP_LAYER);
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GU1.1');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GU2.1');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GU3.1');
        $entityManager->persist($geologicalLayer);
        $entityManager->flush();

        $soilModel->addGeologicalLayer($geologicalLayer);
        $entityManager->persist($soilModel);

        // Create layer 2
        $geologicalLayer = GeologicalLayerFactory::setOwnerNameAndPublic($user, 'SC1_L2', $public);
        $geologicalLayer->setOrder(GeologicalLayer::TOP_LAYER+1);
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GU1.2');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GU2.2');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GU3.2');
        $entityManager->persist($geologicalLayer);
        $entityManager->flush();

        $soilModel->addGeologicalLayer($geologicalLayer);
        $entityManager->persist($soilModel);

        // Create layer 3
        $geologicalLayer = GeologicalLayerFactory::setOwnerNameAndPublic($user, 'SC1_L3', $public);
        $geologicalLayer->setOrder(GeologicalLayer::TOP_LAYER+2);
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GU1.3');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GU2.3');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GU3.3');
        $entityManager->persist($geologicalLayer);
        $entityManager->flush();

        $soilModel->addGeologicalLayer($geologicalLayer);
        $entityManager->persist($soilModel);
        $entityManager->flush();

        // Create Stream
        $stream = StreamFactory::setOwnerNameAndPublic($user, 'SC1_S1', $public);
        $stream->setStartingPoint(new Point(11777338.0302479, 2395656.78306049, 4326));
        $stream->setLine(
            new LineString(array(
                array(11766937.6721201, 2380245.03544451),
                array(11772341.4998545, 2386595.27878767),
                array(11777338.0302479, 2395656.78306049)),
                4326
            )
        );
        $entityManager->persist($stream);
        $entityManager->flush();

        // Add new Area
        $area = AreaFactory::create()
            ->setOwner($user)
            ->setName('SC1_A1')
            ->setAreaType(AreaTypeFactory::create()
                ->setName('SC1_AT1'))
            ->setPublic($public)
            ->setGeometry(new Polygon(
                array(new LineString(array(
                    array(11767778.4794313, 2403329.01798664),
                    array(11791015.33603, 2403329.01798664),
                    array(11791168.2100865, 2379939.28733137),
                    array(11766937.6721201, 2380245.03544451),
                    array(11767778.4794313, 2403329.01798664)                    
                ))), 4326));
        

        $area->addObservationPoint(
            ObservationPointFactory::create()
            ->setOwner($user)
            ->setName('SC1_OP1')
            ->setPoint(new Point(11778481.3041515, 2393327.89177542, 4326))
            ->setPublic($public)
            ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue(100))
            ->addValue($propertyTypeGwHead, PropertyTimeValueFactory::create()
                ->setValue(50)
                ->setDatetime(new \DateTime('2015-01-01 00:00:00')))
            ->addValue($propertyTypeGwHead, PropertyTimeValueFactory::create()
                ->setValue(51)
                ->setDatetime(new \DateTime('2015-02-01 00:00:00')))
            ->addValue($propertyTypeGwHead, PropertyTimeValueFactory::create()
                ->setValue(52)
                ->setDatetime(new \DateTime('2015-03-01 00:00:00')))
            ->addValue($propertyTypeGwHead, PropertyTimeValueFactory::create()
                ->setValue(53)
                ->setDatetime(new \DateTime('2015-04-01 00:00:00')))
        );

        $area->addObservationPoint(
            ObservationPointFactory::create()
                ->setOwner($user)
                ->setName('SC1_OP2')
                ->setPoint(new Point(11772891.9650673, 2397519.89608855, 4326))
                ->setPublic($public)
                ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue(110))
        );

        $area->addObservationPoint(
            ObservationPointFactory::create()
                ->setOwner($user)
                ->setName('SC1_OP3')
                ->setPoint(new Point(11786103.1301754, 2397138.80478736, 4326))
                ->setPublic($public)
                ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue(120))
        );

        $entityManager->persist($area);
        $entityManager->flush();

        // Create boundary
        $boundary = GeneralHeadBoundaryFactory::create()
            ->setOwner($user)
            ->setName('SC1_B1')
            ->setPublic($public)
            ->setGeometry(new LineString(array(
                array(11767778.4794313, 2403329.01798664),
                array(11766937.6721201, 2380245.03544451),
                array(11791168.2100865, 2379939.28733137)), 4326))
            ->addValue($propertyTypeGwHead, PropertyValueFactory::create()->setValue(60));

        $entityManager->persist($boundary);
        $entityManager->flush();

        /** @var ModFlowModel $model */
        $model = ModFlowModelFactory::create();
        $model->setName("ModFlowModel Scenario 1");
        $model->setOwner($user);
        $model->setDescription("ModFlowModel Scenario 1 Description");
        $model->setSoilModel($soilModel);
        $model->setArea($area);
        $entityManager->persist($model);
        $entityManager->flush();

        $properties = $model->getCalculationProperties();
        $properties['grid_size'] = array(
            'rows' => 50,
            'cols' => 50
        );
        $model->setCalculationProperties($properties);
        $entityManager->persist($model);
        $entityManager->flush();

        return 0;
    }


    private function addGeologicalUnitToGeologicalLayer(ObjectManager $entityManager, \AppBundle\Entity\GeologicalLayer $layer, $geologicalUnitName)
    {
        $geologicalUnit = $entityManager->getRepository('AppBundle:GeologicalUnit')
            ->findOneBy(array(
                'name' => $geologicalUnitName
            ));

        if ($geologicalUnit) {
            $layer->addGeologicalUnit($geologicalUnit);
        }

        return $layer;
    }
}