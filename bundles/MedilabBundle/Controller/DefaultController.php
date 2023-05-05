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

    /**
     * @Route("/login")
     */
    public function loginAction(Request $request)
    {
        //return new Response('Hello world from medilab');
        return $this->render('@Medilab/login.html.twig');
    }

    /**
     * @Route("/register")
     */
    public function registerAction(Request $request)
    {
        //return new Response('Hello world from medilab');
        return $this->render('@Medilab/registration.html.twig');
    }

    /**
     * @Route("/get_register", name="get_register", methods={"POST"})
     */
    public function get_registerAction(Request $request)
    {
        dd($request);
    }
}
