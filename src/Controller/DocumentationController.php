<?php

namespace App\Controller;

use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Info(
 *      title="BileMo API",
 *      version="1.0.0",
 *      description="This is the documention for Bilemo API.",
 * )
 */
class DocumentationController extends AbstractController
{
    /**
     * Default route used to redirect to documention.
     *
     * @Route("/", name="app_default")
     */
    public function index()
    {
        return $this->redirectToRoute('app_documentation');
    }

    /**
     * @Route("/api/doc", name="app_documentation")
     */
    public function showDocumentation()
    {
        return $this->render('base.html.twig');
    }
}
