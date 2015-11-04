<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Area;
use AppBundle\Entity\AreaType;
use AppBundle\Entity\Layer;
use AppBundle\Entity\ObservationPoint;
use AppBundle\Entity\Project;

use AppBundle\Entity\SoilProfile;
use AppBundle\Entity\SoilProfileLayer;
use AppBundle\Entity\Stream;
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

        $project = new Project();
        $project->setName('Scenario 1');
        $project->setOwner($user);
        $entityManager->persist($project);

        // Create layers
        $layer1 = new Layer();
        $layer1->setOwner($user);
        $layer1->setPublic(false);

        $layer2 = new Layer();
        $layer2->setOwner($user);
        $layer2->setPublic(false);

        $layer3 = new Layer();
        $layer3->setOwner($user);
        $layer3->setPublic(false);

        // Add Soilprofile 1 with soilprofilelayers
        $soilProfile = new SoilProfile();
        $soilProfile->setOwner($user);
        $soilProfile->setPublic(false);
        $point = new Point(11772891.9650673, 2397519.89608855, 3857);
        $soilProfile->setPoint($point);
        $entityManager->persist($soilProfile);

        $soilProfileLayer = new SoilProfileLayer();
        $soilProfileLayer->setOwner($user);
        $soilProfileLayer->setPublic(false);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(100);
        $soilProfileLayer->setBottomElevation(70);
        $layer1->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        $soilProfileLayer = new SoilProfileLayer();
        $soilProfileLayer->setOwner($user);
        $soilProfileLayer->setPublic(false);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(70);
        $soilProfileLayer->setBottomElevation(40);
        $layer2->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        $soilProfileLayer = new SoilProfileLayer();
        $soilProfileLayer->setOwner($user);
        $soilProfileLayer->setPublic(false);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(40);
        $soilProfileLayer->setBottomElevation(0);
        $layer3->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        // Add Soilprofile 2 with soilprofilelayers
        $soilProfile = new SoilProfile();
        $soilProfile->setOwner($user);
        $soilProfile->setPublic(false);
        $point = new Point(11786103.1301754, 2397138.80478736, 3857);
        $soilProfile->setPoint($point);
        $entityManager->persist($soilProfile);

        $soilProfileLayer = new SoilProfileLayer();
        $soilProfileLayer->setOwner($user);
        $soilProfileLayer->setPublic(false);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(100);
        $soilProfileLayer->setBottomElevation(70);
        $layer1->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        $soilProfileLayer = new SoilProfileLayer();
        $soilProfileLayer->setOwner($user);
        $soilProfileLayer->setPublic(false);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(70);
        $soilProfileLayer->setBottomElevation(40);
        $layer2->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        $soilProfileLayer = new SoilProfileLayer();
        $soilProfileLayer->setOwner($user);
        $soilProfileLayer->setPublic(false);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(40);
        $soilProfileLayer->setBottomElevation(0);
        $layer3->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        // Add Soilprofile 3 with soilprofilelayers
        $soilProfile = new SoilProfile();
        $soilProfile->setOwner($user);
        $soilProfile->setPublic(false);
        $point = new Point(11779836.2954446, 2387061.05704468, 3857);
        $soilProfile->setPoint($point);
        $entityManager->persist($soilProfile);

        $soilProfileLayer = new SoilProfileLayer();
        $soilProfileLayer->setOwner($user);
        $soilProfileLayer->setPublic(false);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(100);
        $soilProfileLayer->setBottomElevation(70);
        $layer1->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        $soilProfileLayer = new SoilProfileLayer();
        $soilProfileLayer->setOwner($user);
        $soilProfileLayer->setPublic(false);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(70);
        $soilProfileLayer->setBottomElevation(40);
        $layer2->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        $soilProfileLayer = new SoilProfileLayer();
        $soilProfileLayer->setOwner($user);
        $soilProfileLayer->setPublic(false);
        $soilProfileLayer->setSoilProfile($soilProfile);
        $soilProfileLayer->setTopElevation(40);
        $soilProfileLayer->setBottomElevation(0);
        $layer3->addSoilProfileLayer($soilProfileLayer);
        $entityManager->persist($soilProfileLayer);

        $entityManager->persist($layer1);
        $entityManager->persist($layer2);
        $entityManager->persist($layer3);

        // Set stream
        $stream = new Stream();
        $stream->setOwner($user);
        $stream->setPublic(false);
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

        $area = new Area();
        $area->setOwner($user);
        $area->setAreaType($areaType);
        $area->setPublic(false);

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
        $observationPoint = new ObservationPoint();
        $observationPoint->setOwner($user);
        $observationPoint->setPublic(false);
        $point = new Point(11778481.3041515, 2393327.89177542, 3857);
        $observationPoint->setPoint($point);
        $observationPoint->setElevation(100);
        $entityManager->persist($observationPoint);
        $area->addObservationPoint($observationPoint);

        $observationPoint = new ObservationPoint();
        $observationPoint->setOwner($user);
        $observationPoint->setPublic(false);
        $point = new Point(11772891.9650673, 2397519.89608855, 3857);
        $observationPoint->setPoint($point);
        $observationPoint->setElevation(100);
        $entityManager->persist($observationPoint);
        $area->addObservationPoint($observationPoint);

        $observationPoint = new ObservationPoint();
        $observationPoint->setOwner($user);
        $observationPoint->setPublic(false);
        $point = new Point(11786103.1301754, 2397138.80478736, 3857);
        $observationPoint->setPoint($point);
        $observationPoint->setElevation(100);
        $entityManager->persist($observationPoint);
        $area->addObservationPoint($observationPoint);

        $entityManager->persist($area);
        $entityManager->flush();
    }
}