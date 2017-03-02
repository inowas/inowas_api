<?php

namespace Inowas\ModflowBundle\Service;

use Inowas\Common\FileName;
use Inowas\Common\LayerNumber;
use Inowas\Modflow\Model\HeadData;
use Inowas\Modflow\Model\ResultType;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\TotalTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class FilePersister
{
    /** @var  string */
    private $baseDataFolder;

    public function  __construct(KernelInterface $kernel)
    {
        $this->baseDataFolder= $kernel->getContainer()->getParameter('inowas.modflow.data_folder');
    }

    public function persist(ModflowId $calculationId, ResultType $type, TotalTime $totalTime, LayerNumber $layerNumber, HeadData $headData, $fileType = 'json')
    {

        $filename = sprintf('%s_%s_%s.%s',
            $type->toString(),
            $totalTime->toInteger(),
            $layerNumber->toInteger(),
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

        $fs->dumpFile($calculationResultDataFolder.'/'.$filename, json_encode($headData->toArray()));
        return FileName::fromString($filename);
    }

    public function read(ModflowId $calculationId, FileName $filename): HeadData
    {
        $filename = $filename->toString();

        if (! file_exists($this->getCalculationResultDataFolder($calculationId).'/'.$filename)){
            throw new \Exception(sprintf('File %s not found', $this->getCalculationResultDataFolder($calculationId).'/'.$filename));
        }

        $data = HeadData::from2dArray(
            json_decode(file_get_contents($this->getCalculationResultDataFolder($calculationId).'/'.$filename))
        );

        return $data;
    }

    private function getCalculationResultDataFolder(ModflowId $calculationId): string
    {
        return $this->baseDataFolder.'/'.$calculationId->toString();
    }
}
