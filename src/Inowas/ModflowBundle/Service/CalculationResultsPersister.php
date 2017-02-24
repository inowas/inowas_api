<?php

namespace Inowas\ModflowBundle\Service;

use Inowas\Common\FileName;
use Inowas\Modflow\Model\CalculationResultWithData;
use Inowas\Modflow\Model\CalculationResultData;
use Inowas\Modflow\Model\CalculationResultType;
use Inowas\Modflow\Model\LayerNumber;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\TotalTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class CalculationResultsPersister
{
    /** @var  string */
    private $baseDataFolder;

    public function  __construct(KernelInterface $kernel)
    {
        $this->baseDataFolder= $kernel->getContainer()->getParameter('inowas.modflow.data_folder');
    }

    public function persist(ModflowId $calculationId, CalculationResultWithData $calculationResult, $fileType = 'json')
    {

        $filename = sprintf('%s_%s_%s.%s',
            $calculationResult->type()->toString(),
            $calculationResult->totalTime()->toInteger(),
            $calculationResult->layerNumber()->toInteger(),
            $fileType
        );

        $fs = new Filesystem();
        if (! $fs->exists($this->baseDataFolder)){
            $fs->mkdir($this->baseDataFolder);
        }

        $calculationResultDataFolder = $this->getCalculationResultDataFolder($calculationId);

        if (! $fs->exists($calculationResultDataFolder)){
            $fs->mkdir($calculationResultDataFolder);
        }

        $fs->dumpFile($calculationResultDataFolder.'/'.$filename, json_encode($calculationResult->data()->toArray()));

        return FileName::fromString($filename);
    }

    public function read(ModflowId $calculationId, FileName $filename): CalculationResultWithData
    {
        $filename = $filename->toString();

        $name = explode('.', $filename)[0];
        $type = CalculationResultType::fromString(explode('_', $name)[1]);
        $totim = TotalTime::fromInt((int)explode('_', $name)[2]);
        $layerNumber = LayerNumber::fromInteger((int)explode('_', $name)[3]);

        if (! file_exists($this->getCalculationResultDataFolder($calculationId).'/'.$filename)){
            throw new \Exception('File not found');
        }

        $data = CalculationResultData::from2dArray(json_decode($this->getCalculationResultDataFolder($calculationId).'/'.$filename));
        return CalculationResultWithData::fromParameters(
            $type, $totim, $layerNumber, $data
        );
    }

    private function getCalculationResultDataFolder(ModflowId $calculationId): string
    {
        return $this->baseDataFolder.'/'.$calculationId->toString();
    }
}
