<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\Id\UserId;
use Inowas\ModflowModel\Model\AMQP\ModflowOptimizationResponse;
use Inowas\ModflowModel\Model\Command\UpdateOptimizationCalculationState;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowOptimizationProgressListenerCommand extends ContainerAwareCommand
{

    /** @var  UserId */
    protected $ownerId;

    protected function configure(): void
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:optimization:listener')
            ->setDescription('Listener which receives progress-messages from optimization-calculations.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $callback = function ($msg) {
            echo ' [+] Submitting result metadata from calculation', "\n";
            echo '  Receiving:' . $msg->body . "\n";

            #try {
            $response = ModflowOptimizationResponse::fromJson($msg->body);

            $optimizationFinder = $this->getContainer()->get('inowas.modflowmodel.optimization_finder');
            $modelId = $optimizationFinder->getModelId($response->optimizationId());

            $commandBus = $this->getContainer()->get('prooph_service_bus.modflow_command_bus');
            $commandBus->dispatch(UpdateOptimizationCalculationState::calculatingWithProgressUpdate(
                $modelId,
                ModflowOptimizationResponse::fromJson($msg->body)
            ));
            #} catch (\Exception $exception) {
            #    echo sprintf($exception->getMessage());
            #}

            echo ' [x] Done', "\n";
            /** @noinspection PhpUndefinedMethodInspection */
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $listener = $this->getContainer()->get('inowas.modflowmodel.amqp_modflow_optimization_progress_listener');
        $output->writeln(sprintf('Listening to %s.', $listener->getRoutingKey()));
        $listener->listen($callback);
    }
}
