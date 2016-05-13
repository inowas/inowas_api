<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Area;
use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyTimeValue;
use AppBundle\Entity\PropertyValue;
use AppBundle\Entity\Raster;
use AppBundle\Model\AreaFactory;
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
            ->setUpperLeftX(0.0005)
            ->setUpperLeftY(0.0007)
            ->setLowerRightX(0.010)
            ->setLowerRightY(0.015)
            ->setNumberOfColumns(10)
            ->setNumberOfRows(11)
            ->setNoDataVal(-999)
            ->setSrid(4326)
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
                'numberOfColumns' => $this->raster->getNumberOfColumns(),
                'numberOfRows' => $this->raster->getNumberOfRows(),
                'upperLeftX' => $this->raster->getUpperLeftX(),
                'upperLeftY' => $this->raster->getUpperLeftY(),
                'lowerRightX' => $this->raster->getLowerRightX(),
                'lowerRightY' => $this->raster->getLowerRightY(),
                'srid' => $this->raster->getSrid(),
                'noDataVal' => $this->raster->getNoDataVal(),
                'data' => json_encode($this->raster->getData()),
                'date' => $date->format('Y-m-d H:i:s')
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $actualRaster = json_decode($client->getResponse()->getContent());

        $this->assertObjectHasAttribute('id', $actualRaster);
        $this->assertObjectHasAttribute('number_of_rows', $actualRaster);
        $this->assertObjectHasAttribute('number_of_columns', $actualRaster);
        $this->assertObjectHasAttribute('upper_left_x', $actualRaster);
        $this->assertObjectHasAttribute('upper_left_y', $actualRaster);
        $this->assertObjectHasAttribute('lower_right_x', $actualRaster);
        $this->assertObjectHasAttribute('lower_right_y', $actualRaster);
        $this->assertObjectHasAttribute('srid', $actualRaster);
        $this->assertObjectHasAttribute('no_data_val', $actualRaster);
        $this->assertObjectHasAttribute('data', $actualRaster);

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
