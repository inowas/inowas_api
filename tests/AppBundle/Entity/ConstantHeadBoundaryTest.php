<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\ConstantHeadBoundary;
use AppBundle\Model\ConstantHeadBoundaryFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\Point;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;

class ConstantHeadBoundaryTest extends \PHPUnit_Framework_TestCase
{

    /** @var  ConstantHeadBoundary */
    protected $chb;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->chb = ConstantHeadBoundaryFactory::create()
            ->setName('CHB_1')
        ;
    }

    public function testInstantiation(){
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $this->chb->getId());
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $this->chb->getGeologicalLayers());
    }

    public function testSetGetGeometry()
    {
        $lineString = new LineString(array(new Point(1,2)));
        $this->chb->setGeometry($lineString);
        $this->assertEquals($lineString, $this->chb->getGeometry());
    }

    public function testAddGetRemoveGeologicalLayers()
    {
        $layer = GeologicalLayerFactory::create();
        $this->assertCount(0, $this->chb->getGeologicalLayers());
        $this->chb->addGeologicalLayer($layer);
        $this->assertCount(1, $this->chb->getGeologicalLayers());
        $this->chb->addGeologicalLayer($layer);
        $this->assertCount(1, $this->chb->getGeologicalLayers());
        $anotherLayer = GeologicalLayerFactory::create();
        $this->chb->addGeologicalLayer($anotherLayer);
        $this->assertCount(2, $this->chb->getGeologicalLayers());
        $this->chb->removeGeologicalLayer($layer);
        $this->chb->removeGeologicalLayer($anotherLayer);
        $this->assertCount(0, $this->chb->getGeologicalLayers());
    }


    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->chb);
    }
}
