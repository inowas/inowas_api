<?php

namespace Inowas\PyprocessingBundle\Service;

use AppBundle\Entity\Area;
use AppBundle\Entity\ModelObject;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Service\GeoTools;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;
use Ramsey\Uuid\Uuid;

class ModflowModelManager
{
    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    /** @var  GeoTools $geoTools */
    private $geoTools;

    /**
     * ModflowModelManager constructor.
     * @param EntityManager $entityManager
     * @param GeoTools $geoTools
     */
    public function __construct(EntityManager $entityManager, GeoTools $geoTools){
        $this->entityManager = $entityManager;
        $this->geoTools = $geoTools;
    }

    /**
     * @return ModFlowModel
     */
    public function create(){
        return ModFlowModelFactory::create();
    }

    /**
     * @param $id
     * @return ModFlowModel|null
     */
    public function findById($id){
        if (! Uuid::isValid($id)){
            throw new InvalidArgumentException('The given id is not a valid Uuid.');
        }

        return $this->entityManager->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'id' => $id
            ));
    }

    /**
     * @param ModFlowModel $model
     * @param bool $calculate
     * @return ModFlowModel
     */
    public function update(ModFlowModel $model, $calculate = false)
    {
        if ($calculate){
            $this->updateBoundingBox($model);
            $this->updateActiveCells($model);
            return $model;
        }

        $this->persist($model);
        $model->postLoad();
        return $model;
    }

    /**
     * @param ModFlowModel $model
     */
    public function persist(ModFlowModel $model){
        $this->entityManager->persist($model);
        $this->entityManager->flush();
        $model->postLoad();
    }

    /**
     * @param ModFlowModel $model
     */
    public function remove(ModFlowModel $model){
        $this->entityManager->remove($model);
        $this->entityManager->flush();
    }

    /**
     * @param ModFlowModel $model
     * @return ModFlowModel
     */
    public function updateBoundingBox(ModFlowModel $model){

        $area = $model->getArea();
        if (! $area instanceof Area){
            throw new InvalidArgumentException('Model is expected to have an Area set.');

        }

        if (! $area->getGeometry() instanceof Polygon){
            throw new InvalidArgumentException(sprintf('The ModelArea is expected to have a Polygon as Geometry set. %s given.', gettype($area->getGeometry())));
        }

        $model->setBoundingBox(
            $this->geoTools->getBoundingBoxFromPolygon(
                $area->getGeometry()
            )
        );

        $this->entityManager->persist($model);
        $this->entityManager->flush();
        $model->postLoad();
        return $model;
    }

    /**
     * @param ModFlowModel $model
     * @param ModelObject|null $entity
     * @return ModFlowModel
     */
    public function updateActiveCells(ModFlowModel $model, ModelObject $entity = null){

        $model->preFlush();
        $modelObjects = $model->getModelObjects();

        if ($modelObjects instanceof Collection) {
            foreach ($modelObjects as $key => $mo){
                if (is_null($entity) || $mo->getId() == $entity->getId()){
                    $this->geoTools->setActiveCells($mo, $model->getBoundingBox(), $model->getGridSize());
                }
            }
        }

        $this->entityManager->persist($model);
        $this->entityManager->flush();
        $model->postLoad();

        return $model;
    }
}
