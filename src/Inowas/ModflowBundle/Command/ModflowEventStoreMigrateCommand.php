<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\Id\UserId;
use Inowas\ModflowBundle\DataFixtures\Scenarios\Hanoi\Hanoi;
use Inowas\ModflowBundle\DataFixtures\Scenarios\RioPrimero\RioPrimero;
use Inowas\ModflowBundle\DataFixtures\Scenarios\RioPrimero\RioPrimeroArea;
use Inowas\ModflowBundle\DataFixtures\Scenarios\RioPrimero\RioPrimeroBaseModel;
use Inowas\ModflowBundle\DataFixtures\Scenarios\RioPrimero\RioPrimeroBaseModelAndFutureWells;
use Inowas\ModflowBundle\DataFixtures\Scenarios\RioPrimero\RioPrimeroBaseModelSanDiego;
use Inowas\ModflowBundle\DataFixtures\Scenarios\RioPrimero\RioPrimeroSanDiego;
use Inowas\ModflowBundle\DataFixtures\Scenarios\SanFelipe\SanFelipe;
use Inowas\ModflowBundle\DataFixtures\Scenarios\Tools\Tools;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowEventStoreMigrateCommand extends ContainerAwareCommand
{

    /** @var  UserId */
    protected $ownerId;

    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure(): void
    {
        $this
            ->setName('inowas:es:migrate')
            ->setDescription('Migrates the Hanoi-Model to the Database')
            ->addArgument('model', InputArgument::OPTIONAL, 'The model which to load')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \LogicException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $modelname =  $input->getArgument('model');

        if (null === $modelname){
            $output->writeln('Possible Arguments are:');
            $output->writeln('1 for default tools');
            $output->writeln('2 for Hanoi Modflow Model with Scenario Analysis');
            $output->writeln('3 for Rio Primero scenario analysis');
            $output->writeln('4 for Rio Primero base model for summer school');
            $output->writeln('5 for Rio Primero area only');
            $output->writeln('6 for Rio Primero Scenario Analysis');
            $output->writeln('7 for San Felipe Basemodel');
            $output->writeln('8 for Rio Primero Basemodel for WorkShop in San Diego');
            $output->writeln('9 for Rio Primero Scenario Analysis');
        }

        if ((int)$modelname === 1) {
            $tools = new Tools();
            $tools->setContainer($this->getContainer());
            $tools->load();
        }

        if ($modelname === 'Hanoi Basemodel with Scenarios' || (int)$modelname === 2) {
            $hanoi = new Hanoi();
            $hanoi->setContainer($this->getContainer());
            $hanoi->load();
        }

        if ($modelname === 'Rio Primero' || (int)$modelname === 3) {
            $rioPrimero = new RioPrimero();
            $rioPrimero->setContainer($this->getContainer());
            $rioPrimero->load();
        }

        if ($modelname === 'Rio Primero base model for summer school' || (int)$modelname === 4) {
            $rioPrimero = new RioPrimeroBaseModel();
            $rioPrimero->setContainer($this->getContainer());
            $rioPrimero->load();
        }

        if ($modelname === 'Rio Primero Area' || (int)$modelname === 5) {
            $rioPrimero = new RioPrimeroArea();
            $rioPrimero->setContainer($this->getContainer());
            $rioPrimero->load();
        }

        if ($modelname === 'Rio Primero Scenario Analysis' || (int)$modelname === 6) {
            $rioPrimero = new RioPrimeroBaseModelAndFutureWells();
            $rioPrimero->setContainer($this->getContainer());
            $rioPrimero->load();
        }

        if ($modelname === 'San Felipe' || (int)$modelname === 7) {
            $rioPrimero = new SanFelipe();
            $rioPrimero->setContainer($this->getContainer());
            $rioPrimero->load();
        }

        if ((int)$modelname === 8) {
            $rioPrimero = new RioPrimeroBaseModelSanDiego();
            $rioPrimero->setContainer($this->getContainer());
            $rioPrimero->load();
        }

        if ((int)$modelname === 9) {
            $rioPrimero = new RioPrimeroSanDiego();
            $rioPrimero->setContainer($this->getContainer());
            $rioPrimero->load();
        }
    }
}
