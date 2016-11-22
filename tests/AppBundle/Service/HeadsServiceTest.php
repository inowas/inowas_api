<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Head;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Service\HeadsService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HeadsServiceTest extends KernelTestCase
{

    /** @var  HeadsService $headsService */
    private $headsService;

    /** @var  EntityManager $em */
    private $em;

    /** @var  ModFlowModel */
    private $model;

    /** @var Head $head */
    private $head;


    public function setUp()
    {
        self::bootKernel();

        $this->headsService = static::$kernel->getContainer()
            ->get('inowas.heads');

        $this->em = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager');

        $this->model = ModFlowModelFactory::create();

        $this->head = new Head();
        $this->head->setModelId($this->model->getId());
        $this->head->setLayer(1);
        $this->head->setTotim(2);
        $this->head->setData(array(
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,9],
            [-1,2,3,4,5,6,7,8,10],
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,7]
        ));
    }

    public function testAddHead(){

        $data = $this->head->getData();
        $addedHead = $this->headsService->addHead($this->model, 0, 1, $data);
        $this->assertInstanceOf(Head::class, $addedHead);
        $this->assertEquals($this->model->getId(), $addedHead->getModelId());
        $this->assertEquals(0, $addedHead->getTotim());
        $this->assertEquals(1, $addedHead->getLayer());
        $this->assertEquals(10, $addedHead->getMax());
        $this->assertEquals(-1, $addedHead->getMin());
        $this->assertEquals($data, $addedHead->getData());
        $this->assertCount(1, $this->em->getRepository('AppBundle:Head')
            ->findBy(array(
                'id' => $addedHead->getId()
            ))
        );
    }

    public function testAddHeadOfKnowsModelTotimLayerReplacesValues(){

        $data = $this->head->getData();
        $this->headsService->addHead($this->model, 0, 1, $data);

        $data[0][0] = 100;
        $data[0][1] = -100;
        $replacedHead = $this->headsService->addHead($this->model, 0, 1, $data);

        $head = $this->em->getRepository('AppBundle:Head')
            ->findOneBy(array(
                'id' => $replacedHead->getId()
            ));

        $this->assertInstanceOf(Head::class, $head);
        $this->assertEquals($this->model->getId(), $head->getModelId());
        $this->assertEquals(0, $head->getTotim());
        $this->assertEquals(1, $head->getLayer());
        $this->assertEquals(100, $head->getMax());
        $this->assertEquals(-100, $head->getMin());
        $this->assertEquals($data, $head->getData());
        $this->assertCount(1, $this->em->getRepository('AppBundle:Head')
            ->findBy(array(
                'id' => $head->getId()
            ))
        );
    }

    public function testDeleteAllHeadsByModelId(){
        $this->headsService->addHead($this->model, 0, 0, $this->head->getData());
        $this->headsService->addHead($this->model, 1, 1, $this->head->getData());
        $this->headsService->addHead($this->model, 2, 2, $this->head->getData());
        $this->headsService->addHead($this->model, 3, 3, $this->head->getData());

        $this->assertCount(4, $this->em->getRepository('AppBundle:Head')
            ->findBy(array(
                'modelId' => $this->model->getId()
            ))
        );

        $this->headsService->removeHeadsFromModel($this->model);

        $this->assertCount(0, $this->em->getRepository('AppBundle:Head')
            ->findBy(array(
                'modelId' => $this->model->getId()
            ))
        );
    }

    public function testDeleteAllHeadsByModelIdAndTotim(){
        $this->headsService->addHead($this->model, 0, 0, $this->head->getData());
        $this->headsService->addHead($this->model, 1, 1, $this->head->getData());
        $this->headsService->addHead($this->model, 2, 2, $this->head->getData());
        $this->headsService->addHead($this->model, 3, 3, $this->head->getData());

        $this->assertCount(4, $this->em->getRepository('AppBundle:Head')
            ->findBy(array(
                'modelId' => $this->model->getId()
            ))
        );

        $this->headsService->removeHeadsFromModel($this->model, 3);

        $this->assertCount(3, $this->em->getRepository('AppBundle:Head')
            ->findBy(array(
                'modelId' => $this->model->getId()
            ))
        );
    }

    public function testGetHeadsListByModel(){
        $this->headsService->addHead($this->model, 0, 0, $this->head->getData());
        $this->headsService->addHead($this->model, 1, 1, $this->head->getData());
        $this->headsService->addHead($this->model, 2, 2, $this->head->getData());
        $this->headsService->addHead($this->model, 3, 3, $this->head->getData());

        $list = $this->headsService->getList($this->model);
        $this->assertTrue(is_array($list));
        $this->assertCount(4, $list);
    }



    public function testGetHead(){
        $head = new Head();
        $head->setModelId($this->model->getId());
        $head->setTotim(0);
        $head->setLayer(0);
        $head->setData(array(
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,9]
        ));

        $this->em->persist($head);
        $this->em->flush();

        $head = $this->headsService->getHead($this->model, 0, 0);
        $this->assertInstanceOf(Head::class, $head);
    }

    public function testGetHeadById(){
        $head = new Head();
        $head->setModelId($this->model->getId());
        $head->setTotim(0);
        $head->setLayer(0);
        $head->setData(array(
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,9]
        ));

        $this->em->persist($head);
        $this->em->flush();

        $head = $this->headsService->getHeadById($head->getId());
        $this->assertInstanceOf(Head::class, $head);
    }

    public function tearDown()
    {
        $heads = $this->em->getRepository('AppBundle:Head')
            ->findAll();

        foreach ($heads as $head){
            $this->em->remove($head);
        }

        $models = $this->em->getRepository('AppBundle:ModFlowModel')
            ->findAll();

        foreach ($models as $model){
            $this->em->remove($model);
        }
    }
}
