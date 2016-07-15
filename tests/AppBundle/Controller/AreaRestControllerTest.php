<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Area;
use AppBundle\Entity\AreaType;
use AppBundle\Entity\User;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\AreaTypeFactory;
use AppBundle\Model\Point;
use AppBundle\Model\UserFactory;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AreaRestControllerTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var User $owner */
    protected $owner;

    /** @var AreaType $areaType*/
    protected $areaType;

    /** @var  Area $area */
    protected $area_1;

    /** @var  Area $area */
    protected $area_2;


    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->owner = UserFactory::createTestUser('AreaOwner');
        $this->entityManager->persist($this->owner);

        $this->areaType = AreaTypeFactory::setName('ModelAreaType');
        $this->entityManager->persist($this->areaType);

        $this->area_1 = AreaFactory::create()
            ->setOwner($this->owner)
            ->setName('ModelArea1')
            ->setAreaType($this->areaType)
            ->setPublic(true);
        $this->entityManager->persist($this->area_1);
        $this->entityManager->flush();

        $this->area_2 = AreaFactory::create()
            ->setOwner($this->owner)
            ->setName('ModelArea2')
            ->setAreaType($this->areaType)
            ->setPublic(true);

        $rings = array(
            new LineString(
                array(
                    new Point(11777056.49104572273790836, 2403440.17028302047401667),
                    new Point(11777973.9436037577688694, 2403506.49811625294387341),
                    new Point(11780228.12698311358690262, 2402856.2682070448063314),
                    new Point(11781703.59880801662802696, 2401713.22520185634493828),
                    new Point(11782192.89715446159243584, 2400859.20254275016486645),
                    new Point(11782678.03379831649363041, 2399224.82580633740872145),
                    new Point(11782955.64566324092447758, 2398372.03099954081699252),
                    new Point(11783586.59488865174353123, 2397659.24991086078807712),
                    new Point(11784427.14815393835306168, 2396590.66674219723790884),
                    new Point(11784914.27011025696992874, 2395382.18267500726506114),
                    new Point(11785330.82068796083331108, 2394174.15454542031511664),
                    new Point(11785536.96124399080872536, 2393180.11378513323143125),
                    new Point(11786097.1273522675037384, 2392467.84464810928329825),
                    new Point(11787011.69080197438597679, 2392108.19440084183588624),
                    new Point(11787715.90038010291755199, 2391962.42985267844051123),
                    new Point(11788487.82464707084000111, 2391319.86146369902417064),
                    new Point(11789680.65233467146754265, 2390320.33801258727908134),
                    new Point(11789747.53923093341290951, 2389681.79035578016191721),
                    new Point(11789176.05731181986629963, 2388337.88133400911465287),
                    new Point(11788252.26803966984152794, 2386996.03587882174178958),
                    new Point(11787540.82363948784768581, 2385794.83458124194294214),
                    new Point(11783036.01740818470716476, 2386882.81766726961359382),
                    new Point(11777486.37431096099317074, 2390598.53498441586270928),
                    new Point(11775189.21765423379838467, 2396638.4036272126249969),
                    new Point(11777056.49104572273790836, 2403440.17028302047401667)
                )
            )
        );

        /** @var Polygon $polygon */
        $polygon = new Polygon($rings, 3857);
        $this->area_2->setGeometry($polygon);
        $this->entityManager->persist($this->area_2);
        $this->entityManager->flush();
    }

    /**
     * Test for the API-Call /api/users/<username>/areas.json
     * which is providing a list of areas of the user
     */
    public function testAreaList()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/'.$this->owner->getUsername().'/areas.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $modelAreas = json_decode($client->getResponse()->getContent());
        $this->assertEquals(2, count($modelAreas));
    }


    public function testAreaListWithUnknownUserReturns404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/unknown_username/areas.json');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * Test for the API-Call /api/areas/<id>.json
     * which is providing a the details of a specific areas of the user
     */
    public function testAreaDetails()
    {
        $client = static::createClient();
        $client->request('GET', '/api/areas/'.$this->area_1->getId().'.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $area = json_decode($client->getResponse()->getContent());
        $this->assertEquals($area->id, $this->area_1->getId());
    }

    public function testDetailsWithInvalidIdReturns404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/areas/unknown_area_id.json');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testDetailsWithUnknownIdReturns404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/areas/ee3f68a1-7ffe-447c-9a67-bfe40850e1b8.json');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $user = $this->entityManager->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $this->owner->getUsername()
            ));
        $this->entityManager->remove($user);

        $areas = $this->entityManager->getRepository('AppBundle:Area')
            ->findBy(array(
                'owner' => $user
            ));

        foreach ($areas as $area) {
            $this->entityManager->remove($area);
        }

        $areaType = $this->entityManager->getRepository('AppBundle:AreaType')
            ->findOneBy(array(
               'id' => $this->areaType->getId()
            ));
        
        $this->entityManager->remove($areaType);

        $this->entityManager->flush();
        $this->entityManager->close();
    }
}
