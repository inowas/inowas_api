<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\ModflowId;
use Inowas\Soilmodel\Interpolation\FlopyReadDataRequest;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

ini_set('memory_limit', '2048M');

class ModflowCalculationReadDataCommand extends ContainerAwareCommand
{
    /** @var  \Inowas\Common\Id\UserId */
    protected $ownerId;

    protected function configure(): void
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:calculation:read:layer')
            ->setDescription('Reads data from a calculated model')
            ->addArgument('calculationId', InputArgument::REQUIRED, 'The calculationId to calculate')
            ->addArgument('dataType', InputArgument::REQUIRED, 'The DataType (head or drawdown)')
            ->addArgument('totalTime', InputArgument::REQUIRED, 'The TotalTime')
            ->addArgument('layerNumber', InputArgument::REQUIRED, 'The Zero-Based-Layernumber');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $calculationId = ModflowId::fromString($input->getArgument('calculationId'));

        $dataType = (string)$input->getArgument('dataType');
        $totalTime = TotalTime::fromInt((int)$input->getArgument('totalTime'));
        $layerNumber = LayerNumber::fromInteger((int)$input->getArgument('layerNumber'));

        $flopyReadDataService = $this->getContainer()->get('inowas.soilmodel.flopy_read_data_service');
        $response = $flopyReadDataService->readData(FlopyReadDataRequest::fromLayerdata(
            $calculationId, $dataType, $totalTime, $layerNumber));

        dump($response);
    }
}
