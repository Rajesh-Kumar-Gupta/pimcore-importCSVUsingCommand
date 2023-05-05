<?php

namespace App\Controller;

use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \Pimcore\Controller\FrontendController;


class CustomRestController extends FrontendController
{
    /**
     * @Route("/api/test")
     */
    public function getAction(Request $request)
    {
        // do some authorization here ...

        $blogs = new DataObject\Category\Listing();

        foreach ($blogs as $key => $blog) {
            $data[] = array(
                "title" => $blog->getcategoryId(),
                "description" => $blog->getcategoryName(),
                );
        }
        return $this->json(["success" => true, "data" => $data]);
    }

    /**
     * @Route("/create-product", name="create_product", methods={"POST"})
     */
    public function postAction(Request $request)
    {
        // do some authorization here ...

        $blogs = new DataObject\Category\Listing();

        foreach ($blogs as $key => $blog) {
            $data[] = array(
                "title" => $blog->getcategoryId(),
                "description" => $blog->getcategoryName(),
                );
        }
        return $this->json(["success" => true, "data" => $data]);
    }
}