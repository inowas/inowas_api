<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\SoilModel;
use AppBundle\Entity\User;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\SoilModelFactory;
use Tests\AppBundle\RestControllerTestCase;

class SoilModelRestControllerTest extends RestControllerTestCase
{
    /** @var SoilModel $soilModel */
    protected $soilModel;

    public function setUp()
    {

        $this->getEntityManager()->persist($this->getOwner());

        $this->soilModel = SoilModelFactory::create();
        $this->soilModel->setOwner($this->getOwner());
        $this->soilModel->setPublic(true);
        $this->soilModel->setName('SoilModelTest');
        $this->getEntityManager()->persist($this->soilModel);
        $this->getEntityManager()->flush();
    }

    public function testGetListWithoutAPIKeyReturns401()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/unknownUser/soilmodels.json');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * Test for the API-Call /api/users/<username>/soilmodels.json
     */
    public function testGetListOfSoilModelsByUserAPI()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/users/'.$this->getOwner()->getUsername().'/soilmodels.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $soilModels = json_decode($client->getResponse()->getContent());
        $this->assertEquals(1, count($soilModels));
    }

    public function testGetListOfSoilModelsWithUnknownReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/users/unknownUser/soilmodels.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetSoilModelDetailsAPI()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/soilmodels/'.$this->soilModel->getId().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetSoilModelGeologicalLayersAPI()
    {
        $this->soilModel->addGeologicalLayer(
            GeologicalLayerFactory::create()
            ->setOwner($this->getOwner())
            ->setOrder(GeologicalLayer::TOP_LAYER)
        );

        $this->getEntityManager()->persist($this->soilModel);
        $this->getEntityManager()->flush();

        $client = static::createClient();
        $client->request(
            'GET',
            '/api/soilmodels/'.$this->soilModel->getId().'/geologicallayers.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
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

        $soilModel = $this->getEntityManager()->getRepository('AppBundle:SoilModel')
            ->findOneBy(array(
               'name' => $this->soilModel->getName()
            ));
        $this->getEntityManager()->remove($soilModel);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->close();
    }
}
