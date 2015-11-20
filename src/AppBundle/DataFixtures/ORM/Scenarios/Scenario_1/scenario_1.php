<?php

namespace AppBundle\DataFixtures\ORM\Scenarios\Scenario_1;

use AppBundle\Entity\Area;
use AppBundle\Entity\AreaType;
use AppBundle\Entity\Boundary;
use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyTimeValue;
use AppBundle\Entity\PropertyType;
use AppBundle\Entity\ObservationPoint;
use AppBundle\Entity\Project;

use AppBundle\Entity\GeologicalPoint;
use AppBundle\Entity\GeologicalUnit;
use AppBundle\Entity\PropertyValue;
use AppBundle\Entity\Stream;
use AppBundle\Entity\GeologicalLayer;
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
        $project = new Project($user);
        $project->setName('Scenario 1');
        $entityManager->persist($project);
        $entityManager->flush();


        // Create all Layers
        // Create layers
        $geologicalLayer1 = new GeologicalLayer($user, $project);
        $entityManager->persist($geologicalLayer1);
        $geologicalLayer2 = new GeologicalLayer($user, $project);
        $entityManager->persist($geologicalLayer2);
        $geologicalLayer3 = new GeologicalLayer($user, $project);
        $entityManager->persist($geologicalLayer3);
        $entityManager->flush();

        // Add Geological Profiles
        $geologicalPoint = new GeologicalPoint($user, $project);
        $geologicalPoint->setPoint(new Point(11772891.9650673, 2397519.89608855, 3857));
        $entityManager->persist($geologicalPoint);
        $entityManager->flush();

        $geologicalUnit = new GeologicalUnit($user, $project);
        $geologicalUnit->setGeologicalPoint($geologicalPoint);
        $geologicalUnit->setTopElevation(100);
        $geologicalUnit->setBottomElevation(70);
        $geologicalLayer1->addGeologicalUnit($geologicalUnit);
        $entityManager->persist($geologicalUnit);
        $entityManager->flush();

        $geologicalUnit = new GeologicalUnit($user, $project);
        $geologicalUnit->setGeologicalPoint($geologicalPoint);
        $geologicalUnit->setTopElevation(70);
        $geologicalUnit->setBottomElevation(40);
        $geologicalLayer2->addGeologicalUnit($geologicalUnit);
        $entityManager->persist($geologicalUnit);
        $entityManager->flush();

        $geologicalUnit = new GeologicalUnit($user, $project);
        $geologicalUnit->setGeologicalPoint($geologicalPoint);
        $geologicalUnit->setTopElevation(40);
        $geologicalUnit->setBottomElevation(0);
        $geologicalLayer3->addGeologicalUnit($geologicalUnit);
        $entityManager->persist($geologicalUnit);
        $entityManager->flush();


        // Add Geological Profiles
        $geologicalPoint = new GeologicalPoint($user, $project);
        $geologicalPoint->setPoint(new Point(11786103.1301754, 2397138.80478736, 3857));
        $entityManager->persist($geologicalPoint);
        $entityManager->flush();

        $geologicalUnit = new GeologicalUnit($user, $project);
        $geologicalUnit->setGeologicalPoint($geologicalPoint);
        $geologicalUnit->setTopElevation(100);
        $geologicalUnit->setBottomElevation(70);
        $geologicalLayer1->addGeologicalUnit($geologicalUnit);
        $entityManager->persist($geologicalUnit);
        $entityManager->flush();

        $geologicalUnit = new GeologicalUnit($user, $project);
        $geologicalUnit->setGeologicalPoint($geologicalPoint);
        $geologicalUnit->setTopElevation(70);
        $geologicalUnit->setBottomElevation(40);
        $geologicalLayer2->addGeologicalUnit($geologicalUnit);
        $entityManager->persist($geologicalUnit);
        $entityManager->flush();

        $geologicalUnit = new GeologicalUnit($user, $project);
        $geologicalUnit->setGeologicalPoint($geologicalPoint);
        $geologicalUnit->setTopElevation(40);
        $geologicalUnit->setBottomElevation(0);
        $geologicalLayer3->addGeologicalUnit($geologicalUnit);
        $entityManager->persist($geologicalUnit);
        $entityManager->flush();

        // Add Geological Profiles
        $geologicalPoint = new GeologicalPoint($user, $project);
        $geologicalPoint->setPoint(new Point(11779836.2954446, 2387061.05704468, 3857));
        $entityManager->persist($geologicalPoint);
        $entityManager->flush();

        $geologicalUnit = new GeologicalUnit($user, $project);
        $geologicalUnit->setGeologicalPoint($geologicalPoint);
        $geologicalUnit->setTopElevation(100);
        $geologicalUnit->setBottomElevation(70);
        $geologicalLayer1->addGeologicalUnit($geologicalUnit);
        $entityManager->persist($geologicalUnit);
        $entityManager->flush();

        $geologicalUnit = new GeologicalUnit($user, $project);
        $geologicalUnit->setGeologicalPoint($geologicalPoint);
        $geologicalUnit->setTopElevation(70);
        $geologicalUnit->setBottomElevation(40);
        $geologicalLayer2->addGeologicalUnit($geologicalUnit);
        $entityManager->persist($geologicalUnit);
        $entityManager->flush();

        $geologicalUnit = new GeologicalUnit($user, $project);
        $geologicalUnit->setGeologicalPoint($geologicalPoint);
        $geologicalUnit->setTopElevation(40);
        $geologicalUnit->setBottomElevation(0);
        $geologicalLayer3->addGeologicalUnit($geologicalUnit);
        $entityManager->persist($geologicalUnit);
        $entityManager->flush();

        // Add new stream
        $stream = new Stream($user, $project);
        $stream->setStartingPoint(new Point(11777338.0302479, 2395656.78306049, 3857));
        $lineCoordinates = array(
            array(11766937.6721201, 2380245.03544451),
            array(11772341.4998545, 2386595.27878767),
            array(11777338.0302479, 2395656.78306049)
        );
        $line = new LineString($lineCoordinates, 3857);
        $stream->setLine($line);
        $entityManager->persist($stream);
        $entityManager->flush();

        // Add new Area
        $areaType = new AreaType();
        $areaType->setName('model_area');
        $entityManager->persist($areaType);

        $area = new Area($user);
        $area->addProject($project);
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
}