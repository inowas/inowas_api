<?php

namespace AppBundle\DataFixtures\ORM\TestScenarios\TestScenario_1;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\User;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\ConstantHeadBoundaryFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\UserFactory;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTestScenario_1 implements FixtureInterface, ContainerAwareInterface
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
        $username = 'test_scenario_1';
        $email = 'test_scenario_1@inowas.com';
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
        $propertyTypeGwHead = PropertyTypeFactory::create(PropertyType::HYDRAULIC_HEAD);
        $propertyTypeTopElevation = PropertyTypeFactory::create(PropertyType::TOP_ELEVATION);
        $propertyTypeBottomElevation = PropertyTypeFactory::create(PropertyType::BOTTOM_ELEVATION);
        $propertyHydraulicConductivity = PropertyTypeFactory::create(PropertyType::HYDRAULIC_CONDUCTIVITY);

        $bb = new BoundingBox(0, 400, 0, 400);
        $bb->setDXInMeters(400);
        $bb->setDYInMeters(400);

        $modflowModelManager = $this->container->get('inowas.modflowmodel.manager');

        /** @var ModFlowModel $model */
        $model = $modflowModelManager->create()
            ->setOwner($user)
            ->setPublic($public)
            ->setName('Lake_Example')
            ->setBoundingBox($bb)
            ->setGridSize(new GridSize(101, 101))
            ->setArea(AreaFactory::create()
                ->setOwner($user)
                ->setName('Area TestScenario 1')
                ->setAreaType("AreaType TestScenario 1")
                ->setPublic($public)
                ->setGeometry(new Polygon(
                    array(new LineString(array(
                        array(0, 0),
                        array(0, 400),
                        array(400, 400),
                        array(400, 0),
                        array(0, 0)
                    ))), 4326))
            )
        ;

        $soilModel = SoilModelFactory::create()
                ->setOwner($user)
                ->setName('SoilModel Lake Example')
                ->setPublic($public);

        $soilModel->addGeologicalLayer(GeologicalLayerFactory::create()
            ->setOrder(GeologicalLayer::TOP_LAYER)
            ->setOwner($user)
            ->setName('Layer 1 Lake Example')
            ->setPublic($public)
            ->addValue($propertyTypeTopElevation, PropertyValueFactory::create()->setValue(0))
            ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue(-5))
            ->addValue($propertyHydraulicConductivity, PropertyValueFactory::create()->setValue(1))
        );

        for ($i = 1; $i < 10; $i++) {
            $soilModel->addGeologicalLayer(GeologicalLayerFactory::create()
                ->setOrder(GeologicalLayer::TOP_LAYER+$i)
                ->setOwner($user)
                ->setName('Layer '.($i+1).' Lake Example')
                ->setPublic($public)
                ->addValue($propertyTypeBottomElevation, PropertyValueFactory::create()->setValue(($i+1) * -5))
                ->addValue($propertyHydraulicConductivity, PropertyValueFactory::create()->setValue(1))
            );
        }

        $model->setSoilModel($soilModel);

        $model->addBoundary(ConstantHeadBoundaryFactory::create()
                ->setOwner($user)
                ->setName('Boundary Outer Circle Lake-Example')
                ->setPublic($public)
                ->setGeometry(new LineString(array(
                    array(0, 0),
                    array(0, 400),
                    array(400, 400),
                    array(400, 0),
                    array(0, 0)
                )))
                ->addValue($propertyTypeGwHead, PropertyValueFactory::create()->setValue(100))
            )
            ->addBoundary(ConstantHeadBoundaryFactory::create()
                ->setOwner($user)
                ->setName('Boundary Lake Lake-Example')
                ->setPublic($public)
                ->setGeometry(new LineString(array(
                    array(199, 199),
                    array(199, 201),
                    array(201, 201),
                    array(201, 199),
                    array(199, 199)
                ), 4326))
                ->addValue($propertyTypeGwHead, PropertyValueFactory::create()->setValue(90))
            )
        ;

        $modflowModelManager->update($model, false);

        return 0;
    }
}