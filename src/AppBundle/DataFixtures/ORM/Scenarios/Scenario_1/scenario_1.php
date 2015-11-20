<?php

namespace AppBundle\DataFixtures\ORM\Scenarios\Scenario_1;

use AppBundle\Entity\Boundary;
use AppBundle\Entity\Project;
use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyTimeValue;
use AppBundle\Entity\PropertyType;
use AppBundle\Entity\ObservationPoint;

use AppBundle\Entity\GeologicalPoint;
use AppBundle\Entity\GeologicalUnit;
use AppBundle\Entity\PropertyValue;
use AppBundle\Entity\GeologicalLayer;

use AppBundle\Model\AreaFactory;
use AppBundle\Model\AreaTypeFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\ProjectFactory;
use AppBundle\Model\StreamFactory;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


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

        // Add new User
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setUsername('inowas_scenario_1');
        $user->setEmail('inowas_scenario_1@inowas.com');
        $user->setPassword('inowas_scenario_1');
        $userManager->updateUser($user);
        $entityManager->flush();


        // Add new Project
        $project = ProjectFactory::setOwnerAndPublic($user, false);
        $project->setName('Scenario 1');
        $entityManager->persist($project);
        $entityManager->flush();


        // Add Geological Profile 1
        $geologicalPoint = GeologicalPointFactory::setOwnerProjectNameAndPoint($user, $project, 'GP1', new Point(11772891.9650673, 2397519.89608855, 3857));
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, $project, 'GP1.1', 100, 70);
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, $project, 'GP1.2',  70, 40);
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, $project, 'GP1.3',  40,  0);
        $entityManager->persist($geologicalPoint);
        $entityManager->flush();

        // Add Geological Profile 2
        $geologicalPoint = GeologicalPointFactory::setOwnerProjectNameAndPoint($user, $project, 'GP2', new Point(11786103.1301754, 2397138.80478736, 3857));
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, $project, 'GP2.1', 100, 70);
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, $project, 'GP2.2',  70, 40);
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, $project, 'GP2.3',  40,  0);
        $entityManager->persist($geologicalPoint);
        $entityManager->flush();

        // Add Geological Profile 3
        $geologicalPoint = GeologicalPointFactory::setOwnerProjectNameAndPoint($user, $project, 'GP3', new Point(11779836.2954446, 2387061.05704468, 3857));
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, $project, 'GP3.1', 100, 70);
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, $project, 'GP3.2',  70, 40);
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, $project, 'GP3.3',  40,  0);
        $entityManager->persist($geologicalPoint);
        $entityManager->flush();

        // Create layer 1
        $geologicalLayer = GeologicalLayerFactory::setOwnerProjectNameAndPublic($user, $project, 'L1');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'GP1.1');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'GP2.1');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'GP3.1');
        $entityManager->persist($geologicalLayer);
        $entityManager->flush();

        // Create layer 2
        $geologicalLayer = GeologicalLayerFactory::setOwnerProjectNameAndPublic($user, $project, 'L2');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'GP1.2');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'GP2.2');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'GP3.2');
        $entityManager->persist($geologicalLayer);
        $entityManager->flush();

        // Create layer 3
        $geologicalLayer = GeologicalLayerFactory::setOwnerProjectNameAndPublic($user, $project, 'L3');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'GP1.2');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'GP2.2');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'GP3.2');
        $entityManager->persist($geologicalLayer);
        $entityManager->flush();


        // Create Stream
        $stream = StreamFactory::setOwnerProjectNameAndPublic($user, $project, 'S1');
        $stream->setStartingPoint(new Point(11777338.0302479, 2395656.78306049, 3857));
        $stream->setLine(
            new LineString(array(
                array(11766937.6721201, 2380245.03544451),
                array(11772341.4998545, 2386595.27878767),
                array(11777338.0302479, 2395656.78306049)),
                3857
            )
        );
        $entityManager->persist($stream);
        $entityManager->flush();

        // Add new Area
        $areaType = AreaTypeFactory::setName('AT1');
        $entityManager->persist($areaType);
        $area = AreaFactory::setOwnerProjectNameAndPublic($user, $project, 'A1');
        $area->setAreaType($areaType);
        $coordinates = array(
            array(11767778.4794313, 2403329.01798664),
            array(11791015.33603, 2403329.01798664),
            array(11791168.2100865, 2379939.28733137),
            array(11766937.6721201, 2380245.03544451),
            array(11767778.4794313, 2403329.01798664)
        );
        $line = new LineString($coordinates);
        $polygon = new Polygon(array($line), 3857);
        $area->setGeometry($polygon);



        // Create ObservationPoints for area
        $observationPoint1 = new ObservationPoint($user, $project);
        $observationPoint1->setPoint(new Point(11778481.3041515, 2393327.89177542, 3857));
        $observationPoint1->setElevation(100);
        $entityManager->persist($observationPoint1);
        $area->addObservationPoint($observationPoint1);

        $observationPoint2 = new ObservationPoint($user, $project);
        $observationPoint2->setPoint(new Point(11772891.9650673, 2397519.89608855, 3857));
        $observationPoint2->setElevation(100);
        $entityManager->persist($observationPoint2);
        $area->addObservationPoint($observationPoint2);

        $observationPoint3 = new ObservationPoint($user, $project);
        $observationPoint3->setPoint(new Point(11786103.1301754, 2397138.80478736, 3857));
        $observationPoint3->setElevation(100);
        $entityManager->persist($observationPoint3);

        $area->addObservationPoint($observationPoint3);
        $entityManager->persist($area);

        // Load boundary
        $boundary = new Boundary($user, $project);
        $lineCoordinates = array(
            array(11767778.4794313, 2403329.01798664),
            array(11766937.6721201, 2380245.03544451),
            array(11791168.2100865, 2379939.28733137)
        );
        $line = new LineString($lineCoordinates, 3857);
        $boundary->setGeometry($line);
        $entityManager->persist($boundary);

        // Add ModelObjectPropertyTypes
        $propertyTypeGwHead = new PropertyType();
        $propertyTypeGwHead->setName("gwhead");
        $entityManager->persist($propertyTypeGwHead);

        $propertyTypeElevation = new PropertyType();
        $propertyTypeElevation->setName("elevation");
        $entityManager->persist($propertyTypeElevation);

        // Add ModelObjectProperties and TimeSeries
        $property = new Property();
        $property->setType($propertyTypeGwHead);
        $property->setModelObject($observationPoint1);
        $entityManager->persist($property);

        $timeValue = new PropertyTimeValue();
        $timeValue->setProperty($property);
        $timeValue->setTimeStamp(new \DateTime('2015-01-01 00:00:00'));
        $timeValue->setValue(50);
        $entityManager->persist($timeValue);

        $timeValue = new PropertyTimeValue();
        $timeValue->setProperty($property);
        $timeValue->setTimeStamp(new \DateTime('2015-02-01 00:00:00'));
        $timeValue->setValue(51);
        $entityManager->persist($timeValue);

        $timeValue = new PropertyTimeValue();
        $timeValue->setProperty($property);
        $timeValue->setTimeStamp(new \DateTime('2015-03-01 00:00:00'));
        $timeValue->setValue(52);
        $entityManager->persist($timeValue);

        $timeValue = new PropertyTimeValue();
        $timeValue->setProperty($property);
        $timeValue->setTimeStamp(new \DateTime('2015-04-01 00:00:00'));
        $timeValue->setValue(53);
        $entityManager->persist($timeValue);

        $timeValue = new PropertyTimeValue();
        $timeValue->setProperty($property);
        $timeValue->setTimeStamp(new \DateTime('2015-05-01 00:00:00'));
        $timeValue->setValue(54);
        $entityManager->persist($timeValue);


        $property = new Property();
        $property->setType($propertyTypeGwHead);
        $property->setModelObject($boundary);
        $entityManager->persist($property);

        $propertyValue = new PropertyValue();
        $propertyValue->setProperty($property);
        $propertyValue->setValue(60);
        $entityManager->persist($propertyValue);

        $property = new Property();
        $property->setType($propertyTypeElevation);
        $property->setModelObject($observationPoint1);
        $entityManager->persist($property);

        $propertyValue = new PropertyValue();
        $propertyValue->setProperty($property);
        $propertyValue->setValue(100);
        $entityManager->persist($propertyValue);

        $property = new Property();
        $property->setType($propertyTypeElevation);
        $property->setModelObject($observationPoint2);
        $entityManager->persist($property);

        $propertyValue = new PropertyValue();
        $propertyValue->setProperty($property);
        $propertyValue->setValue(100);
        $entityManager->persist($propertyValue);

        $property = new Property();
        $property->setType($propertyTypeElevation);
        $property->setModelObject($observationPoint2);
        $entityManager->persist($property);

        $propertyValue = new PropertyValue();
        $propertyValue->setProperty($property);
        $propertyValue->setValue(100);
        $entityManager->persist($propertyValue);

        $entityManager->flush();
    }

    private function addNewGeologicalUnitToGeologicalPoint(GeologicalPoint $geologicalPoint, Project $project, $name = "", $topElevation = 0, $bottomElevation = 0)
    {
        $geologicalUnit  = new GeologicalUnit($geologicalPoint->getOwner(), $project, $geologicalPoint->getPublic());
        $geologicalUnit->setName($name);
        $geologicalUnit->setTopElevation($topElevation);
        $geologicalUnit->setBottomElevation($bottomElevation);

        $geologicalUnit->setGeologicalPoint($geologicalPoint);

        return $geologicalPoint;
    }

    private function addGeologicalUnitToGeologicalLayer(ObjectManager $entityManager, GeologicalLayer $layer, $geologicalUnitName)
    {
        $geologicalUnit = $entityManager->getRepository('AppBundle:GeologicalUnit')
            ->findOneBy(array(
                'name' => $geologicalUnitName
            ));

        if ($geologicalUnit)
        {
            $layer->addGeologicalUnit($geologicalUnit);
        }

        return $layer;
    }
}