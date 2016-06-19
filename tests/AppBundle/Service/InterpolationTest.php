<?php

namespace AppBundle\Tests\Service;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\PointValue;
use AppBundle\Service\Interpolation;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InterpolationTest extends WebTestCase
{
    /** @var  Interpolation $interpolation */
    protected $interpolation;

    public function setUp()
    {
        self::bootKernel();

        $this->interpolation = static::$kernel->getContainer()
            ->get('inowas.interpolation')
        ;
    }

    public function testDefaultTmpFolderIsSet(){
        $this->assertNotEmpty($this->interpolation->getTmpFolder());
        $this->assertStringStartsWith('/', $this->interpolation->getTmpFolder());
    }

    public function testTempFolderCanBeChanged(){
        $newTempFolder = '/test/test';
        $this->interpolation->setTmpFolder($newTempFolder);
        $this->assertEquals($newTempFolder, $this->interpolation->getTmpFolder());
    }

    public function testDefaultFileNameIsSetAndLongerThan10Chars(){
        $this->assertNotEmpty($this->interpolation->getTmpFileName());
        $this->assertGreaterThan(10, strlen($this->interpolation->getTmpFileName()));
    }

    public function testDefaultFilenameCanBeChanged(){
        $newTempFileName = 'testFile.log';
        $this->interpolation->setTmpFileName($newTempFileName);
        $this->assertEquals($newTempFileName, $this->interpolation->getTmpFileName());
    }

    public function testSetAndGetGridSize()
    {
        $gridSize = new GridSize(10, 11);
        $this->interpolation->setGridSize($gridSize);
        $this->assertEquals($gridSize, $this->interpolation->getGridSize());
    }

    public function testSetAndGetBoundingBox()
    {
        $boundingBox = new BoundingBox(-1.2, 2.1, -5.1, 1.5);
        $this->interpolation->setBoundingBox($boundingBox);
        $this->assertEquals($boundingBox, $this->interpolation->getBoundingBox());
    }

    public function testAddingPointValue(){
        $pointValue = new PointValue(1,2,3);
        $this->interpolation->addPoint($pointValue);
        $this->assertCount(1, $this->interpolation->getPoints());
        $this->assertEquals($pointValue, $this->interpolation->getPoints()->first());
    }

    public function testAddingOnePointValueWillNotBeAddedTwice()
    {
        $pointValue = new PointValue(1,2,3);
        $this->interpolation->addPoint($pointValue);
        $this->interpolation->addPoint($pointValue);
        $this->assertCount(1, $this->interpolation->getPoints());
        $this->assertEquals($pointValue, $this->interpolation->getPoints()->first());
    }

    public function testRemovePointValue()
    {
        $pointValue = new PointValue(1,2,3);
        $this->interpolation->addPoint($pointValue);
        $this->assertCount(1, $this->interpolation->getPoints());
        $this->assertEquals($pointValue, $this->interpolation->getPoints()->first());
        $this->interpolation->removePoint($pointValue);
        $this->assertCount(0, $this->interpolation->getPoints());
    }

    public function testThrowExceptionIfAlgorithmIsUnknown()
    {
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->setBoundingBox(new BoundingBox(-10.1, 10.2, -5.1, 5.2));
        $this->interpolation->addPoint(new PointValue(1,2,3));

        $unknownAlgorithm = 'foo';
        $this->setExpectedException(
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
            'Algorithm '.$unknownAlgorithm.' not found.'
            );

        $this->interpolation->interpolate($unknownAlgorithm);
    }

    public function testThrowExceptionIfIfGridSizeIsNotSet()
    {
        $this->interpolation->setBoundingBox(new BoundingBox(-10.1, 10.2, -5.1, 5.2));
        $this->interpolation->addPoint(new PointValue(1,2,3));

        $this->setExpectedException(
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
            'GridSize not set.'
        );
        $this->interpolation->interpolate(Interpolation::TYPE_MEAN);
    }

    public function testThrowExceptionIfIfBoundingBoxIsNotSet()
    {
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->addPoint(new PointValue(1,2,3));

        $this->setExpectedException(
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
            'BoundingBox not set.'
        );
        $this->interpolation->interpolate(Interpolation::TYPE_MEAN);
    }

    public function testThrowExceptionIfIfNoPointIstSet()
    {
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->setBoundingBox(new BoundingBox(-10.1, 10.2, -5.1, 5.2));

        $this->setExpectedException(
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
            'No PointValues set.'
        );
        $this->interpolation->interpolate(Interpolation::TYPE_MEAN);
    }

    public function testInterpolationCreatesFolderAndFilesIfNotExistent(){
        $folderName = "/tmp/newFolder".rand(100000,9999999);
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->setBoundingBox(new BoundingBox(-10.1, 10.2, -5.1, 5.2));
        $this->interpolation->addPoint(new PointValue(1,2,3));
        $this->interpolation->setTmpFolder($folderName);
        $this->assertFalse(is_dir($folderName));
        $this->interpolation->interpolate(Interpolation::TYPE_MEAN);
        $this->assertTrue(is_dir($folderName));
        $this->assertFileExists($folderName."/".$this->interpolation->getTmpFileName().".in");
        $this->assertFileExists($folderName."/".$this->interpolation->getTmpFileName().".out");
        unlink($folderName."/".$this->interpolation->getTmpFileName().".in");
        unlink($folderName."/".$this->interpolation->getTmpFileName().".out");
        rmdir($folderName);
        $this->assertFalse(is_dir($folderName));
    }

    public function testIdwInterpolation(){
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->setBoundingBox(new BoundingBox(-10.1, 10.2, -5.1, 5.2));
        $this->interpolation->addPoint(new PointValue(1,2,3));
        $this->interpolation->addPoint(new PointValue(1,3,4));
        $this->interpolation->interpolate(Interpolation::TYPE_IDW);
        $this->assertCount($this->interpolation->getGridSize()->getNY(), $this->interpolation->getData());
        $this->assertCount($this->interpolation->getGridSize()->getNX(), $this->interpolation->getData()[0]);
    }
    
    public function testMeanInterpolation(){
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->setBoundingBox(new BoundingBox(-10.1, 10.2, -5.1, 5.2));
        $this->interpolation->addPoint(new PointValue(1,2,3));
        $this->interpolation->addPoint(new PointValue(1,3,4));
        $this->interpolation->interpolate(Interpolation::TYPE_MEAN);
        $this->assertCount($this->interpolation->getGridSize()->getNY(), $this->interpolation->getData());
        $this->assertCount($this->interpolation->getGridSize()->getNX(), $this->interpolation->getData()[0]);
        $this->assertEquals(3.5, $this->interpolation->getData()[5][5]);
    }

    public function testGaussianInterpolation(){
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->setBoundingBox(new BoundingBox(0, 10, 0, 10));
        $this->interpolation->addPoint(new PointValue(1, 5, 3));
        $this->interpolation->addPoint(new PointValue(2, 8, 3));
        $this->interpolation->addPoint(new PointValue(7, 2, 3));
        $this->interpolation->addPoint(new PointValue(6, 4, 3));
        $this->interpolation->addPoint(new PointValue(8, 2, 3));
        $this->interpolation->addPoint(new PointValue(9, 9, 3));
        $this->interpolation->interpolate(Interpolation::TYPE_GAUSSIAN);
        $this->assertCount($this->interpolation->getGridSize()->getNY(), $this->interpolation->getData());
        $this->assertCount($this->interpolation->getGridSize()->getNX(), $this->interpolation->getData()[0]);
    }

    public function testMultipleInterpolationAlgorithms(){
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->setBoundingBox(new BoundingBox(0, 10, 0, 10));
        $this->interpolation->addPoint(new PointValue(1, 5, 3));
        $this->interpolation->addPoint(new PointValue(2, 8, 3));
        $this->interpolation->addPoint(new PointValue(7, 2, 3));
        $this->interpolation->addPoint(new PointValue(6, 4, 3));
        $this->interpolation->addPoint(new PointValue(8, 2, 3));
        $this->interpolation->addPoint(new PointValue(9, 9, 3));
        $this->interpolation->interpolate(array(0 => Interpolation::TYPE_GAUSSIAN, 1 => Interpolation::TYPE_MEAN));
        $this->assertCount($this->interpolation->getGridSize()->getNY(), $this->interpolation->getData());
        $this->assertCount($this->interpolation->getGridSize()->getNX(), $this->interpolation->getData()[0]);
        $this->assertEquals($this->interpolation->getMethod(), Interpolation::TYPE_GAUSSIAN);
    }

    public function testInterpolationAlgorithmsFallback(){
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->setBoundingBox(new BoundingBox(0, 10, 0, 10));
        $this->interpolation->addPoint(new PointValue(1, 5, 3));
        $this->interpolation->addPoint(new PointValue(2, 8, 3));
        $this->interpolation->interpolate(array(0 => Interpolation::TYPE_GAUSSIAN, 1 => Interpolation::TYPE_MEAN));
        $this->assertCount($this->interpolation->getGridSize()->getNY(), $this->interpolation->getData());
        $this->assertCount($this->interpolation->getGridSize()->getNX(), $this->interpolation->getData()[0]);
        $this->assertEquals($this->interpolation->getMethod(), Interpolation::TYPE_MEAN);
    }
}