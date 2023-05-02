<?php

namespace HelloBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends FrontendController
{
    /**
     * @Route("/home")
     */
    public function indexAction(Request $request)
    {
        //return new Response('Hello world from hello');
        return $this->render('@HelloBundle/home.html.twig');
    }
    public function footerAction(Request $request)
    {
        return $this->render('@HelloBundle/includes/footer.html.twig');
    }
}
