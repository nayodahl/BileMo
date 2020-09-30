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
     * @Route("/api/resellers/{resellerId}", methods="GET", name="reseller")
     */
    public function showReseller(int $resellerId, ResellerRepository $resellerRepo)
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);

        $reseller = $resellerRepo->find($resellerId);
        if (null !== $reseller) {
            $data = $serializer->normalize($reseller, 'json', ['groups' => 'show_resellers']);

            return new JsonResponse($data);
        }

        return $this->json(null, 404);
    }

    /**
     * @Route("/api/resellers", methods="GET", name="resellers")
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
