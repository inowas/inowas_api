<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AddBoundaryEvent;
use AppBundle\Entity\WellBoundary;
use AppBundle\Model\WellBoundaryFactory;

class AbstractEventTest extends \PHPUnit_Framework_TestCase
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
