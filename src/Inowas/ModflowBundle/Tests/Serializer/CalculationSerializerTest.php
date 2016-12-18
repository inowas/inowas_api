<?php

namespace Inowas\ModflowBundle\Tests\Serializer;

use Inowas\ModflowBundle\Model\Calculation;
use Inowas\ModflowBundle\Service\CalculationManager;
use Inowas\ModflowBundle\Service\ModflowToolManager;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CalculationSerializerTest extends KernelTestCase {

    /** @var  CalculationManager */
    protected $cm;

    /** @var  ModflowToolManager */
    protected $mm;

    /** @var  Serializer */
    protected $serializer;

    /** @var  Calculation */
    protected $calculation;

    public function setUp()
    {
        self::bootKernel();
        $this->cm = static::$kernel->getContainer()
            ->get('inowas.modflow.calculationmanager')
        ;

        $this->mm = static::$kernel->getContainer()
            ->get('inowas.modflow.toolmanager')
        ;

        $this->serializer = static::$kernel->getContainer()
            ->get('jms_serializer')
        ;
    }

    public function testSerialize(){

        $model = $this->mm->createModel();
        $calculation = $this->cm->create($model);
        $calculation->setModelUrl('testUrl');
        $calculation->setPort(8080);
        $calculation->setDataFolder('datafolder');
        $calculation->setDateTimeAddToQueue(new \DateTime('2015-01-01'));
        $calculation->setDateTimeStart(new \DateTime('2015-01-02'));
        $calculation->setDateTimeEnd(new \DateTime('2015-01-03'));
        $calculation->setOutput('Output');
        $this->cm->update($calculation);

        $json = $this->serializer
            ->serialize($calculation, 'json',
                SerializationContext::create()
                    ->setGroups(array('details'))
            );

        $this->assertJson($json);
        $response = json_decode($json);

        $this->assertObjectHasAttribute('id', $response);
        $this->assertEquals($calculation->getId()->toString(), $response->id);
        $this->assertObjectHasAttribute('model_id', $response);
        $this->assertEquals($calculation->getModelId(), $response->model_id);
        $this->assertObjectHasAttribute('model_url', $response);
        $this->assertEquals($calculation->getModelUrl(), $response->model_url);
        $this->assertObjectHasAttribute('data_folder', $response);
        $this->assertEquals($calculation->getDataFolder(), $response->data_folder);
        $this->assertObjectHasAttribute('state', $response);
        $this->assertEquals($calculation->getState(), $response->state);
        $this->assertObjectHasAttribute('date_time_add_to_queue', $response);
        $this->assertEquals($calculation->getDateTimeAddToQueue(), new \DateTime($response->date_time_add_to_queue));
        $this->assertObjectHasAttribute('date_time_start', $response);
        $this->assertEquals($calculation->getDateTimeStart(), new \DateTime($response->date_time_start));
        $this->assertObjectHasAttribute('date_time_end', $response);
        $this->assertEquals($calculation->getDateTimeEnd(), new \DateTime($response->date_time_end));
        $this->assertObjectHasAttribute('output', $response);
        $this->assertEquals($calculation->getOutput(), $response->output);
        $this->assertObjectHasAttribute('finished_with_success', $response);
        $this->assertEquals($calculation->isFinishedWithSuccess(), $response->finished_with_success);
        $this->calculation = $calculation;
    }

    public function tearDown()
    {
        $this->cm->remove($this->calculation);
    }
}
