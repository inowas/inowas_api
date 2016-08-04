<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\BoundaryModelObject;
use AppBundle\Entity\ModelObject;
use AppBundle\Entity\User;
use AppBundle\Model\GeneralHeadBoundaryFactory;
use Ramsey\Uuid\Uuid;
use Tests\AppBundle\RestControllerTestCase;

class ModelObjectRestControllerTest extends RestControllerTestCase
{
    /** @var ModelObject $modelObject */
    protected $modelObject;

    public function setUp()
    {

        $this->modelObject = GeneralHeadBoundaryFactory::create()
            ->setOwner($this->getOwner())
            ->setName('ModelObjectTestBoundaryName')
            ->setPublic(true);

        $this->getEntityManager()->persist($this->getOwner());
        $this->getEntityManager()->persist($this->modelObject);
        $this->getEntityManager()->flush();
    }

    public function testListWithoutAPIKeyReturns401()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/unknown_username/modelobjects.json');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testList()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/users/'.$this->getOwner()->getUsername().'/modelobjects.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(1, json_decode($client->getResponse()->getContent()));
    }

    public function testListWithUnknownUserReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET', '/api/users/unknown_username/modelobjects.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testDetails()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modelobjects/'.$this->modelObject->getId().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $modelObject = json_decode($client->getResponse()->getContent());
        $this->assertEquals($this->modelObject->getId(), $modelObject->id);
    }

    public function testDetailsWithInvalidIdReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/modelobjects/unknown_id.json',
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
            '/api/modelobjects/'.Uuid::uuid4()->toString().'.json',
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
        /** @var User $owner */
        $owner = $this->getEntityManager()->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $this->getOwner()->getUsername()
            ));
        $this->getEntityManager()->remove($owner);

        $boundaries = $this->getEntityManager()->getRepository('AppBundle:ModelObject')
            ->findBy(array(
                'owner' => $owner
            ));

        foreach ($boundaries as $boundary) {
            if ($boundary instanceof BoundaryModelObject){
                $this->getEntityManager()->remove($boundary);
            }
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->close();
    }
}
