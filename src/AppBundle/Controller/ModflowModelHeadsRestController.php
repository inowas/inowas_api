<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ModflowModelScenario;
use AppBundle\Entity\ModFlowModel;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use HeatMap\HeatMap;
use Inowas\PyprocessingBundle\Model\Modflow\ModflowModelInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @return JsonResponse
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

        if ($element instanceof ModflowModelScenario){
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
        } else {
            $date = new \DateTime('now');
            $response[$date->format('Y-m-d')] = $heads;
        }

        return new JsonResponse($response);
    }


    /**
     * Get head values
     *
     * @Route(requirements={"_format"="png"})
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
     * @param ParamFetcher $paramFetcher
     * @param $id
     * @return Response
     *
     * @QueryParam(name="totim", requirements="\d+", default=0, description="Time in days from beginning")
     * @QueryParam(name="layer", requirements="\d+", default=0, description="Layer number")
     * @QueryParam(name="max", requirements="\d+", default=null, description="Value of the spectrum maximum")
     * @QueryParam(name="min", requirements="\d+", default=null, description="Value of the spectrum minimum")
     * @QueryParam(name="upper", requirements="\d+", default=95, description="Percentile spectrum max")
     * @QueryParam(name="loper", requirements="\d+", default=5, description="Percentile spectrum min")
     */
    public function getModflowmodelHeadsImageAction(ParamFetcher $paramFetcher, $id){

        /** @var ModflowModelInterface $element $element */
        $element = $this->findElementById($id);
        $heads = $element->getHeads();

        if (! array_key_exists($paramFetcher->get('totim'), $heads)){
            throw new NotFoundHttpException(sprintf('Head values for totim = %s not available.', $paramFetcher->get('totim')));
        }

        $head  = $heads[$paramFetcher->get('totim')];

        if (! array_key_exists($paramFetcher->get('layer'), $head)){
            throw new NotFoundHttpException(sprintf('Head values for totim = %s, layer = %s not available.', $paramFetcher->get('totim'), $paramFetcher->get('layer')));
        }
        $data = $head[$paramFetcher->get('layer')];

        $heatMap = new HeatMap();

        if ($paramFetcher->get('min') && $paramFetcher->get('max')){
            $file = $heatMap->createWithAbsoluteLimits($data, $paramFetcher->get('min'), $paramFetcher->get('max'));
        } else {
            $file = $heatMap->createWithPercentileLimits($data, $paramFetcher->get('loper'), $paramFetcher->get('upper'));
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
        $response->setContent(file_get_contents($file));
        return $response;
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
            ->getRepository('AppBundle:ModflowModelScenario')
            ->findOneBy(array(
                'id' => $id
            ));

        if ($element instanceof ModflowModelScenario) {
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
