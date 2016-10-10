<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class CacheClearCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('inowas:cache:clear')
            ->setDescription('Clear all caches.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $geoImgDataFolder = $this->getContainer()->getParameter('inowas.geoimage.data_folder');
        $tmpFolder = $this->getContainer()->getParameter('inowas.temp_folder');
        $fs = new Filesystem();

        $output->writeln(sprintf('Clear Temp-Folder %s', $tmpFolder));
        if ($fs->exists($tmpFolder)) {
            $fs->remove($tmpFolder);
            $fs->mkdir($tmpFolder);
        }

        $output->writeln(sprintf('Clear Image-Folder %s', $geoImgDataFolder));
        if ($fs->exists($geoImgDataFolder)) {
            $fs->remove($geoImgDataFolder);
            $fs->mkdir($geoImgDataFolder);
        }
    }
}
