<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\Id\UserId;
use Inowas\ModflowModel\Model\AMQP\ModflowCalculationResponse;
use Inowas\ModflowModel\Model\Command\UpdateCalculationState;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowCalculationListenerCommand extends ContainerAwareCommand
{

    /** @var  UserId */
    protected $ownerId;

    protected function configure(): void
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:calculation:listener')
            ->setDescription('Listener which receives messages of finished calculations');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $callback = function ($msg) {
            echo ' [+] Submitting result metadata from calculation', "\n";
            echo '  Receiving:' . $msg->body . "\n";

            try {
                $response = ModflowCalculationResponse::fromJson($msg->body);
                $commandBus = $this->getContainer()->get('prooph_service_bus.modflow_command_bus');
                $commandBus->dispatch(
                    UpdateCalculationState::calculationFinished(
                        $response->modelId(),
                        $response->calculationId(),
                        $response
                    )
                );
            } catch (\Exception $exception) {
                echo sprintf($exception->getMessage());
            }

            echo ' [x] Done', "\n";
            /** @noinspection PhpUndefinedMethodInspection */
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $listener = $this->getContainer()->get('inowas.modflowmodel.amqp_modflow_calculation_results_listener');
        $output->writeln(sprintf('Listening to %s.', $listener->getRoutingKey()));
        $listener->listen($callback);
    }
}
