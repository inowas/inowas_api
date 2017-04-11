<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Modflow\Model\Command\UpdateCalculationResults;
use Inowas\Soilmodel\Interpolation\FlopyCalculationResponse;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowCalculationListenerCommand extends ContainerAwareCommand
{

    /** @var  \Inowas\Common\Id\UserId */
    protected $ownerId;

    protected function configure(): void
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:calculation:listener')
            ->setDescription('Listener which receives messages of finished calculations')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $callback = function($msg) {
            $response = FlopyCalculationResponse::fromJson($msg->body);
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            $commandBus = $this->getContainer()->get('prooph_service_bus.modflow_command_bus');
            $commandBus->dispatch(UpdateCalculationResults::withResponse($response->calculationId(), $response));
        };

        $listener = $this->getContainer()->get('inowas.soilmodel.flopy_calculation_listener_service');
        $listener->listen($callback);
    }
}
