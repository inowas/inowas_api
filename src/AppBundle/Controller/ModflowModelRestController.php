<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Area;
use AppBundle\Entity\ModflowModelScenario;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\User;
use AppBundle\Model\ActiveCells;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ModflowModelRestController extends FOSRestController
{
    /**
     * Return all public models and the private models from the user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return all public models and the private models from the user.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @return View
     */
    public function getModflowmodelsAction()
    {
        $user = $this->getUser();

        if (! $user instanceof User) {
            throw $this->createNotFoundException('User not found.');
        }

        $models = $this->getDoctrine()
            ->getRepository('AppBundle:ModFlowModel')
            ->findBy(
                array('public' => true)
            );

        $view = View::create();
        $view->setData($models)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('list'))
            )
        ;

        return $view;
    }

    /**
     * Return the overall project list from a user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the overall Project list from a user",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $username
     * @return View
     */
    public function getUserModflowmodelsAction($username)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $username
            ));

        if (! $user instanceof User) {
            throw $this->createNotFoundException('User with username '.$username.' not found.');
        }

        if (! $user === $this->getUser()){
            throw $this->createAccessDeniedException('You have no access to this resource wit user privileges.');
        }

        $models = $this->getDoctrine()
            ->getRepository('AppBundle:ModFlowModel')
            ->findBy(
                array('owner' => $user),
                array('id' => 'ASC')
            );

        $view = View::create();
        $view->setData($models)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('list'))
            )
        ;

        return $view;
    }

    /**
     * Returns modeldetails by model-id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns modeldetails by model-id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return View
     */
    public function getModflowmodelAction($id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modeldetails');

        $view = View::create();
        $view->setData($model)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext);

        return $view;
    }

    /**
     * Get head values
     *
     * @Route(requirements={"_format"="jpg"})
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
     * @return Response
     */
    public function getModflowmodelImageAction($id){

        /** @var ModFlowModel $model */
        $this->findModelById($id);

        $file = $this->get('kernel')->getRootDir() . '/../var/data/modflow/' . $id . '/image.jpg';

        if (! file_exists($file)){
            $file = $this->get('kernel')->getRootDir() . '/../web/img/emptyModel.jpg';
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
        $response->setContent(file_get_contents($file));
        return $response;
    }

    /**
     * Deletes a ModflowModel by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the project-information by id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return Response
     */
    public function deleteModflowmodelsAction($id)
    {
        $modelOrScenario = $this->findModelOrScenarioById($id);

        if (! $this->getUser() == $modelOrScenario->getOwner()){
            throw new AccessDeniedException('To delete the model you have to be the owner.');
        }

        if ($modelOrScenario instanceof ModFlowModel){
            $manager = $this->get('inowas.modflowmodel.manager');
            $manager->remove($modelOrScenario);
            return new Response('Success');
        }

        $manager = $this->get('inowas.modflowmodelscenario.manager');
        $manager->remove($modelOrScenario);
        return new Response('Success');
    }

    /**
     * Creating a new ModflowModel
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the details of the new Modflowmodel",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @RequestParam(name="name", default="", description="Modelname.")
     * @RequestParam(name="description", default="", description="Modelname.")
     * @RequestParam(name="public", default=true, description="Public.")
     *
     * @return RedirectResponse
     */
    public function postModflowmodelAction(ParamFetcher $paramFetcher){

        $modflowModelManager = $this->get('inowas.modflowmodel.manager');
        $model = $modflowModelManager->create()
            ->setOwner($this->getUser())
            ->setName($paramFetcher->get('name'))
            ->setDescription($paramFetcher->get('description'))
            ->setPublic($paramFetcher->get('public'))
        ;

        $soilModel = $this->get('inowas.soilmodel.manager')->create();
        $model->setSoilModel($soilModel);
        $modflowModelManager->update($model);

        return $this->redirectToRoute('get_modflowmodel', array('id'=>$model->getId()->toString()));
    }

    /**
     * Update ModflowModelData by corresponding model-id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update ModflowModelData by corresponding model-id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @RequestParam(name="active_cells", description="Active cells array.", strict=false)
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
     * @return View
     */
    public function putModflowmodelAction(ParamFetcher $paramFetcher, $id){
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        if ($paramFetcher->get('active_cells')){
            $model->setActiveCells(ActiveCells::fromJSON($paramFetcher->get('active_cells')));
        }

        $modflowModelManager = $this->get('inowas.modflowmodel.manager');
        $modflowModelManager->persist($model);

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelProperties');

        $view = View::create();
        $view->setData($model)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns the boundingbox array from a model
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the boundingbox array from a model.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
     *
     * @QueryParam(name="srid", nullable=true, description="SRID, default 3857", default=3857)
     * @return View
     */
    public function getModflowmodelBoundingboxAction(ParamFetcher $paramFetcher, $id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        if (null === $model->getBoundingBox()) {
            throw $this->createNotFoundException('BoundingBox not found.');
        }

        $srid = $paramFetcher->get('srid');
        $bb = $model->getBoundingBox();

        if ($bb->getSrid() != 0 && $bb->getSrid() != $srid){
            $bb = $this->getDoctrine()->getRepository('AppBundle:ModFlowModel')
                ->transformBoundingBox($model->getBoundingBox(), $srid);
        }

        $result = array(
            array($bb->getYMin(), $bb->getXMin()),
            array($bb->getYMax(), $bb->getXMax())
        );

        $view = View::create();
        $view->setData($result)
            ->setStatusCode(200)
        ;

        return $view;
    }

    /**
     * Returns the gridsize, boundingbox, activecells array from a model
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the boundingbox array from a model.",
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
    public function getModflowmodelPropertiesAction($id){

        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        $view = View::create();
        $view->setData($model)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups('modelProperties'))
            ->setStatusCode(200)
        ;

        return $view;
    }

    /**
     * Returns the grid as geojson
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the grid as geojson from a model.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
     *
     * @QueryParam(name="srid", nullable=true, description="SRID, default 3857", default="3857")
     * @return View
     */
    public function getModflowmodelGridAction(ParamFetcher $paramFetcher, $id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        if (null === $model->getBoundingBox()) {
            throw $this->createNotFoundException('BoundingBox not found.');
        }

        if (null === $model->getGridSize()) {
            throw $this->createNotFoundException('Gridsize not found.');
        }

        if (null === $model->getActiveCells()) {
            throw $this->createNotFoundException('ActiveCells not found.');
        }

        $srid = $paramFetcher->get('srid');
        $bb = $model->getBoundingBox();

        if ($bb->getSrid() != 0 && $bb->getSrid() != $srid){
            $bb = $this->getDoctrine()->getRepository('AppBundle:ModFlowModel')
                ->transformBoundingBox($model->getBoundingBox(), $srid);
        }

        $result = $this->get('inowas.geotools')->getGeoJsonGrid($bb, $model->getGridSize(), $model->getActiveCells());

        $view = View::create();
        $view->setData($result)
            ->setStatusCode(200)
        ;

        return $view;
    }

    /**
     * Returns the html content and polygon-data for the summary view by modflow-model-id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the html content and polygon-data for the summary view by modflow-model-id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param $id
     * @param $contentType
     *
     * @return View
     */
    public function getModflowmodelContentAction($id, $contentType){

        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        if ($contentType == 'soilmodel') {
            if (!$model->hasSoilModel()){
                throw $this->createNotFoundException('Soilmodel not found.');
            }

            $geoJson = $this->getDoctrine()->getRepository('AppBundle:Area')
                ->getAreaPolygonIn4326($model->getArea()->getId());
            $layers = $model->getSoilModel()->getSortedGeologicalLayers();

            $twig = $this->get('twig');
            $html = $twig->render(':inowas/model/modflow:soilmodel.html.twig', array(
                'modelId' => $model->getId()->toString(),
                'layers' => $layers
            ));

            $result['html'] = $html;
            $result['geojson'] = $geoJson;
        } elseif ($contentType == 'calculation') {

            $calculations = $this->getDoctrine()->getRepository('AppBundle:ModflowCalculation')
                ->findBy(
                    array('modelId' => $id),
                    array('dateTimeAddToQueue' => 'DESC'),
                    1
                );

            $calculation = null;
            if (count($calculations) > 0){
                $calculation = $calculations[0];
            }

            $twig = $this->get('templating');
            $html = $twig->renderResponse(':inowas/model/modflow:calculation.html.twig', array(
                'calculation' => $calculation
            ))->getContent();

            $result['html'] = $html;

        } else {
            $result="";
        }


        $view = View::create();
        $view->setData($result)
            ->setStatusCode(200)
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
     * @param $id
     * @return ModFlowModel|ModflowModelScenario
     */
    private function findModelOrScenarioById($id)
    {

        if (!Uuid::isValid($id)){
            throw $this->createNotFoundException(sprintf('Model or ModelScenario with id: %s not found.'), $id);
        }

        $scenario = $this->getDoctrine()
            ->getRepository('AppBundle:ModflowModelScenario')
            ->findOneBy(array(
                'id' => $id
            ));

        if ($scenario instanceof ModflowModelScenario) {
            return $scenario;
        }

        $model = $this->getDoctrine()
            ->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'id' => $id,
            ));

        if (!$model) {
            throw $this->createNotFoundException(sprintf('Model or ModelScenario with id: %s not found.'), $id);
        }

        return $model;
    }
}
