<?php

namespace AppBundle\Command;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\PropertyType;
use AppBundle\Service\Interpolation;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InterpolationCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:modflowmodel:layerinterpolation')
            ->setDescription('Interpolates all Layers automatically.')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'The ModflowModel-Id is needed'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $geoTools = $this->getContainer()->get('inowas.geotools');
        $soilModelService = $this->getContainer()->get('inowas.soilmodel');

        $id = $input->getArgument('id');
        $soilModelService->loadModflowModelById($id);

        $modFlowModel = $soilModelService->getModflowModel();
        $activeCells = $geoTools->calculateActiveCells($modFlowModel->getArea(), $modFlowModel->getBoundingBox(), $modFlowModel->getGridSize());
        $modFlowModel->setActiveCells($activeCells);
        $entityManager->persist($modFlowModel);
        $entityManager->flush();

        $layers = $modFlowModel->getSoilModel()->getGeologicalLayers();

        /** @var GeologicalLayer $layer */
        foreach ($layers as $layer)
        {
            $propertyTypes = $soilModelService->getAllPropertyTypesFromLayer($layer);
            /** @var PropertyType $propertyType */
            foreach ($propertyTypes as $propertyType){
                if ($propertyType->getAbbreviation() == PropertyType::TOP_ELEVATION && $layer->getOrder() != GeologicalLayer::TOP_LAYER) {
                    continue;
                }

                echo (sprintf("Interpolating Layer %s, Property %s\r\n", $layer->getName(), $propertyType->getName()));
                $output = $soilModelService->interpolateLayerByProperty(
                    $layer,
                    $propertyType->getAbbreviation(),
                    array(Interpolation::TYPE_IDW, Interpolation::TYPE_MEAN),
                    $activeCells
                    );

                echo ($output);
            }
        }
    }
}