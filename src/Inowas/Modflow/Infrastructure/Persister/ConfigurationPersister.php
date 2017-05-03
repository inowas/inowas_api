<?php

declare(strict_types=1);

namespace Inowas\Modflow\Infrastructure\Persister;

use Inowas\Common\Id\IdInterface;
use Inowas\Modflow\Model\ModflowConfiguration;
use Symfony\Component\Filesystem\Filesystem;

class ConfigurationPersister
{
    /** @var  string */
    private $dataFolder;

    public function  __construct(string $dataFolder)
    {
        $this->dataFolder = $dataFolder;
    }

    public function persistConfiguration(IdInterface $id, ModflowConfiguration $configuration): void
    {
        $fs = new Filesystem();
        if (! $fs->exists($this->dataFolder)){
            $fs->mkdir($this->dataFolder);
        }

        $dataFolder = $this->getDataFolder($id);

        if (! $fs->exists($dataFolder)){
            $fs->mkdir($dataFolder);
        }

        $fileName = sprintf('%s/%s', $dataFolder, 'configuration.json');
        $fs->dumpFile($fileName, json_encode($configuration));
    }

    public function loadConfiguration(IdInterface $id): ModflowConfiguration
    {

        $dataFolder = $this->getDataFolder($id);
        $fileName = sprintf('%s/%s', $dataFolder, 'configuration.json');

        if (! file_exists($fileName)){
            throw new \Exception(sprintf('File %s not found', $fileName));
        }

        return ModflowConfiguration::fromJson(json_decode(file_get_contents(($fileName))));
    }

    private function getDataFolder(IdInterface $calculationId): string
    {
        return $this->dataFolder.'/'.$calculationId->toString();
    }
}
