<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\ConfigurationFileCreatorFactory;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class ConfigurationFileCreatorFactoryTest extends WebTestCase
{

    /** @var  KernelInterface */
    protected $httpKernel;

    /** @var  Serializer */
    protected $serializer;

    /** @var  ConfigurationFileCreatorFactory */
    protected $configurationFileCreatorFactory;

    public function setUp()
    {
        self::bootKernel();

        $this->httpKernel = static::$kernel->getContainer()
            ->get('kernel');

        $this->serializer = static::$kernel->getContainer()
            ->get('serializer');

        $this->configurationFileCreatorFactory = new ConfigurationFileCreatorFactory($this->httpKernel, $this->serializer);
    }

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Service\ConfigurationFileCreatorFactory', $this->configurationFileCreatorFactory);
    }

    public function testCreateInterpolationFileCreatorFactory(){
        $interpolationConfigurationFileCreator = $this->configurationFileCreatorFactory->create('interpolation');
        $this->assertInstanceOf('AppBundle\Process\Interpolation\InterpolationConfigurationFileCreator', $interpolationConfigurationFileCreator);
    }

    public function testCreateGeoImageFileCreatorFactory(){
        $geoImageConfigurationFileCreator = $this->configurationFileCreatorFactory->create('geoimage');
        $this->assertInstanceOf('AppBundle\Process\GeoImage\GeoImageConfigurationFileCreator', $geoImageConfigurationFileCreator);
    }

    public function testCreateModflowFileCreatorFactory(){
        $modflowConfigurationFileCreator = $this->configurationFileCreatorFactory->create('modflow');
        $this->assertInstanceOf('AppBundle\Process\Modflow\ModflowConfigurationFileCreator', $modflowConfigurationFileCreator);
    }

    public function testCreateUnknownFileCreatorFactoryThrowsException(){
        $this->setExpectedException('AppBundle\Exception\InvalidArgumentException');
        $this->configurationFileCreatorFactory->create('foo');
    }
}
