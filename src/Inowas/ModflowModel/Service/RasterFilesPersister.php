<?php

namespace Inowas\ModflowModel\Service;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;

class RasterFilesPersister
{
    /** @var  string */
    private $rasterDataFolder;

    public function __construct(string $dataFolder)
    {
        $this->rasterDataFolder = $dataFolder.'/raster';

        $fs = new Filesystem();

        try {
            $fs->mkdir($this->rasterDataFolder);
        } catch (IOException $e) {
            echo 'An error occurred while creating your directory at ' .$e->getPath();
        }
    }

    public function load(string $hash): File
    {
        $finder = new Finder();
        $finder->files()->in($this->rasterDataFolder)->name($hash);

        if ($finder->count() === 0){
            throw new FileNotFoundException(sprintf('File with name %s in directory %s not found.', $hash, $this->rasterDataFolder));
        }

        $file = null;
        foreach ($finder as $f) {
            $file = $f;
        }

        return new File($file->getRealPath(), $hash);
    }

    public function save(File $file): File
    {
        $filename = md5_file($file->getRealPath());
        return $file->move($this->rasterDataFolder, $filename);
    }

    public function clear(): void
    {
        $fs = new FileSystem();
        if (file_exists($this->rasterDataFolder)) {
            $fs->remove($this->rasterDataFolder);
        }
    }
}
