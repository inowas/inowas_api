<?php

namespace AppBundle\DataFixtures\ORM\TestScenarios\PropertyTypes;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\PropertyType;
use AppBundle\Entity\Raster;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\RasterFactory;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Tests\Model;

class LoadHeads implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $entityManager)
    {
        
        $hh = $entityManager->getRepository('AppBundle:PropertyType')
            ->findOneBy(array(
                'abbreviation' => 'hh'
            ));

        if (null === $hh) {
            throw new NotFoundHttpException('PropertyType not found');
        }

        echo "Loading heads from file\r\n";
        $filename = __DIR__."/head_layer_3.json";
        $headsJSON = file_get_contents($filename, FILE_USE_INCLUDE_PATH);
        $heads = json_decode($headsJSON, true);

        var_dump($heads);
        var_dump(count($heads[1]));

        $heads = json_decode($headsJSON);

        for ($iy = 0; $iy < count($heads); $iy++){
            for ($ix = 0; $ix < count($heads[0]); $ix++){
                if ($heads[$iy][$ix] < -2000){
                    $heads[$iy][$ix] = Raster::DEFAULT_NO_DATA_VAL;
                }
            }
        }

        die();
        
        $raster = RasterFactory::create();
        $raster->setData($heads);
        $raster->setGridSize(new GridSize(count($heads[0]), count($heads)));
        $raster->setBoundingBox(
            new BoundingBox(
                11775189.21765423379838467,
                11789747.53923093341290951,
                2385794.83458124194294214,
                2403506.49811625294387341,
                3857)
        );

        $model = $em->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'name' => "Hanoi"
            ));

        if (null === $model) {
            throw new NotFoundHttpException('Model not found');
        }


        /** @var ModFlowModel $model */
        foreach ($model->getSoilModel()->getGeologicalLayers() as $layer){
            if ($layer->getOrder() == 3){
                $layer->addValue($hh, PropertyValueFactory::create()->setRaster($raster));
                $em->persist($layer);
                break;
            }
        }


        $em->persist($raster);
        $em->flush();

        $activeCells = $this->container->get('inowas.geotools')
            ->calculateActiveCells($model->getArea(), $raster->getBoundingBox(), $raster->getGridSize());
        $geoImageService = $this->container->get('inowas.geoimage');
        $geoImageService->createImageFromRaster($raster, $activeCells);
    }
}