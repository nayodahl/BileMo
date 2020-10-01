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
use OpenApi\Annotations as OA;

class ResellerController extends AbstractController
{
    /**
     * Get the detail of a reseller
     * 
     * @OA\Get(
     *      path="/api/resellers/{resellerId}", 
     *      tags={"reseller"},
     *      summary="Find reseller by ID",
     *      description="Returns a single reseller",
     *      @OA\Parameter(
     *          name="resellerId",
     *          in="path",
     *          description="ID of reseller to return",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int"
     *               )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Reseller"),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Reseller not found"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid ID supplier"
     *     )
     * )
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

        return $this->json(['message' => 'this reseller does not exist'], 404);
    }

    /**
     * Get the list of all resellers
     * 
     * @Route("/api/resellers", methods="GET", name="resellers")
     * @OA\Get(
     *      path="/api/resellers", 
     *      tags={"reseller"},
     *      summary="Find all resellers",
     *      description="Returns a list of all resellers",
     *      @OA\Response(
     *          response="200",
     *          description="successful operation",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="there is no reseller for the moment"
     *      ),
     * )
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

        return $this->json(['message' => 'there is no reseller for the moment'], 404);
    }
}
