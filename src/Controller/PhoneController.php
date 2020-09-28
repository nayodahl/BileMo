<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PhoneController extends AbstractController
{
    /**
     * @Route("/api/phones/{id}", name="phone")
     */
    public function showPhone()
    {
        return $this->json([
            'message' => 'Detail of a phone',
            'path' => 'src/Controller/PhoneController.php',
        ]);
    }

    /**
     * @Route("/api/phones", name="phones")
     */
    public function showPhones()
    {
        return $this->json([
            'message' => 'List of all phones with some infos',
            'path' => 'src/Controller/PhoneController.php',
        ]);
    }
}
