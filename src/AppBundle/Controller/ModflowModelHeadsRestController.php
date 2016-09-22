<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ModelScenario;
use AppBundle\Entity\ModFlowModel;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;

class ModflowModelHeadsRestController extends FOSRestController
{
    /**
     * Get head values
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Post head values.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Calculation-Id is not found"
     *   }
     * )
     *
     * @param $id
     * @return View
     *
     * @QueryParam(name="totim", description="Time in days from beginning")
     */
    public function getModflowmodelHeadsAction($id)
    {
        /** @var ModFlowModel $model */
        $element = $this->findElementById($id);

        $heads = null;
        $model = null;

        if ($element instanceof ModFlowModel){
            $heads = $element->getHeads();
            $model = $element;
        }

        if ($element instanceof ModelScenario){
            $heads = $element->getHeads();
            $model = $element->getBaseModel();
        }

        $response = array();
        if ($model->getStressPeriods()->count() > 0){
            /** @var \DateTime $startDate */
            $startDate = $model->getStressPeriods()->first()->getDateTimeBegin();

            foreach ($heads as $totim => $head){
                $date = clone $startDate;
                $date->modify(sprintf('+%s days', (int)$totim-1));
                $response[$date->format('Y-m-d')] = $head;
            }
        }

        $view = View::create();
        $view->setData($response)
            ->setStatusCode(200)
        ;

        return $view;
    }

    /**
     * Route to submit Result Head-Values
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Post head values.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Calculation-Id is not found"
     *   }
     * )
     *
     * @param $id
     * @param ParamFetcher $paramFetcher
     * @return View
     *
     * @RequestParam(name="totim", description="Time in days from beginning")
     * @RequestParam(name="heads", description="Heads-Array in Json")
     */
    public function postModflowmodelHeadsAction($id, ParamFetcher $paramFetcher)
    {
        $totim = $paramFetcher->get('totim');
        $head = $paramFetcher->get('heads');

        /** @var ModFlowModel $model */
        $model = $this->findElementById($id);
        $heads = $model->getHeads();
        $heads[$totim] = $head;

        $model->setHeads($heads);
        $this->getDoctrine()->getManager()->persist($model);
        $this->getDoctrine()->getManager()->flush();

        $view = View::create();
        $view->setData('OK')
            ->setStatusCode(200)
        ;

        return $view;
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
}
