<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends Controller
{
    /**
     * @Route("/product", name="getproduct")
     */
    private function indexAction()
    {
        $ifproduct = $this->getDoctrine->getRepository('AppBundle:Product')->find(1);
        return $this->render('default/index.html.twig', ['a' => $ifproduct]);
    }
}
