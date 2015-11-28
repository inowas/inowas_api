<?php

namespace AppBundle\Controller;

use AppBundle\Model\Point;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        $geologicalLayers = $this->getDoctrine()
            ->getRepository('AppBundle:GeologicalLayer')
            ->findAllByProjectId($id);

        dump($geologicalLayers);
        die();

        $project = $this->getDoctrine()
            ->getRepository('AppBundle:Project')
            ->findOneBy(array(
                'id' => $id
            ));

        /** @var SerializerInterface $serializer */
        $serializer = $this->get('jms_serializer');

        $serialisationContext = SerializationContext::create()
            ->enableMaxDepthChecks()
            ->setGroups('details')
        ;

        $point = new Point(1,2,3);
        $user = $this->getDoctrine()->getRepository('AppBundle:User')
            ->findOneBy(array(
                'id' => 1))
        ;

        dump($serializer);

        $content = "<html><body>";
        //$content .= $serializer->serialize($user, 'json', $serialisationContext)."\n";
        $content .= $serializer->serialize($point, 'json', $serialisationContext)."\n";
        $content .= "</body></html>";

        $response = new Response();
        $response->setContent($content);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type','text/html');
        return $response;
    }
}
