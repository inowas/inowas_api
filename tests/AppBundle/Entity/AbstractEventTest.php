<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\AddBoundaryEvent;
use AppBundle\Entity\PropertyType;
use AppBundle\Entity\PropertyValue;
use AppBundle\Entity\Well;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
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
