<?php

namespace App\Controller;

use App\Repository\ResellerRepository;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ResellerController extends AbstractController
{
    /**
     * @Route("/api/resellers/{id}", name="reseller")
     */
    public function showReseller(int $id, ResellerRepository $resellerRepo)
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);

        $reseller = $resellerRepo->find($id);
        if (null !== $reseller) {
            $data = $serializer->normalize($reseller, 'json', ['groups' => 'show_resellers']);

            return new JsonResponse($data);
        }

        return $this->json(null, 404);
    }

    /**
     * @Route("/api/resellers", name="resellers")
     */
    public function showResellers(ResellerRepository $resellerRepo)
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);

        $resellers = $resellerRepo->findAll();
        if (null !== $resellers) {
            $data = $serializer->normalize($resellers, 'json', ['groups' => 'show_resellers']);

            return new JsonResponse($data);
        }

        return $this->json(null, 404);
    }
}
