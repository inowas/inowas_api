<?php

namespace InowasPyprocessingBundle\Tests\Service;

use InowasPyprocessingBundle\Model\GeoImage\GeoImageConfigurationFileCreator;
use InowasPyprocessingBundle\Model\Interpolation\InterpolationConfigurationFileCreator;
use InowasPyprocessingBundle\Exception\InvalidArgumentException;
use InowasPyprocessingBundle\Model\Modflow\ModflowConfigurationFileCreator;
use InowasPyprocessingBundle\Service\ConfigurationFileCreatorFactory;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class ConfigurationFileCreatorFactoryTest extends WebTestCase
{

    /** @var  KernelInterface */
    protected $httpKernel;

    /** @var  Serializer */
    protected $serializer;

    /** @var  \InowasPyprocessingBundle\Service\ConfigurationFileCreatorFactory */
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
        $this->assertInstanceOf(ConfigurationFileCreatorFactory::class, $this->configurationFileCreatorFactory);
    }

    public function testCreateInterpolationFileCreatorFactory(){
        $interpolationConfigurationFileCreator = $this->configurationFileCreatorFactory->create('interpolation');
        $this->assertInstanceOf(InterpolationConfigurationFileCreator::class, $interpolationConfigurationFileCreator);
    }

    public function testCreateGeoImageFileCreatorFactory(){
        $geoImageConfigurationFileCreator = $this->configurationFileCreatorFactory->create('geoimage');
        $this->assertInstanceOf(GeoImageConfigurationFileCreator::class, $geoImageConfigurationFileCreator);
    }

    public function testCreateModflowFileCreatorFactory(){
        $modflowConfigurationFileCreator = $this->configurationFileCreatorFactory->create('modflow');
        $this->assertInstanceOf(ModflowConfigurationFileCreator::class, $modflowConfigurationFileCreator);
    }

    public function testCreateUnknownFileCreatorFactoryThrowsException(){
        $this->setExpectedException(InvalidArgumentException::class);
        $this->configurationFileCreatorFactory->create('foo');
    }
}
