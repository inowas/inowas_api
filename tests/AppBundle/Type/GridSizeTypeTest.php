<?php

namespace Tests\AppBundle\Type;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\Interpolation\GridSize;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GridSizeTypeTest extends WebTestCase
{

    /** @var  EntityManagerInterface */
    protected $em;

    /** @var Connection */
    protected $dbalConnection;

    /** @var  GridSize */
    protected $gridSize;

    /** @var  ModFlowModel $model */
    protected $model;

    public function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager');

        $this->dbalConnection = static::$kernel->getContainer()
            ->get('doctrine.dbal.default_connection');

        $this->gridSize = new GridSize(12, 13);

        /** @var ModFlowModel model */
        $this->model = new ModFlowModel();
        $this->model->setName('Model')
            ->setGridSize($this->gridSize);
    }

    public function testConvertNullToDataBaseValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToDatabaseValue(null, 'grid_size'));
    }

    public function testConvertToDatabase() {
        $this->assertEquals('{"n_x":12,"n_y":13}', $this->dbalConnection->convertToDatabaseValue($this->gridSize, 'grid_size'));
    }

    public function testConvertNullToPhpValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToPHPValue(null, 'grid_size'));
    }

    public function testConvertToPhpValue() {
        $this->assertEquals($this->gridSize, $this->dbalConnection->convertToPHPValue('{"n_x":12,"n_y":13}', 'grid_size'));
    }

    public function testSaveGridSizeWithModel()
    {
        $this->em->persist($this->model);
        $this->em->flush();
        $this->em->clear();
        
        $model = $this->em->getRepository('AppBundle:ModFlowModel')->findOneBy(array('id' => $this->model->getId()->toString()));
        $this->assertEquals($this->gridSize, $model->getGridSize());
        $this->em->remove($model);
        $this->em->flush();
    }
}