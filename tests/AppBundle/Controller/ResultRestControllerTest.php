<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Area;
use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyTimeValue;
use AppBundle\Entity\PropertyValue;
use AppBundle\Entity\Raster;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\RasterBandFactory;
use AppBundle\Model\RasterFactory;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResultRestControllerTest extends WebTestCase
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
    protected $rasterEntity;

    /** @var \AppBundle\Model\Raster $raster */
    protected $rasterObject;

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
        $this->rasterObject = RasterFactory::createModel();
        $this->rasterObject
            ->setWidth(10)
            ->setHeight(10)
            ->setUpperLeftX(0.0005)
            ->setUpperLeftY(0.0005)
            ->setScaleX(1)
            ->setScaleY(1)
            ->setSkewX(0)
            ->setSkewY(0)
            ->setSrid(4326);

        $rasterBand = RasterBandFactory::create();
        $rasterBand->setPixelType('\'32BF\'::text');
        $rasterBand->setInitValue(200);
        $rasterBand->setNoDataVal(-9999);
        $rasterBand->setData(array(
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,7,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,1,2,3,4,5,6,7,8,9)
        ));

        $this->rasterObject->setBand($rasterBand);
    }

    public function testPostResult()
    {
        $areaId = $this->area->getId();
        $propertyType = PropertyTypeFactory::setName('Hydraulic Head');
        $propertyType->setAbbreviation("hh");
        $this->entityManager->persist($propertyType);
        $this->entityManager->flush();

        $date = new \DateTime('now');

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/results.json',
            array(
                'id' => $areaId,
                'propertyType' => $propertyType->getName(),
                'width' => $this->rasterObject->getWidth(),
                'height' => $this->rasterObject->getHeight(),
                'upperLeftX' => $this->rasterObject->getUpperLeftX(),
                'upperLeftY' => $this->rasterObject->getUpperLeftY(),
                'scaleX' => $this->rasterObject->getScaleX(),
                'scaleY' => $this->rasterObject->getScaleY(),
                'skewX' => $this->rasterObject->getSkewX(),
                'skewY' => $this->rasterObject->getSkewY(),
                'srid' => $this->rasterObject->getSrid(),
                'bandPixelType' => $this->rasterObject->getBand()->getPixelType(),
                'bandInitValue' => $this->rasterObject->getBand()->getInitValue(),
                'bandNoDataVal' => $this->rasterObject->getBand()->getNoDataVal(),
                'data' => json_encode($this->rasterObject->getBand()->getData()),
                'date' => $date->format('Y-m-d H:i:s')
            )
        );
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $client->getResponse();

        /** @var Raster $raster */
        $raster = $this->serializer->deserialize($client->getResponse()->getContent(), 'AppBundle\Entity\Raster', 'json');
        $this->assertEquals($raster->getRaster(), $this->rasterObject);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
    }
}
