<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BoundaryModelObject;
use AppBundle\Entity\ModflowModelScenario;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\User;
use AppBundle\Exception\InvalidArgumentException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;

class ModflowModelSoilmodelRestController extends FOSRestController
{
    /**
     * Returns the area by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the area by ModflowModel-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return View
     */
    public function getModflowmodelSoilmodelAction($id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);
        $model = $this->setMutable($model, $this->getUser());

        if (! $model->hasSoilModel()){
            throw new InvalidArgumentException(sprintf('Model with id=%s has no Soilmodel.', $id));
        }

        $soilModel = $model->getSoilModel();

        $view = View::create();
        $view->setData($soilModel)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('soilmodeldetails')))
            ->setTemplate('InowasPyprocessingBundle:inowas/modflow:soilmodel.html.twig')
            ->setTemplateData(array(
                'modelId' => $model->getId()->toString(),
                'layers' => $soilModel->getSortedGeologicalLayers()
            ))
        ;

        return $view;
    }

    /**
     * @param $id
     * @return \AppBundle\Entity\AbstractModel
     */
    private function findModelById($id)
    {

        if (!Uuid::isValid($id)){
            throw $this->createNotFoundException('Model with id='.$id.' not found.');
        }

        $scenario = $this->getDoctrine()
            ->getRepository('AppBundle:ModflowModelScenario')
            ->findOneBy(array(
                'id' => $id
            ));

        if ($scenario instanceof ModflowModelScenario) {
            return $scenario->getModel();
        }

        $model = $this->getDoctrine()
            ->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'id' => $id,
            ));

        if (!$model) {
            throw $this->createNotFoundException('Model not found.');
        }

        return $model;
    }

    /**
     * @param ModFlowModel $model
     * @param User $user
     * @return ModFlowModel
     */
    private function setMutable(ModFlowModel $model, User $user){

        if ($model->getOwner() == $user){
            $model->getArea()->setMutable(true);

            /** @var BoundaryModelObject $boundary */
            foreach ($model->getBoundaries() as $boundary) {
                $boundary->setMutable(true);
            }

            /** @var BoundaryModelObject $boundary */
            foreach ($model->getObservationPoints() as $observationPoint) {
                $observationPoint->setMutable(true);
            }
        }

        return $model;
    }
}
