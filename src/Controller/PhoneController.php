<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PhoneController extends AbstractController
{
    /**
     * @Route("/api/phones/{id}", name="phone")
     */
    public function showPhone(int $id, PhoneRepository $phoneRepo)
    {
        $phone = $phoneRepo->find($id);
        if (null !== $phone) {
            return  $this->json($phone, 200);
        }

        return $this->json(null, 404);
    }

    /**
     * @Route("/api/phones", name="phones")
     */
    public function showPhones(PhoneRepository $phoneRepo)
    {
        $phones = $phoneRepo->findAll();

        return $this->json($phones, 200);
    }
}
