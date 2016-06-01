<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Area;
use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyTimeValue;
use AppBundle\Entity\PropertyValue;
use AppBundle\Entity\Raster;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\RasterFactory;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RasterRestControllerTest extends WebTestCase
{

    /** @var \Doctrine\ORM\EntityManager */
    protected $entityManager;

    /** @var Serializer */
    protected $serializer;

    /** @var Area $area */
    protected $area;

    /** @var Property $property */
    protected $property;

    /** @var PropertyValue $propertyValue */
    protected $propertyValue;

    /** @var PropertyTimeValue $propertyTimeValue */
    protected $propertyTimeValue;

    /** @var Raster $raster */
    protected $raster;

    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager')
        ;

        $this->serializer = static::$kernel->getContainer()
            ->get('jms_serializer')
        ;

        $this->area = AreaFactory::create();

        $this->raster = RasterFactory::create();
        $this->raster
            ->setBoundingBox(new BoundingBox(0.0005, 0.0007, 0.0010, 0.0015, 4326))
            ->setGridSize(new GridSize(10, 11))
            ->setNoDataVal(-999)
            ->setData(
                array(
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,7,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9),
                    array(0,1,2,3,4,5,6,7,8,9)
            ))
            ->setDescription('Description')
        ;
    }

    public function testPostResult()
    {
        $this->entityManager->persist($this->area);
        $this->entityManager->flush();

        $propertyType = PropertyTypeFactory::setName('Hydraulic Head');
        $propertyType->setAbbreviation("hh");

        $this->entityManager->persist($propertyType);
        $this->entityManager->flush();

        $date = new \DateTime('now');

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/rasters.json',
            array(
                'id' => $this->area->getId(),
                'propertyName' => 'MyPropertyName',
                'propertyType' => $propertyType->getName(),
                'numberOfColumns' => $this->raster->getGridSize()->getNX(),
                'numberOfRows' => $this->raster->getGridSize()->getNY(),
                'upperLeftX' => $this->raster->getBoundingBox()->getXMin(),
                'upperLeftY' => $this->raster->getBoundingBox()->getXMax(),
                'lowerRightX' => $this->raster->getBoundingBox()->getYMin(),
                'lowerRightY' => $this->raster->getBoundingBox()->getYMax(),
                'srid' => $this->raster->getBoundingBox()->getSrid(),
                'noDataVal' => $this->raster->getNoDataVal(),
                'data' => json_encode($this->raster->getData()),
                'description' => $this->raster->getDescription(),
                'date' => $date->format('Y-m-d H:i:s')
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $actualRaster = json_decode($client->getResponse()->getContent());

        $this->assertObjectHasAttribute('id', $actualRaster);
        $this->assertObjectHasAttribute('grid_size', $actualRaster);
        $this->assertObjectHasAttribute('bounding_box', $actualRaster);
        $this->assertObjectHasAttribute('no_data_val', $actualRaster);
        $this->assertObjectHasAttribute('data', $actualRaster);
        $this->assertObjectHasAttribute('description', $actualRaster);

        $expectedRaster = $this->entityManager->getRepository('AppBundle:Raster')
            ->findOneBy(array(
                'id' => $actualRaster->id
            ));

        $this->assertEquals($expectedRaster->getId(), $actualRaster->id);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
    }
}
