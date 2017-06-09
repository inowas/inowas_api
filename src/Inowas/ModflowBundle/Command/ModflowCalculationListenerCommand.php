<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\ModflowCalculation\Model\Command\UpdateCalculationResults;
use Inowas\ModflowCalculation\Model\ModflowCalculationResponse;
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
            echo ' [+] Submitting result metadata from calculation', "\n";
            echo '  Receiving:'. $msg->body ."\n";
            $response = ModflowCalculationResponse::fromJson($msg->body);
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            $commandBus = $this->getContainer()->get('prooph_service_bus.modflow_command_bus');
            $commandBus->dispatch(UpdateCalculationResults::withResponse($response->calculationId(), $response));
        };

        $listener = $this->getContainer()->get('inowas.modflowcalculation.amqp_flopy_calculation_listener');
        $listener->listen($callback);
    }
}
