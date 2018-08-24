<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\DataStructures\JsonObject;
use Inowas\ModflowModel\Service\AMQPBasicProducer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowOptimizationCommand extends ContainerAwareCommand
{
    /** @var  AMQPBasicProducer */
    private $producer;

    protected function configure(): void
    {
        $this
            ->setName('inowas:optimization:test')
            ->setDescription('Processes all relevant operations.')
            ->addArgument('filename', InputArgument::REQUIRED, 'JSON-File');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rootDir = $this->getContainer()->get('kernel')->getRootDir();
        $json = file_get_contents($rootDir.'/../'.$input->getArgument('filename'));

        $this->producer = $this->getContainer()->get('inowas.modflowmodel.amqp_modflow_optimization');
        $this->producer->publish(JsonObject::fromJson($json));

        $callback = function ($msg) {
            echo ' [+] Submitting result metadata from calculation', "\n";
            echo '  Receiving:' . $msg->body . "\n";
            echo ' [x] Done', "\n";
            /** @noinspection PhpUndefinedMethodInspection */
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $listener = $this->getContainer()->get('inowas.modflowmodel.amqp_modflow_optimization_progress_listener');
        $output->writeln(sprintf('Listening to %s.', $listener->getRoutingKey()));
        $listener->listen($callback);
    }
}
