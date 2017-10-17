<?php

declare(strict_types=1);

namespace Tests\Inowas\ModflowBundle\Controller;

use FOS\UserBundle\Doctrine\UserManager;
use Inowas\AppBundle\Model\User;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\Inowas\ModflowBundle\EventSourcingBaseTest;

class RasterControllerTest extends EventSourcingBaseTest
{
    /** @var UserManager */
    protected $userManager;

    /** @var User */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->userManager = $this->container->get('fos_user.user_manager');

        $this->commandBus = static::$kernel->getContainer()
            ->get('prooph_service_bus.modflow_command_bus');

        $user = $this->userManager->findUserByUsername('testUser');

        if(! $user instanceof User){
            /** @var User $user */
            $user = $this->userManager->createUser();
            $user->setUsername('testUser');
            $user->setName('testUserName');
            $user->setEmail('testUser@testUser.com');
            $user->setPlainPassword('testUserPassword');
            $user->setEnabled(true);
            $this->userManager->updateUser($user);
        }

        $this->user = $user;
    }

    /**
     * @test
     * @group messaging-integration-tests
     */
    public function it_uploads_a_raster_file(): void
    {
        $userId = UserId::fromString($this->user->getId()->toString());
        $apiKey = $this->user->getApiKey();

        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($userId, $modelId);

        $tempFilePath = sprintf('%s/%s',sys_get_temp_dir(), uniqid('temp_raster_file', true));
        copy(__DIR__.'/testfiles/inowas_logo.png', $tempFilePath);

        $file = new UploadedFile($tempFilePath, 'inowas_logo.png');
        $md5 = md5_file($file->getRealPath());

        $client = static::createClient();
        $client->request(
            'POST',
            '/v2/rasterfile',
            array(),
            array('file' => $file),
            array('HTTP_X-AUTH-TOKEN' => $apiKey)
        );

        $response = $client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertInstanceOf(File::class, $this->container->get('inowas.modflowmodel.raster_files_persister')->load($md5));
        $this->container->get('inowas.modflowmodel.raster_files_persister')->clear();
    }

    /**
     * @test
     * @group messaging-integration-tests
     */
    public function it_uploads_a_geotiff_raster_file_and_redirects_to_get_route(): void
    {
        $userId = UserId::fromString($this->user->getId()->toString());
        $apiKey = $this->user->getApiKey();

        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($userId, $modelId);

        $tempFilePath = sprintf('%s/%s',sys_get_temp_dir(), uniqid('temp_raster_file', true));
        copy(__DIR__.'/testfiles/FAS_Brazil1.2017289.terra.721.2km.tif', $tempFilePath);

        $file = new UploadedFile($tempFilePath, 'FAS_Brazil1.2017289.terra.721.2km.tif');
        $md5 = md5_file($file->getRealPath());

        $client = static::createClient();
        $client->request(
            'POST',
            '/v2/rasterfile',
            array(),
            array('file' => $file),
            array('HTTP_X-AUTH-TOKEN' => $apiKey)
        );

        $this->assertInstanceOf(File::class, $this->container->get('inowas.modflowmodel.raster_files_persister')->load($md5));

        $response = $client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue($response->isRedirect());

        $client->followRedirect();
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertJson($content);
        $arr = json_decode($content, true);
        $this->assertArrayHasKey('metadata', $arr);

        $metadata = $arr['metadata'];
        $this->assertArrayHasKey('driver', $metadata);
        $this->assertArrayHasKey('origin', $metadata);
        $this->assertEquals(-51.784, $metadata['origin'][0]);
        $this->assertEquals(0.2192, $metadata['origin'][1]);
        $this->assertArrayHasKey('pixelSize', $metadata);
        $this->assertEquals(0.0191408, $metadata['pixelSize'][0]);
        $this->assertEquals(-0.017986451612903, $metadata['pixelSize'][1]);
        $this->assertArrayHasKey('projection', $metadata);
        $this->assertArrayHasKey('rasterXSize', $metadata);
        $this->assertEquals(500, $metadata['rasterXSize']);
        $this->assertArrayHasKey('rasterYSize', $metadata);
        $this->assertEquals(775, $metadata['rasterYSize']);
        $this->assertArrayHasKey('rasterCount', $metadata);
        $this->assertEquals(3, $metadata['rasterCount']);

        $this->assertArrayHasKey('data', $arr);
        $data = $arr['data'];
        $this->assertCount(3, $data);
        $this->assertCount(775, $data[0]);
        $this->assertCount(500, $data[0][0]);

        $this->container->get('inowas.modflowmodel.raster_files_persister')->clear();
    }
}
