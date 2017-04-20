<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Modflow\Model\Command\CalculateModflowModelCalculation;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowCalculationCommand extends ContainerAwareCommand
{

    /** @var  \Inowas\Common\Id\UserId */
    protected $ownerId;

    protected function configure(): void
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:calculation:calculate')
            ->setDescription('Calculates a model by calculationId')
            ->addArgument('calculationId', InputArgument::OPTIONAL, 'The calculationId to calculate')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if (! $input->getArgument('calculationId')){
            $calculations = $this->getContainer()->get('inowas.modflow_projection.calculation_configuration_finder')->findAll();
            foreach ($calculations as $calculation){
                $output->writeln($calculation['calculation_id']);
            }

            return;
        }

        $calculationId = ModflowId::fromString($input->getArgument('calculationId'));
        $calculationFinder = $this->getContainer()->get('inowas.modflow_projection.calculation_configuration_finder');
        $calculation = $calculationFinder->getFlopyCalculation($calculationId);

        if ($calculation) {
            $flopyCalculation = $this->getContainer()->get('inowas.soilmodel.flopy_calculation_service');
            $flopyCalculation->calculate($calculation);
        }
    }
}
