<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\RemoveEvent;
use AppBundle\Entity\WellBoundary;
use AppBundle\Model\WellBoundaryFactory;

class RemoveEventTest extends \PHPUnit_Framework_TestCase
{
    /** @var  WellBoundary */
    protected $well;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->well = WellBoundaryFactory::create();
    }

    public function testInstantiateChangeLayerValueEvent()
    {
        $event = new RemoveEvent($this->well);
        $this->assertInstanceOf('AppBundle\Entity\RemoveEvent', $event);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {}
}
