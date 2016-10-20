<?php

namespace Inowas\PyprocessingBundle\Service;

use Inowas\PyprocessingBundle\Model\Modflow\ModflowModelInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\ProcessBuilder;

class MapImage
{
    /** @var  KernelInterface */
    protected $kernel;

    /**
     * GeoImage constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function createImage(ModflowModelInterface $model){

        $dataFolder = $this->kernel->getRootDir().'/../var/data/modflow/'.$model->getId();
        $fs = new Filesystem();
        if (! $fs->exists($dataFolder)){
            $fs->mkdir($dataFolder);
        }

        $phantomJsBuilder = new ProcessBuilder();
        $phantomJsBuilder->setWorkingDirectory($this->kernel->getRootDir().'/../');
        $phantomJsBuilder->setPrefix('./bin/phantomjs');
        $phantomJsBuilder->add('./js/saveMapAsPng.js');
        $phantomJsBuilder->add(sprintf('http://localhost/models/modflow/%s/map', $model->getId()));
        $phantomJsBuilder->add($dataFolder.'/image.png');
        $phantomJS = $phantomJsBuilder->getProcess();
        $phantomJS->run();

        return true;
    }
}
