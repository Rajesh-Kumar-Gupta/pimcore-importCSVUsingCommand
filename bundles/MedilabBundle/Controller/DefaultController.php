<?php

namespace MedilabBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends FrontendController
{
    /**
     * @Route("/medilab")
     */
    public function indexAction(Request $request)
    {
        //return new Response('Hello world from medilab');
        return $this->render('@Medilab/home.html.twig');
    }
    public function footerAction(Request $request)
    {
        return $this->render('@Medilab/includes/footer.html.twig');
    }
}
