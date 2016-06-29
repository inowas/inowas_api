<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AddBoundaryEvent;
use AppBundle\Entity\Well;
use AppBundle\Model\WellFactory;

class AbstractEventTest extends \PHPUnit_Framework_TestCase
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

    public function testCreateId()
    {
        $event = new AddBoundaryEvent($this->well);
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $event->getId());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {}
}
