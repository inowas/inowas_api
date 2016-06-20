<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ModelScenario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\BrowserKit\Response;

class ModelController extends Controller
{
    /**
     * @Route("/models/modflow", name="modflow_model_list")
     *
     * @return Response
     */
    public function modelsAction()
    {
        $models = $this->getDoctrine()->getRepository('AppBundle:ModFlowModel')
            ->findAll();

        return $this->render(':inowas/model/modflow:models.html.twig', array(
                'models' => $models
            )
        );
    }

    /**
     * @Route("/models/modflow/{id}")
     *
     * @param $id
     * @return Response*
     */
    public function modelAction($id)
    {
        $model = $this->getDoctrine()->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'id' => $id
            ));

        if (null === $model){
            return $this->redirectToRoute('modflow_model_list');
        }

        return $this->render(':inowas/model/modflow:model.html.twig', array(
                'model' => $model,
                'modelId' => $model->getId()->toString()
            )
        );
    }

    /**
     * @Route("/modflow/scenarios", name="modflow_model_modflow_scenarios_list")
     *
     * @return Response
     */
    public function modelsModflowScenariosAction()
    {
        $scenarios = $this->getDoctrine()->getRepository('AppBundle:ModelScenario')
            ->findAll();

        return $this->render(':inowas/model/modflow:scenarios.html.twig', array(
                'scenarios' => $scenarios
            )
        );
    }

    /**
     * @Route("/modflow/scenario/{id}", name="modflow_model_modflow_scenario")
     *
     * @param $id
     * @return Response
     */
    public function modelModFlowScenarioAction($id)
    {
        /** @var ModelScenario $scenario */
        $scenario = $this->getDoctrine()->getRepository('AppBundle:ModelScenario')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$scenario instanceof ModelScenario){
            return $this->redirectToRoute('modflow_model_modflow_scenarios_list');
        }

        $model = $scenario->getModel();
        
        return $this->render(':inowas/model/modflow:model.html.twig', array(
                'model' => $model,
                'modelId' => $scenario->getId()->toString()
            )
        );
    }
}
