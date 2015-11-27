<?php

namespace AppBundle\Controller;

use CrEOF\Spatial\PHP\Types\Geography\Point;
use JMS\Serializer\SerializationContext;
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

        /** @var SerializerInterface $serializer */
        $serializer = $this->get('jms_serializer');
        echo $serializer->serialize(new Point(1,2,3), 'json', SerializationContext::create()->enableMaxDepthChecks());
        die();
    }
}
