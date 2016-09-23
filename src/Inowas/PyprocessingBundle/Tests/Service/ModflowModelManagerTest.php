<?php

namespace Inowas\PyprocessingBundle\Tests\Service;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\ActiveCells;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Service\GeoTools;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\ORM\EntityManager;
use Inowas\PyprocessingBundle\Service\ModflowModelManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ModflowModelManagerTest extends KernelTestCase
{

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var GeoTools $geoTools
     */
    protected $geoTools;

    /**
     * @var ModflowModelManager
     */
    protected $modflowModelManager;

    public function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->geoTools = static::$kernel->getContainer()
            ->get('inowas.geotools');

        $this->modflowModelManager = new ModflowModelManager($this->em, $this->geoTools);
    }

    public function testInstantiate(){
           $this->assertInstanceOf(ModflowModelManager::class, $this->modflowModelManager);
    }

    public function testCreateModflowModelReturnsInstanceofModflowModel(){
        $model = $this->modflowModelManager->create();
        $this->assertInstanceOf(ModFlowModel::class, $model);
    }

    public function testPersistModflowModel(){
        $model = $this->modflowModelManager->create();
        $this->modflowModelManager->persist($model);

        $models = $this->em->getRepository('AppBundle:ModFlowModel')
            ->findBy(array(
                'id' => $model->getId()
            ));

        $this->assertCount(1, $models);
    }

    public function testRemoveModflowModel(){
        $model = $this->modflowModelManager->create();
        $this->modflowModelManager->persist($model);

        $models = $this->em->getRepository('AppBundle:ModFlowModel')
            ->findBy(array(
                'id' => $model->getId()
            ));

        $this->assertCount(1, $models);

        $this->modflowModelManager->remove($model);
        $models = $this->em->getRepository('AppBundle:ModFlowModel')
            ->findBy(array(
                'id' => $model->getId()
            ));

        $this->assertCount(0, $models);
    }

    public function testFindModelById(){
        $model = $this->modflowModelManager->create();
        $this->modflowModelManager->persist($model);
        $this->em->clear();

        $persistedModel = $this->modflowModelManager->findById($model->getId());
        $this->assertInstanceOf(ModFlowModel::class, $persistedModel);
        $this->assertEquals($model->getId(), $persistedModel->getId());
    }

    public function testUpdateModflowBoundingBoxAndSetActiveCells(){
        $model = $this->modflowModelManager->create();

        $model->setArea(AreaFactory::create()
            ->setName('Rio Primero Area')
            ->setPublic(true)
            ->setGeometry(new Polygon(array(
                array(
                    array(-63.687336, -31.313615),
                    array(-63.687336, -31.367449),
                    array(-63.569260, -31.367449),
                    array(-63.569260, -31.313615),
                    array(-63.687336, -31.313615)
                )), 4326)));
        $model->setGridSize(new GridSize(20, 10));

        $this->modflowModelManager->update($model, true);

        $this->assertInstanceOf(BoundingBox::class, $model->getBoundingBox());
        $this->assertInstanceOf(ActiveCells::class, $model->getActiveCells());

        $this->modflowModelManager->remove($model);
    }

    public function testUpdateBoundingBoxCalculatesBoundingBoxAndPersistsModel(){
        $model = $this->modflowModelManager->create();

        $model->setArea(AreaFactory::create()
            ->setName('Rio Primero Area')
            ->setPublic(true)
            ->setGeometry(new Polygon(array(
                array(
                    array(-63.687336, -31.313615),
                    array(-63.687336, -31.367449),
                    array(-63.569260, -31.367449),
                    array(-63.569260, -31.313615),
                    array(-63.687336, -31.313615)
                )), 4326)));

        $this->modflowModelManager->persist($model);
        $this->modflowModelManager->updateBoundingBox($model);

        $this->em->clear();
        $model = $this->em->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'id' => $model->getId()
            ));

        $this->assertInstanceOf(BoundingBox::class, $model->getBoundingBox());
    }

}
