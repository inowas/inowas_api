<?php

namespace AppBundle\DataFixtures\ORM\Scenarios\Scenario_1;

use AppBundle\Entity\User;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\AreaTypeFactory;
use AppBundle\Model\BoundaryFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\ObservationPointFactory;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyTimeValueFactory;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\StreamFactory;

use AppBundle\Model\Point;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
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
            $entityManager->flush();
        }

        // Add new Project
        #$project = ProjectFactory::setOwnerAndPublic($user, $public);
        #$project->setName('Scenario 1');
        #$project->setDescription('This is not a real example.<br>The data was stored only for testing purposes.<br>The Data contains 3 geological points with respectively 3 geological units.');
        #$entityManager->persist($project);
        #$entityManager->flush();

        // Add Geological Profile 1
        $geologicalPoint = GeologicalPointFactory::setOwnerNameAndPoint($user, 'SC1_GP1', new Point(11772891.9650673, 2397519.89608855, 4326), $public);
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, 'SC1_GP1.1', 100, 70);
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, 'SC1_GP1.2',  70, 40);
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, 'SC1_GP1.3',  40,  0);
        $entityManager->persist($geologicalPoint);
        $entityManager->flush();

        // Add Geological Profile 2
        $geologicalPoint = GeologicalPointFactory::setOwnerNameAndPoint($user, 'SC1_GP2', new Point(11786103.1301754, 2397138.80478736, 4326), $public);
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, 'SC1_GP2.1', 100, 70);
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, 'SC1_GP2.2',  70, 40);
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, 'SC1_GP2.3',  40,  0);
        $entityManager->persist($geologicalPoint);
        $entityManager->flush();

        // Add Geological Profile 3
        $geologicalPoint = GeologicalPointFactory::setOwnerNameAndPoint($user, 'SC1_GP3', new Point(11779836.2954446, 2387061.05704468, 4326), $public);
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, 'SC1_GP3.1', 100, 70);
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, 'SC1_GP3.2',  70, 40);
        $geologicalPoint = $this->addNewGeologicalUnitToGeologicalPoint($geologicalPoint, 'SC1_GP3.3',  40,  0);
        $entityManager->persist($geologicalPoint);
        $entityManager->flush();

        // Create layer 1
        $geologicalLayer = GeologicalLayerFactory::setOwnerNameAndPublic($user, 'SC1_L1', $public);
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GP1.1');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GP2.1');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GP3.1');
        $entityManager->persist($geologicalLayer);
        $entityManager->flush();

        // Create layer 2
        $geologicalLayer = GeologicalLayerFactory::setOwnerNameAndPublic($user, 'SC1_L2', $public);
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GP1.2');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GP2.2');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GP3.2');
        $entityManager->persist($geologicalLayer);
        $entityManager->flush();

        // Create layer 3
        $geologicalLayer = GeologicalLayerFactory::setOwnerNameAndPublic($user, 'SC1_L3', $public);
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GP1.3');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GP2.3');
        $geologicalLayer = $this->addGeologicalUnitToGeologicalLayer($entityManager, $geologicalLayer, 'SC1_GP3.3');
        $entityManager->persist($geologicalLayer);
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

        // Add new Areatype
        $areaType = AreaTypeFactory::setName('SC1_AT1');
        $entityManager->persist($areaType);
        $entityManager->flush();

        // Add new Area
        $area = AreaFactory::setOwnerNameTypeAndPublic($user, 'SC1_A1', $areaType, $public);
        $coordinates = array(
            array(11767778.4794313, 2403329.01798664),
            array(11791015.33603, 2403329.01798664),
            array(11791168.2100865, 2379939.28733137),
            array(11766937.6721201, 2380245.03544451),
            array(11767778.4794313, 2403329.01798664)
        );
        $line = new LineString($coordinates);
        $polygon = new Polygon(array($line), 4326);
        $area->setGeometry($polygon);

        // Create ObservationPoints for area
        $observationPoint = ObservationPointFactory::setOwnerNameAndPoint($user, 'SC1_OP1', new Point(11778481.3041515, 2393327.89177542, 4326), $public);
        $observationPoint->setElevation(100);
        $entityManager->persist($observationPoint);
        $area->addObservationPoint($observationPoint);

        $observationPoint = ObservationPointFactory::setOwnerNameAndPoint($user, 'SC1_OP2', new Point(11772891.9650673, 2397519.89608855, 4326), $public);
        $observationPoint->setElevation(100);
        $entityManager->persist($observationPoint);
        $area->addObservationPoint($observationPoint);

        $observationPoint = ObservationPointFactory::setOwnerNameAndPoint($user, 'SC1_OP3', new Point(11786103.1301754, 2397138.80478736, 4326), $public);
        $observationPoint->setElevation(100);
        $entityManager->persist($observationPoint);
        $area->addObservationPoint($observationPoint);

        $entityManager->persist($area);
        $entityManager->flush();

        // Create boundary
        $boundary = BoundaryFactory::setOwnerNameAndPublic($user, 'SC1_B1', $public);
        $lineCoordinates = array(
            array(11767778.4794313, 2403329.01798664),
            array(11766937.6721201, 2380245.03544451),
            array(11791168.2100865, 2379939.28733137)
        );
        $line = new LineString($lineCoordinates, 4326);
        $boundary->setGeometry($line);
        $entityManager->persist($boundary);
        $entityManager->flush();

        // Add ModelObjectPropertyTypes
        $propertyTypeGwHead = PropertyTypeFactory::setName("gwhead");
        $entityManager->persist($propertyTypeGwHead);

        $propertyTypeElevation = PropertyTypeFactory::setName("elevation");
        $entityManager->persist($propertyTypeElevation);

        // Add Property GWHead and TimeValues to ObservationPoint SC1_OP1
        $observationPoint = $entityManager->getRepository('AppBundle:ObservationPoint')
            ->findOneBy(array(
                'name' => 'SC1_OP1'
            ));
        $property = PropertyFactory::setTypeAndModelObject($propertyTypeGwHead, $observationPoint);
        $entityManager->persist($property);

        $propertyTimeValue = PropertyTimeValueFactory::setPropertyDateTimeAndValue($property, new \DateTime('2015-01-01 00:00:00'), 50);
        $entityManager->persist($propertyTimeValue);

        $propertyTimeValue = PropertyTimeValueFactory::setPropertyDateTimeAndValue($property, new \DateTime('2015-02-01 00:00:00'), 51);
        $entityManager->persist($propertyTimeValue);

        $propertyTimeValue = PropertyTimeValueFactory::setPropertyDateTimeAndValue($property, new \DateTime('2015-03-01 00:00:00'), 52);
        $entityManager->persist($propertyTimeValue);

        $propertyTimeValue = PropertyTimeValueFactory::setPropertyDateTimeAndValue($property, new \DateTime('2015-04-01 00:00:00'), 53);
        $entityManager->persist($propertyTimeValue);

        $propertyTimeValue = PropertyTimeValueFactory::setPropertyDateTimeAndValue($property, new \DateTime('2015-05-01 00:00:00'), 54);
        $entityManager->persist($propertyTimeValue);

        // Add Property Elevation and TimeValues to ObservationPoint OP1
        $property = PropertyFactory::setTypeAndModelObject($propertyTypeElevation, $observationPoint);
        $entityManager->persist($property);

        $propertyValue = PropertyValueFactory::setPropertyAndValue($property, 100);
        $entityManager->persist($propertyValue);


        // Add Property GWHead and TimeValues to ObservationPoint OP2
        $observationPoint = $entityManager->getRepository('AppBundle:ObservationPoint')
            ->findOneBy(array(
                'name' => 'SC1_OP2'
            ));

        $property = PropertyFactory::setTypeAndModelObject($propertyTypeElevation, $observationPoint);
        $entityManager->persist($property);

        $propertyValue = PropertyValueFactory::setPropertyAndValue($property, 100);
        $entityManager->persist($propertyValue);

        // Add Property GWHead and TimeValues to ObservationPoint OP2
        $observationPoint = $entityManager->getRepository('AppBundle:ObservationPoint')
            ->findOneBy(array(
                'name' => 'SC1_OP3'
            ));

        $property = PropertyFactory::setTypeAndModelObject($propertyTypeElevation, $observationPoint);
        $entityManager->persist($property);

        $propertyValue = PropertyValueFactory::setPropertyAndValue($property, 100);
        $entityManager->persist($propertyValue);

        // Add Property, Values to Boundary B1
        $boundary = $entityManager->getRepository('AppBundle:Boundary')
            ->findOneBy(array(
                'name' => 'SC1_B1'
            ));

        $property = PropertyFactory::setTypeAndModelObject($propertyTypeGwHead, $boundary);
        $entityManager->persist($property);

        $propertyValue = PropertyValueFactory::setPropertyAndValue($property, 60);
        $entityManager->persist($propertyValue);

        $entityManager->flush();
    }

    private function addNewGeologicalUnitToGeologicalPoint(\AppBundle\Entity\GeologicalPoint $geologicalPoint, $name = "", $topElevation = 0, $bottomElevation = 0)
    {
        $geologicalUnit  = GeologicalUnitFactory::setOwnerNameAndPublic($geologicalPoint->getOwner(), $name, $geologicalPoint->getPublic());
        $geologicalUnit->setTopElevation($topElevation);
        $geologicalUnit->setBottomElevation($bottomElevation);
        $geologicalUnit->setGeologicalPoint($geologicalPoint);
        return $geologicalPoint;
    }

    private function addGeologicalUnitToGeologicalLayer(ObjectManager $entityManager, \AppBundle\Entity\GeologicalLayer $layer, $geologicalUnitName)
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