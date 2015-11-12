<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Layer;
use AppBundle\Entity\ModelObject;
use AppBundle\Entity\ModelObjectProperty;
use AppBundle\Entity\ModelObjectPropertyType;
use AppBundle\Entity\Project;
use AppBundle\Entity\SoilProfile;
use AppBundle\Entity\SoilProfileLayer;
use AppBundle\Entity\TimeSeries;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadScenario_3 implements FixtureInterface, ContainerAwareInterface
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
        $user->setUsername('inowas_scenario_3');
        $user->setEmail('inowas_scenario_3@inowas.com');
        $user->setPassword('inowas_scenario_3');
        $userManager->updateUser($user);

        $project = new Project($user);
        $project->setName('Scenario 3');
        $entityManager->persist($project);

        // Create layers
        $layers = array();
        $layers[] = new Layer($user, $project);
        $layers[] = new Layer($user, $project);
        $layers[] = new Layer($user, $project);

        foreach ($layers as $layer)
        {
            $entityManager->persist($layer);
        }

        /**
         * boreholes
         * format csv
         * values: x, z, top elevation, elevation layer 1 bottom, elevation layer 2 bottom, elevation layer 3 bottom
         */
        $boreholes = array(
            array(11788180.14119624905288219, 2399553.70677717169746757, 6.9620555082, -15.620461, -16.6950614647, -29.6238878368),
            array(11786677.1576645914465189, 2399023.25597102148458362, 6.3898393629, -18.692347, -19.6996569609, -32.5665305456),
            array(11790319.33101668581366539, 2398894.851558992639184, 6.4946664581, -13.079302, -14.910420567, -30.8610677157),
            array(11787852.11418525315821171, 2398370.05450820876285434, 6.6255488629, -14.659299, -16.0398404293, -32.55306351),
            array(11788813.92677002400159836, 2397933.54498588852584362, 6.00167328, -13.052457, -14.6532666561, -32.1812023187),
            array(11786027.24900209158658981, 2397733.62118429923430085, 5.8840840611, -18.705297, -20.7075689585, -35.5333428407),
            array(11789988.11762658506631851, 2397172.48782889731228352, 4.2165512235, -7.0936039, -12.0379149657, -31.1265758612),
            array(11791380.79679805226624012, 2397164.43166950764134526, 4.3417676952, -10.386462, -12.8329227765, -38.8812598791),
            array(11788809.08773461729288101, 2397071.46049584727734327, 5.8281212776, -13.302987, -16.0170465372, -33.3523350985),
            array(11787630.06820404902100563, 2396970.35447654547169805, 6.3122125587, -19.153846, -19.4959900515, -36.3420303062),
            array(11789449.4274347797036171, 2396636.77293530059978366, 4.8102839087, -7.3111901, -12.3626654894, -31.2542124112),
            array(11786126.7311509121209383, 2396332.1691995277069509, 8.0264573378, -20.123346, -20.1233463996, -34.6577082023),
            array(11788587.59028892032802105, 2395779.62533995928242803, 5.4408550039, -9.0921872, -13.6747104681, -31.6138470968),
            array(11787194.41469062119722366, 2395679.70603070175275207, 6.6896371476, -25.315095, -25.9193635476, -39.5301528491),
            array(11790727.54822205752134323, 2395336.36947100562974811, 5.883913212, -12.022013, -13.6956019976, -42.7144197219),
            array(11789653.92750435508787632, 2394911.54699952248483896, 4.8793368059, -14.590854, -16.9008577469, -35.5136943279),
            array(11788792.7711452916264534, 2394162.22863991418853402, 7.4945438256, -18.060908, -20.4922798132, -31.0874280172),
            array(11786864.16385251842439175, 2394065.31637709401547909, 9.8397560393, -12.043455, -30.8630841769, -31.6942006527),
            array(11790286.8163007590919733, 2393184.02505666017532349, 5.1983350317, -9.573477, -10.2657631458, -33.081997754),
            array(11788463.01185098849236965, 2392655.74013845855370164, 9.6630525443, -13.489015, -14.6492345712, -29.4182375737)
        );

        foreach ($boreholes as $borehole)
        {
            $soilProfile = new SoilProfile($user, $project);
            $point = new Point($borehole[0], $borehole[1], 3857);
            $soilProfile->setPoint($point);
            $entityManager->persist($soilProfile);

            $soilProfileLayer = new SoilProfileLayer($user, $project);
            $soilProfileLayer->setSoilProfile($soilProfile);
            $soilProfileLayer->setTopElevation($borehole[2]);
            $soilProfileLayer->setBottomElevation($borehole[3]);

            /** @var Layer[] $layers */
            $layers[0]->addSoilProfileLayer($soilProfileLayer);
            $entityManager->persist($soilProfileLayer);

            $soilProfileLayer = new SoilProfileLayer($user, $project);
            $soilProfileLayer->setSoilProfile($soilProfile);
            $soilProfileLayer->setTopElevation($borehole[3]);
            $soilProfileLayer->setBottomElevation($borehole[4]);

            /** @var Layer[] $layers */
            $layers[1]->addSoilProfileLayer($soilProfileLayer);
            $entityManager->persist($soilProfileLayer);

            $soilProfileLayer = new SoilProfileLayer($user, $project);
            $soilProfileLayer->setSoilProfile($soilProfile);
            $soilProfileLayer->setTopElevation($borehole[4]);
            $soilProfileLayer->setBottomElevation($borehole[5]);

            /** @var Layer[] $layers */
            $layers[2]->addSoilProfileLayer($soilProfileLayer);
            $entityManager->persist($soilProfileLayer);
        }

        // Set ModelObjectProperties to layers
        // Layer 1 -> layers[0];
        $timeSeries = new TimeSeries();
        $timeSeries->setValue(40);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[0], 'Hydraulic conductivity', $timeSeries);
        $entityManager->persist($timeSeries);
        $entityManager->persist($modelObjectProperty);

        $timeSeries = new TimeSeries();
        $timeSeries->setValue(8);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[0], 'Vertical anisotropy', $timeSeries);
        $entityManager->persist($timeSeries);
        $entityManager->persist($modelObjectProperty);

        $timeSeries = new TimeSeries();
        $timeSeries->setValue(0.00001);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[0], 'Specific storage', $timeSeries);
        $entityManager->persist($timeSeries);
        $entityManager->persist($modelObjectProperty);

        $timeSeries = new TimeSeries();
        $timeSeries->setValue(0.1);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[0], 'Specific yield', $timeSeries);
        $entityManager->persist($timeSeries);
        $entityManager->persist($modelObjectProperty);

        $entityManager->flush();

        // Set ModelObjectProperties to layers
        // Layer 2 -> layers[1];
        $timeSeries = new TimeSeries();
        $timeSeries->setValue(1);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[0], 'Vertical conductance', $timeSeries);
        $entityManager->persist($timeSeries);
        $entityManager->persist($modelObjectProperty);

        $entityManager->flush();

        // Set ModelObjectProperties to layers
        // Layer 3 -> layers[2];
        $timeSeries = new TimeSeries();
        $timeSeries->setValue(42);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[0], 'Hydraulic conductivity', $timeSeries);
        $entityManager->persist($timeSeries);
        $entityManager->persist($modelObjectProperty);

        $timeSeries = new TimeSeries();
        $timeSeries->setValue(21);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[0], 'Vertical anisotropy', $timeSeries);
        $entityManager->persist($timeSeries);
        $entityManager->persist($modelObjectProperty);

        $timeSeries = new TimeSeries();
        $timeSeries->setValue(0.00001);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[0], 'Specific storage', $timeSeries);
        $entityManager->persist($timeSeries);
        $entityManager->persist($modelObjectProperty);

        $timeSeries = new TimeSeries();
        $timeSeries->setValue(0.1);
        $modelObjectProperty = $this->addModelObjectProperty($entityManager, $layers[0], 'Specific yield', $timeSeries);
        $entityManager->persist($timeSeries);
        $entityManager->persist($modelObjectProperty);

        $entityManager->flush();
    }

    public function addModelObjectProperty(EntityManagerInterface $entityManager, ModelObject $modelObject, $modelObjectPropertyTypeName , $timeSeries)
    {
        $modelObjectProperty = new ModelObjectProperty();
        $modelObjectPropertyType = $entityManager->getRepository('AppBundle:ModelObjectPropertyType')
            ->findOneBy(array(
                'name' => $modelObjectPropertyTypeName
            ));

        if (!$modelObjectPropertyType)
        {
            $modelObjectPropertyType = new ModelObjectPropertyType();
            $modelObjectPropertyType->setName($modelObjectPropertyTypeName);
            $entityManager->persist($modelObjectPropertyType);
        }

        $modelObjectProperty->setType($modelObjectPropertyType);

        /** @var TimeSeries $timeserie */
        foreach ($timeSeries as $timeserie)
        {
            $timeserie->setModelObjectProperties($modelObjectProperty);
            $modelObjectProperty->addTimeSeries($timeserie);

        }
        $modelObjectProperty->setModelObject($modelObject);
        return $modelObject;
    }
}