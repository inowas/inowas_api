<?php

namespace Inowas\PyprocessingBundle\Tests\Service;

use Inowas\PyprocessingBundle\Model\GeoImage\GeoImageConfigurationFileCreator;
use Inowas\PyprocessingBundle\Model\Interpolation\InterpolationConfigurationFileCreator;
use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;
use Inowas\PyprocessingBundle\Model\Modflow\ModflowConfigurationFileCreator;
use Inowas\PyprocessingBundle\Service\ConfigurationFileCreatorFactory;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class ConfigurationFileCreatorFactoryTest extends WebTestCase
{

    /** @var  KernelInterface */
    protected $httpKernel;

    /** @var  Serializer */
    protected $serializer;

    /** @var  \Inowas\PyprocessingBundle\Service\ConfigurationFileCreatorFactory */
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
