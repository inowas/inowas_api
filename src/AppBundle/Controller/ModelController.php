<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ModelScenario;
use AppBundle\Entity\ModFlowModel;
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
        /** @var ModFlowModel $models */
        $models = $this->getDoctrine()->getRepository('AppBundle:ModFlowModel')
            ->findAll();

        /** @var ModFlowModel $model */
        foreach ($models as $model) {
            $scenarios = $this->getDoctrine()->getRepository('AppBundle:ModelScenario')
                ->findBy(array(
                    'baseModel' => $model->getId()->toString()
                ));

            foreach ($scenarios as $scenario){
                $model->registerScenario($scenario);
            }
        }

        return $this->render(':inowas/model/modflow:models.html.twig', array(
                'models' => $models
            )
        );
    }

    /**
     * @Route("/models/modflow/{id}", name="modflow_model")
     *
     * @param $id
     * @return Response
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
     * @Route("/models/modflow/{id}/scenarios", name="modflow_model_modflow_scenarios_list")
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
     * @Route("/models/modflow/{modelId}/scenarios/{scenarioId}", name="modflow_model_modflow_scenario")
     *
     * @param $scenarioId
     * @return Response
     */
    public function modelModFlowScenarioAction($scenarioId)
    {
        /** @var ModelScenario $scenario */
        $scenario = $this->getDoctrine()->getRepository('AppBundle:ModelScenario')
            ->findOneBy(array(
                'id' => $scenarioId
            ));

        if (!$scenario instanceof ModelScenario){
            return $this->redirectToRoute('modflow_model_list');
        }
        
        return $this->render(':inowas/model/modflow:szenario.html.twig', array(
                'scenario' => $scenario,
            )
        );
    }
}
