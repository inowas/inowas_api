<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\WellBoundary;
use AppBundle\Model\Point;
use AppBundle\Model\WellBoundaryFactory;
use Tests\AppBundle\RestControllerTestCase;

class WellRestControllerTest extends RestControllerTestCase
{
    /** @var  WellBoundary $well */
    protected $well;

    public function setUp()
    {
        $this->getEntityManager()->persist($this->getOwner());
        $this->getEntityManager()->flush();

        $this->well = WellBoundaryFactory::create()
            ->setName('Well')
            ->setPublic(true)
            ->setOwner($this->getOwner())
            ->setPoint(new Point(10.1, 11.1, 3568))
        ;

        $this->getEntityManager()->persist($this->well);
        $this->getEntityManager()->flush();
    }

    /**
     * Test for the API-Call /api/users/<username>/wells.json
     */
    public function testWellList()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/users/'.$this->getOwner()->getUsername().'/wells.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $wells = json_decode($client->getResponse()->getContent());
        $this->assertEquals(1, count($wells));
        $well = $wells[0];
        $this->assertObjectHasAttribute('id', $well);
        $this->assertEquals($this->well->getId(), $well->id);
        $this->assertObjectHasAttribute('name', $well);
        $this->assertEquals($this->well->getName(), $well->name);
    }

    /**
     * Test for the API-Call /api/wells/<id>.json
     */
    public function testWellDetails()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/wells/'.$this->well->getId().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $well = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $well);
        $this->assertEquals($this->well->getId(), $well->id);
        $this->assertObjectHasAttribute('name', $well);
        $this->assertEquals($this->well->getName(), $well->name);
        $this->assertObjectHasAttribute('point', $well);
        $point = $well->point;
        $this->assertEquals($this->well->getPoint()->getX(), $point->x);
        $this->assertEquals($this->well->getPoint()->getY(), $point->y);
        $this->assertEquals($this->well->getPoint()->getSrid(), $point->srid);
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

        $entities = $this->getEntityManager()->getRepository('AppBundle:WellBoundary')
            ->findBy(array(
                'owner' => $user
            ))
        ;

        foreach ($entities as $entity) {
            $this->getEntityManager()->remove($entity);
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->close();
    }
}
