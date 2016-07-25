<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\ModflowCalculation;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CalculationRestControllerTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var ModflowCalculation */
    protected $calculation;

    /** @var  \DateTime */
    protected $timeNow;

    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager');

        $this->timeNow = new \DateTime();
        $this->calculation = new ModflowCalculation();
        $this->calculation->setModelId(Uuid::uuid4());
        $this->calculation->setExecutable('mf2005');
        $this->calculation->setDateTimeStart(new \DateTime('2016-01-01'));
        $this->calculation->setDateTimeEnd(new \DateTime('2016-01-02'));
        $this->calculation->setOutput('Output');
        $this->calculation->setErrorOutput('Error');
    }

    public function testGetCalculationByIdAPI(){
        $this->entityManager->persist($this->calculation);
        $this->entityManager->flush();

        $client = static::createClient();
        $client->request('GET', '/api/calculations/'.$this->calculation->getId().'.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $calculation = json_decode($client->getResponse()->getContent());

        $this->assertObjectHasAttribute('model_id', $calculation);
        $this->assertEquals($this->calculation->getModelId(), $calculation->model_id);
        $this->assertObjectHasAttribute('executable', $calculation);
        $this->assertEquals($this->calculation->getExecutable(), $calculation->executable);
        $this->assertObjectHasAttribute('state', $calculation);
        $this->assertEquals($this->calculation->getState(), $calculation->state);
        $this->assertObjectHasAttribute('date_time_add_to_queue', $calculation);
        $this->assertEquals($this->calculation->getDateTimeAddToQueue(), $this->timeNow);
        $this->assertObjectHasAttribute('date_time_start', $calculation);
        $this->assertEquals($this->calculation->getDateTimeStart(), new \DateTime('2016-01-01'));
        $this->assertObjectHasAttribute('date_time_end', $calculation);
        $this->assertEquals($this->calculation->getDateTimeEnd(), new \DateTime('2016-01-02'));
        $this->assertObjectHasAttribute('output', $calculation);
        $this->assertEquals($this->calculation->getOutput(), $calculation->output);
        $this->assertObjectHasAttribute('error_output', $calculation);

        $this->assertEquals($this->calculation->getErrorOutput(), $calculation->error_output);
        $calculation = $this->entityManager->getRepository('AppBundle:ModflowCalculation')
            ->findOneBy(array(
                'id' => $this->calculation->getId()->toString()
            ));

        $this->entityManager->remove($calculation);
        $this->entityManager->flush();
        $this->entityManager->close();
    }


    public function testAreaListWithNotValidIdReturns404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/calculations/notValidId.json');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testAreaListWithNotKnownIdReturns404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/calculations/'.Uuid::uuid4()->toString().'.json');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
    }
}
