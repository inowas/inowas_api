<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Head;
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
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ModflowModelHeadsRestController extends FOSRestController
{

    /**
     * Get head list
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
     */
    public function getModflowmodelHeadsListAction($id)
    {
        /** @var ModflowModelInterface $model */
        $model = $this->findElementById($id);

        $heads = $this->get('inowas.heads')->getList($model);

        $view = View::create();
        $view->setData($heads)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(['list'])
            )
        ;

        return $view;
    }


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
     * @param $paramFetcher
     * @return View
     *
     * @QueryParam(name="totim", requirements="\d+", default=0, strict=false, description="Time in days from beginning")
     * @QueryParam(name="layer", requirements="\d+", default=0, strict=false, description="Layer number")
     */
    public function getModflowmodelHeadsAction($id, ParamFetcher $paramFetcher)
    {
        /** @var ModFlowModel $model */
        $element = $this->findElementById($id);
        $head = $this->get('inowas.heads')->getHead($element, $paramFetcher->get('totim'), $paramFetcher->get('layer'));

        $view = View::create();
        $view->setData($head)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(['details'])
                ->setSerializeNull(true)
            )
        ;

        return $view;
    }

    /**
     * Get head values
     *
     * @Route(requirements={"_format"="png"})
     * @Method({"GET"})
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
     * @QueryParam(name="max", default=null, description="Value of the spectrum maximum")
     * @QueryParam(name="min", default=null, description="Value of the spectrum minimum")
     * @QueryParam(name="upper", requirements="\d+", default=95, description="Percentile spectrum max")
     * @QueryParam(name="loper", requirements="\d+", default=5, description="Percentile spectrum min")
     */
    public function getModflowmodelHeadsImageAction(ParamFetcher $paramFetcher, $id){

        /** @var ModflowModelInterface $element $element */
        $element = $this->findElementById($id);

        /** @var Head $head */
        $head = $this->get('inowas.heads')->getHead($element, $paramFetcher->get('totim'), $paramFetcher->get('layer'));

        if (! $head instanceof Head){
            throw new NotFoundHttpException(
                sprintf('Head values for totim = %s and layer %s not found.', $paramFetcher->get('totim'), $paramFetcher->get('layer')));
        }

        $data = $head->getData();
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
        /** @var ModflowModelInterface $model */
        $model = $this->findElementById($id);


        $totim = $paramFetcher->get('totim');
        $heads = json_decode($paramFetcher->get('heads'));

        foreach ($heads as $key => $data){
            $this->get('inowas.heads')->addHead($model, $totim, $key, $data);
        }

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
