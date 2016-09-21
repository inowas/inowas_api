<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ModelScenario;
use AppBundle\Entity\ModflowCalculation;
use AppBundle\Entity\ModFlowModel;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationPropertiesFactory;
use Inowas\PyprocessingBundle\Service\Flopy;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

class ModflowModelCalculationRestController extends FOSRestController
{
    /**
     * Returns state of calculation of the model by model-id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns state of calculation of the model by calculation-id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Calculation-Id is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return View
     */
    public function getModflowmodelCalculationsAction($id){

        $model = $this->findElementById($id);
        $calculations = $this->getDoctrine()->getRepository('AppBundle:ModflowCalculation')
            ->findBy(
                array('modelId' => $model->getId()->toString()),
                array('dateTimeAddToQueue' => 'DESC'),
                1
            );

        if (count($calculations)>0){
            $calculations = $calculations[0];
        }

        $view = View::create();
        $view->setData($calculations)
            ->setStatusCode(200)
        ;

        return $view;
    }

    /**
     * Sends the command to calculate the model by model-id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Sends the command to calculate the model by model-id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Calculation-Id is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return Response
     */
    public function postModflowmodelCalculationAction($id)
    {

        if ($this->isScenario($id))
        {
            $scenario = $this->getDoctrine()->getRepository('AppBundle:ModelScenario')
                ->findOneBy(array(
                    'id' => $id
                ));

            /** @var ModFlowModel $model */
            $model = $scenario->getBaseModel();

            $fpc = FlopyCalculationPropertiesFactory::loadFromApiRunAndSubmit($model);
            $model->setCalculationProperties($fpc);
            $this->getDoctrine()->getManager()->persist($model);
            $this->getDoctrine()->getManager()->flush();

            /** @var Flopy $flopy */
            $flopy = $this->get('inowas.flopy');

            $flopy->addToQueue(
                $this->getParameter('inowas.api_base_url'),
                $this->getParameter('inowas.modflow.data_folder'),
                $scenario->getId()->toString(),
                $this->getUser()->getId()->toString()
            );

            $flopy->startAsyncFlopyProcessRunner(
                $this->get('kernel')->getRootDir()
            );

            return $this->redirect(
                $this->generateUrl('get_modflowmodel_calculations', array('id' => $scenario->getId()->toString())).'.json'
            );
        }

        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        $fpc = FlopyCalculationPropertiesFactory::loadFromApiRunAndSubmit($model);
        $model->setCalculationProperties($fpc);
        $this->getDoctrine()->getManager()->persist($model);
        $this->getDoctrine()->getManager()->flush();

        /** @var Flopy $flopy */
        $flopy = $this->get('inowas.flopy');

        $flopy->addToQueue(
            $this->getParameter('inowas.api_base_url'),
            $this->getParameter('inowas.modflow.data_folder'),
            $model->getId()->toString(),
            $this->getUser()->getId()->toString()
        );

        $flopy->startAsyncFlopyProcessRunner(
            $this->get('kernel')->getRootDir()
        );

        return $this->redirect(
            $this->generateUrl('get_modflowmodel_calculations', array('id' => $model->getId()->toString())).'.json'
        );

    }

    /**
     * @param $id
     * @return \AppBundle\Entity\AbstractModel
     */
    private function findElementById($id)
    {

        if (!Uuid::isValid($id)){
            throw $this->createNotFoundException(sprintf('Element with id: %s not found.', $id));
        }

        $element = $this->getDoctrine()
            ->getRepository('AppBundle:ModelScenario')
            ->findOneBy(array(
                'id' => $id
            ));

        if ($element instanceof ModelScenario) {
            return $element;
        }

        $element = $this->getDoctrine()
            ->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'id' => $id,
            ));

        if (! $element instanceof ModFlowModel) {
            throw $this->createNotFoundException(sprintf('Element with id: %s not found', $id));
        }

        return $element;
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
            ->getRepository('AppBundle:ModelScenario')
            ->findOneBy(array(
                'id' => $id
            ));

        if ($scenario instanceof ModelScenario) {
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

    private function isScenario($id){
        return $this->getDoctrine()->getRepository('AppBundle:ModelScenario')
            ->findOneBy(array(
                'id' => $id
            ));
    }
}
