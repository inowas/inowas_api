<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Area;
use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyTimeValue;
use AppBundle\Entity\PropertyValue;
use AppBundle\Entity\Raster;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\RasterFactory;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;
use Tests\AppBundle\RestControllerTestCase;

class RasterRestControllerTest extends RestControllerTestCase
{

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

        $this->getEntityManager()->persist($this->getOwner());
        $this->getEntityManager()->flush();

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

    public function testGetRasterById()
    {
        $this->getEntityManager()->persist($this->raster);
        $this->getEntityManager()->flush();

        $client = static::createClient();
        $client->request(
            'GET',
            '/api/rasters/'.$this->raster->getId().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $raster = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('id', $raster);
        $this->assertEquals($this->raster->getId(), $raster->id);
        
        $this->assertObjectHasAttribute('grid_size', $raster);
        $gridSize = $raster->grid_size;
        $this->assertObjectHasAttribute('n_x', $gridSize);
        $this->assertEquals($this->raster->getGridSize()->getNX(), $gridSize->n_x);
        $this->assertObjectHasAttribute('n_y', $gridSize);
        $this->assertEquals($this->raster->getGridSize()->getNY(), $gridSize->n_y);


        $this->assertObjectHasAttribute('bounding_box', $raster);
        $boundingBox = $raster->bounding_box;
        $this->assertObjectHasAttribute('x_min', $boundingBox);
        $this->assertEquals($this->raster->getBoundingBox()->getXMin(), $boundingBox->x_min);
        $this->assertObjectHasAttribute('x_max', $boundingBox);
        $this->assertEquals($this->raster->getBoundingBox()->getXMax(), $boundingBox->x_max);
        $this->assertObjectHasAttribute('y_min', $boundingBox);
        $this->assertEquals($this->raster->getBoundingBox()->getYMin(), $boundingBox->y_min);
        $this->assertObjectHasAttribute('y_max', $boundingBox);
        $this->assertEquals($this->raster->getBoundingBox()->getYMax(), $boundingBox->y_max);
        $this->assertObjectHasAttribute('srid', $boundingBox);
        $this->assertEquals($this->raster->getBoundingBox()->getSrid(), $boundingBox->srid);


        $this->assertObjectHasAttribute('no_data_val', $raster);
        $this->assertEquals($this->raster->getNoDataVal(), $raster->no_data_val);

        $this->assertObjectHasAttribute('data', $raster);
        $this->assertEquals($this->raster->getData(), $raster->data);

        $this->assertObjectHasAttribute('description', $raster);
        $this->assertEquals($this->raster->getDescription(), $raster->description);
    }

    public function testGetRasterWithInvalidIdReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/rasters/1234.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetRasterWithUnknownIdReturns404()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/rasters/'.Uuid::uuid4()->toString().'.json',
            array(),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testPostResult()
    {
        $this->getEntityManager()->persist($this->area);
        $this->getEntityManager()->flush();

        $propertyType = PropertyTypeFactory::create(PropertyType::HYDRAULIC_HEAD);

        $date = new \DateTime('now');

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/rasters.json',
            array(
                'id' => $this->area->getId(),
                'propertyName' => 'MyPropertyName',
                'propertyType' => $propertyType->getAbbreviation(),
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
            ),
            array(),
            array('HTTP_X-AUTH-TOKEN' => $this->getOwner()->getApiKey())
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $actualRaster = json_decode($client->getResponse()->getContent());

        $this->assertObjectHasAttribute('id', $actualRaster);
        $this->assertObjectHasAttribute('grid_size', $actualRaster);
        $this->assertObjectHasAttribute('bounding_box', $actualRaster);
        $this->assertObjectHasAttribute('no_data_val', $actualRaster);
        $this->assertObjectHasAttribute('data', $actualRaster);
        $this->assertObjectHasAttribute('description', $actualRaster);

        $expectedRaster = $this->getEntityManager()->getRepository('AppBundle:Raster')
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
        $user = $this->getEntityManager()->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $this->getOwner()->getUsername()
            ));
        $this->getEntityManager()->remove($user);
    }
}
