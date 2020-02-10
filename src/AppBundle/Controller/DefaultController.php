<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Product;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $ifproduct = $this->container->get('doctrine')->getRepository('AppBundle:Product')->findAll();
        return $this->render('default/index.html.twig', ['a' => print_r($ifproduct)]);
    }
}
