<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Area;
use AppBundle\Entity\AreaType;
use AppBundle\Entity\Boundary;
use AppBundle\Entity\Layer;
use AppBundle\Entity\ModelObjectProperty;
use AppBundle\Entity\ModelObjectPropertyType;
use AppBundle\Entity\ObservationPoint;
use AppBundle\Entity\Project;

use AppBundle\Entity\SoilProfile;
use AppBundle\Entity\SoilProfileLayer;
use AppBundle\Entity\Stream;
use AppBundle\Entity\TimeSeries;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class LoadUserData implements FixtureInterface, ContainerAwareInterface
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

        $user = $userManager->createUser();
        $user->setUsername('inowas_scenario_1');
        $user->setEmail('inowas_scenario_1@inowas.com');
        $user->setPassword('inowas_scenario_1');
        $userManager->updateUser($user);

        $project = new Project($user);
        $project->setName('Scenario 1');
        $entityManager->persist($project);

        // Create layers
        $layer1 = new Layer($user);
        $layer1->addProject($project);
        $layer2 = new Layer($user);
        $layer2->addProject($project);
        $layer3 = new Layer($user);
        $layer3->addProject($project);

        // Add Soilprofile 1 with soilprofilelayers
        $soilProfile = new SoilProfile($user);
        $soilProfile->addProject($project);
        $point = new Point(11772891.9650673, 2397519.89608855, 3857);
        $soilProfile->setPoint($point);
        $entityManager->persist($soilProfile);

        $soilProfileLayer = new SoilProfileLayer($user);
        $soilProfileLayer->addProject($project);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(100);
        $soilProfileLayer->setBottomElevation(70);
        $layer1->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        $soilProfileLayer = new SoilProfileLayer($user);
        $soilProfileLayer->addProject($project);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(70);
        $soilProfileLayer->setBottomElevation(40);
        $layer2->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        $soilProfileLayer = new SoilProfileLayer($user);
        $soilProfileLayer->addProject($project);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(40);
        $soilProfileLayer->setBottomElevation(0);
        $layer3->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        // Add Soilprofile 2 with soilprofilelayers
        $soilProfile = new SoilProfile($user);
        $soilProfile->addProject($project);
        $point = new Point(11786103.1301754, 2397138.80478736, 3857);
        $soilProfile->setPoint($point);
        $entityManager->persist($soilProfile);

        $soilProfileLayer = new SoilProfileLayer($user);
        $soilProfileLayer->addProject($project);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(100);
        $soilProfileLayer->setBottomElevation(70);
        $layer1->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        $soilProfileLayer = new SoilProfileLayer($user);
        $soilProfileLayer->addProject($project);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(70);
        $soilProfileLayer->setBottomElevation(40);
        $layer2->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        $soilProfileLayer = new SoilProfileLayer($user);
        $soilProfileLayer->addProject($project);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(40);
        $soilProfileLayer->setBottomElevation(0);
        $layer3->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        // Add Soilprofile 3 with soilprofilelayers
        $soilProfile = new SoilProfile($user);
        $soilProfile->addProject($project);
        $point = new Point(11779836.2954446, 2387061.05704468, 3857);
        $soilProfile->setPoint($point);
        $entityManager->persist($soilProfile);

        $soilProfileLayer = new SoilProfileLayer($user);
        $soilProfileLayer->addProject($project);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(100);
        $soilProfileLayer->setBottomElevation(70);
        $layer1->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        $soilProfileLayer = new SoilProfileLayer($user);
        $soilProfileLayer->addProject($project);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(70);
        $soilProfileLayer->setBottomElevation(40);
        $layer2->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        $soilProfileLayer = new SoilProfileLayer($user);
        $soilProfileLayer->addProject($project);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(40);
        $soilProfileLayer->setBottomElevation(0);
        $layer3->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        $entityManager->persist($layer1);
        $entityManager->persist($layer2);
        $entityManager->persist($layer3);

        // Set stream
        $stream = new Stream($user);
        $stream->addProject($project);
        $startingPoint = new Point(11777338.0302479, 2395656.78306049, 3857);
        $stream->setStartingPoint($startingPoint);

        $lineCoordinates = array(
            array(11766937.6721201, 2380245.03544451),
            array(11772341.4998545, 2386595.27878767),
            array(11777338.0302479, 2395656.78306049)
        );
        $line = new LineString($lineCoordinates, 3857);
        $stream->setLine($line);
        $entityManager->persist($stream);

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
        $observationPoint1 = new ObservationPoint($user);
        $observationPoint1->addProject($project);
        $point = new Point(11778481.3041515, 2393327.89177542, 3857);
        $observationPoint1->setPoint($point);
        $observationPoint1->setElevation(100);
        $entityManager->persist($observationPoint1);
        $area->addObservationPoint($observationPoint1);

        $observationPoint2 = new ObservationPoint($user);
        $observationPoint2->addProject($project);
        $point = new Point(11772891.9650673, 2397519.89608855, 3857);
        $observationPoint2->setPoint($point);
        $observationPoint2->setElevation(100);
        $entityManager->persist($observationPoint2);
        $area->addObservationPoint($observationPoint2);

        $observationPoint3 = new ObservationPoint($user);
        $observationPoint3->addProject($project);
        $point = new Point(11786103.1301754, 2397138.80478736, 3857);
        $observationPoint3->setPoint($point);
        $observationPoint3->setElevation(100);
        $entityManager->persist($observationPoint3);

        $area->addObservationPoint($observationPoint3);
        $entityManager->persist($area);

        // Load boundary
        $boundary = new Boundary($user);
        $boundary->addProject($project);
        $lineCoordinates = array(
            array(11767778.4794313, 2403329.01798664),
            array(11766937.6721201, 2380245.03544451),
            array(11791168.2100865, 2379939.28733137)
        );
        $line = new LineString($lineCoordinates, 3857);
        $boundary->setGeometry($line);
        $entityManager->persist($boundary);

        // Add ModelObjectPropertyTypes
        $propertyTypeGwHead = new ModelObjectPropertyType();
        $propertyTypeGwHead->setName("gwhead");
        $entityManager->persist($propertyTypeGwHead);

        $propertyTypeElevation = new ModelObjectPropertyType();
        $propertyTypeElevation->setName("elevation");
        $entityManager->persist($propertyTypeElevation);

        // Add ModelObjectProperties and TimeSeries
        $modelObjectProperty = new ModelObjectProperty();
        $modelObjectProperty->setType($propertyTypeGwHead);
        $modelObjectProperty->setModelObject($observationPoint1);
        $entityManager->persist($modelObjectProperty);

        $timeSeries = new TimeSeries();
        $timeSeries->setModelObjectProperties($modelObjectProperty);
        $timeSeries->setTimeStamp(new \DateTime('2015-01-01 00:00:00'));
        $timeSeries->setValue(50);
        $entityManager->persist($timeSeries);

        $timeSeries = new TimeSeries();
        $timeSeries->setModelObjectProperties($modelObjectProperty);
        $timeSeries->setTimeStamp(new \DateTime('2015-02-01 00:00:00'));
        $timeSeries->setValue(51);
        $entityManager->persist($timeSeries);

        $timeSeries = new TimeSeries();
        $timeSeries->setModelObjectProperties($modelObjectProperty);
        $timeSeries->setTimeStamp(new \DateTime('2015-03-01 00:00:00'));
        $timeSeries->setValue(52);
        $entityManager->persist($timeSeries);

        $timeSeries = new TimeSeries();
        $timeSeries->setModelObjectProperties($modelObjectProperty);
        $timeSeries->setTimeStamp(new \DateTime('2015-04-01 00:00:00'));
        $timeSeries->setValue(53);
        $entityManager->persist($timeSeries);

        $timeSeries = new TimeSeries();
        $timeSeries->setModelObjectProperties($modelObjectProperty);
        $timeSeries->setTimeStamp(new \DateTime('2015-05-01 00:00:00'));
        $timeSeries->setValue(54);
        $entityManager->persist($timeSeries);

        $modelObjectProperty = new ModelObjectProperty();
        $modelObjectProperty->setType($propertyTypeGwHead);
        $modelObjectProperty->setModelObject($boundary);
        $entityManager->persist($modelObjectProperty);

        $timeSeries = new TimeSeries();
        $timeSeries->setModelObjectProperties($modelObjectProperty);
        $timeSeries->setValue(60);
        $entityManager->persist($timeSeries);

        $modelObjectProperty = new ModelObjectProperty();
        $modelObjectProperty->setType($propertyTypeElevation);
        $modelObjectProperty->setModelObject($observationPoint1);
        $entityManager->persist($modelObjectProperty);

        $timeSeries = new TimeSeries();
        $timeSeries->setModelObjectProperties($modelObjectProperty);
        $timeSeries->setValue(100);
        $entityManager->persist($timeSeries);

        $modelObjectProperty = new ModelObjectProperty();
        $modelObjectProperty->setType($propertyTypeElevation);
        $modelObjectProperty->setModelObject($observationPoint1);
        $entityManager->persist($modelObjectProperty);

        $timeSeries = new TimeSeries();
        $timeSeries->setModelObjectProperties($modelObjectProperty);
        $timeSeries->setValue(100);
        $entityManager->persist($timeSeries);

        $modelObjectProperty = new ModelObjectProperty();
        $modelObjectProperty->setType($propertyTypeElevation);
        $modelObjectProperty->setModelObject($observationPoint1);
        $entityManager->persist($modelObjectProperty);

        $timeSeries = new TimeSeries();
        $timeSeries->setModelObjectProperties($modelObjectProperty);
        $timeSeries->setValue(100);
        $entityManager->persist($timeSeries);

        $entityManager->flush();
    }
}