<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ModflowCalculation;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CalculationRestController extends FOSRestController
{
    /**
     * Return a calculation by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a calculation by id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the calculation is not found"
     *   }
     * )
     *
     * @param $id
     * @return View
     * @throws NotFoundHttpException
     */
    public function getCalculationAction($id)
    {

        if (! Uuid::isValid($id)){
            throw $this->createNotFoundException('Calculation with id='.$id.' not found.');
        }

        $calculation = $this->getDoctrine()
            ->getRepository('AppBundle:ModflowCalculation')
            ->findOneBy(array(
                'id' => $id
            ));

        if (! $calculation instanceof ModflowCalculation) {
            throw $this->createNotFoundException('Calculation with id='.$id.' not found.');
        }

        $view = View::create();
        $view->setData($calculation);

        return $view;
    }
}
