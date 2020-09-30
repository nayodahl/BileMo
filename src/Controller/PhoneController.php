<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PhoneController extends AbstractController
{
    /**
     * @Route("/api/phones/{phoneId}", methods="GET", name="phone")
     */
    public function showPhone(int $phoneId, PhoneRepository $phoneRepo)
    {
        $phone = $phoneRepo->find($phoneId);
        if (null !== $phone) {
            return  $this->json($phone, 200);
        }

        return $this->json(['message' => 'this phone does not exist'], 404);
    }

    /**
     * @Route("/api/phones", methods="GET", name="phones")
     */
    public function showPhones(PhoneRepository $phoneRepo)
    {
        $phones = $phoneRepo->findAll();
        if (null !== $phones) {
            return  $this->json($phones, 200);
        }

        return $this->json(['message' => 'there is no phone for the moment'], 404);
    }
}
