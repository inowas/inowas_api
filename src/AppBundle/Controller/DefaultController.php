<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/", name="home")
     * @Method({"GET"})
     */
    public function indexAction()
    {
        // replace this example code with whatever you need
        return $this->redirectToRoute('modflow_model_list');
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/old", name="old_home")
     * @Method({"GET"})
     */
    public function oldHomeIndexAction()
    {
        // replace this example code with whatever you need
        return $this->render('inowas/index.html.twig');
    }
}
