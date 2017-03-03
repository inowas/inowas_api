<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\Calculation\ResultType;
use Inowas\Modflow\Model\LayerNumber;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Id\UserId;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowCalculationResultsCommand extends ContainerAwareCommand
{

    /** @var  UserId */
    protected $ownerId;

    protected function configure(): void
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:calculation:results')
            ->setDescription('Shows some result stats.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resultsFinder = $this->getContainer()->get('inowas.modflow_projection.calculation_results_finder');

        dump($resultsFinder->findValue(
            ModflowId::fromString('696593ca-1306-4dc5-88e4-34b98afbb148'),
            ResultType::fromString('head'),
            LayerNumber::fromInteger(1),
            TotalTime::fromInt(1005)
        ));

        die();

        dump($resultsFinder->findLayerValues(
            ModflowId::fromString('780ccf4d-9fef-46c1-b1ea-ef4d06b33fd5')
        ));

        die();

        dump($resultsFinder->findTimes(
            ModflowId::fromString('780ccf4d-9fef-46c1-b1ea-ef4d06b33fd5'),
            ResultType::fromString('head'),
            LayerNumber::fromInteger(1)
        ));
    }
}
