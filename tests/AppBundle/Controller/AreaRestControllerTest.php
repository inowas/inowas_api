<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Area;
use AppBundle\Entity\User;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\Point;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Tests\AppBundle\RestControllerTestCase;

class AreaRestControllerTest extends RestControllerTestCase
{
    /** @var  Area $area */
    protected $area_1;

    /** @var  Area $area */
    protected $area_2;


    public function setUp()
    {
        $owner = $this->getOwner();
        $this->getEntityManager()->persist($owner);

        $this->area_1 = AreaFactory::create()
            ->setOwner($owner)
            ->setName('ModelArea1')
            ->setAreaType('ModelAreaType')
            ->setPublic(true);
        $this->getEntityManager()->persist($this->area_1);
        $this->getEntityManager()->flush();

        $this->area_2 = AreaFactory::create()
            ->setOwner($owner)
            ->setName('ModelArea2')
            ->setAreaType('ModelAreaType')
            ->setPublic(true);

        $rings = array(
            new LineString(
                array(
                    new Point(11777056.49104572273790836, 2403440.17028302047401667),
                    new Point(11777973.94360375776886940, 2403506.49811625294387341),
                    new Point(11780228.12698311358690262, 2402856.26820704480633140),
                    new Point(11781703.59880801662802696, 2401713.22520185634493828),
                    new Point(11782192.89715446159243584, 2400859.20254275016486645),
                    new Point(11782678.03379831649363041, 2399224.82580633740872145),
                    new Point(11782955.64566324092447758, 2398372.03099954081699252),
                    new Point(11783586.59488865174353123, 2397659.24991086078807712),
                    new Point(11784427.14815393835306168, 2396590.66674219723790884),
                    new Point(11784914.27011025696992874, 2395382.18267500726506114),
                    new Point(11785330.82068796083331108, 2394174.15454542031511664),
                    new Point(11785536.96124399080872536, 2393180.11378513323143125),
                    new Point(11786097.12735226750373840, 2392467.84464810928329825),
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
                    new Point(11775189.21765423379838467, 2396638.40362721262499690),
                    new Point(11777056.49104572273790836, 2403440.17028302047401667)
                )
            )
        );

        /** @var Polygon $polygon */
        $polygon = new Polygon($rings, 3857);
        $this->area_2->setGeometry($polygon);
        $this->getEntityManager()->persist($this->area_2);
        $this->getEntityManager()->flush();
    }

    /**
     * Test for the API-Call /api/users/<username>/areas.json
     * which is providing a list of areas of the user
     */
    public function testGetAreaListWithoutAPIKeyReturns401()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/users/'.$this->getOwner()->getUsername().'/areas.json'
        );

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * Test for the API-Call /api/users/<username>/areas.json
     * which is providing a list of areas of the user
     */
    public function testAreaList()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/users/'.$this->getOwner()->getUsername().'/areas.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $modelAreas = json_decode($client->getResponse()->getContent());
        $this->assertEquals(2, count($modelAreas));
    }


    public function testAreaListWithUnknownUserReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/users/unknown_username/areas.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * Test for the API-Call /api/areas/<id>.json
     * which is providing a the details of a specific areas of the user
     */
    public function testAreaDetails()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/areas/'.$this->area_1->getId().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $area = json_decode($client->getResponse()->getContent());
        $this->assertEquals($area->id, $this->area_1->getId());
    }

    public function testDetailsWithInvalidIdReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/areas/unknown_area_id.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testDetailsWithUnknownIdReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/areas/ee3f68a1-7ffe-447c-9a67-bfe40850e1b8.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $user = $this->getEntityManager()->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $this->getOwner()->getUsername()
            ));
        $this->getEntityManager()->remove($user);

        $areas = $this->getEntityManager()->getRepository('AppBundle:Area')
            ->findBy(array(
                'owner' => $user
            ));

        foreach ($areas as $area) {
            $this->getEntityManager()->remove($area);
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->close();
    }
}
