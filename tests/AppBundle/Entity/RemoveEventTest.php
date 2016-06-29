<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\RemoveEvent;
use AppBundle\Entity\Well;
use AppBundle\Model\WellFactory;

class RemoveEventTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Well */
    protected $well;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->well = WellFactory::create();
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
