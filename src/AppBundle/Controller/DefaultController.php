<?php

namespace AppBundle\Controller;

use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        return $this->render('inwas/index.html.twig');
    }

    /**
     * @Route("/home", name="homepage")
     */
    public function indexHomeAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }

    /**
     * @Route("/test/{id}", name="test")
     */
    public function indexTestAction($id)
    {

        $project = $this->getDoctrine()
            ->getRepository('AppBundle:Project')
            ->findOneBy(array(
                'id' => $id
            ));

        dump($project);
        die();

        /** @var SerializerInterface $serializer */
        $serializer = $this->get('fos_rest.serializer');

        echo $serializer->serialize($projects, 'json');
        die();

    }
}
