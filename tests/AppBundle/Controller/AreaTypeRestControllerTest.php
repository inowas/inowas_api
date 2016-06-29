<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\AreaType;
use AppBundle\Model\AreaTypeFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AreaTypeRestControllerTest extends WebTestCase
{

    /** @var  EntityManagerInterface */
    protected $em;

    /** @var  AreaType */
    protected $areaType;

    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;
        
        $this->areaType = AreaTypeFactory::setName('ModelAreaType');
    }

    /**
     * Test for the API-Call /api/areatypes.json
     * which is providing a list available of areaTypes
     */
    public function testPropertyCallDetailsWithoutDates()
    {
        $client = static::createClient();
        $client->request('GET', '/api/areatypes.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $areaTypesBefore = json_decode($client->getResponse()->getContent());

        $this->em->persist($this->areaType);
        $this->em->flush();

        $client->request('GET', '/api/areatypes.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $areaTypesAfter = json_decode($client->getResponse()->getContent());

        $this->assertTrue(count($areaTypesBefore) == count($areaTypesAfter)-1);

        $areaType = $this->em->getRepository('AppBundle:AreaType')
            ->findOneBy(array(
                'id' => $this->areaType->getId()->toString()
            ));

        if ($areaType){
            $this->em->remove($areaType);
            $this->em->flush();
        }
    }
    
    public function tearDown()
    {
    }
}
