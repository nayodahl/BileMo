<?php

namespace App\Controller;

use App\Repository\ResellerRepository;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ResellerController extends AbstractController
{
    /**
     * Get the detail of a reseller.
     *
     * @Route("/api/resellers/{resellerId}", methods="GET", name="app_reseller")
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
    public function showReseller(int $resellerId, ResellerRepository $resellerRepo): JsonResponse
    {
        $reseller = $resellerRepo->find($resellerId);
        if (null !== $reseller) {
            return $this->json($reseller, 200, [], ['groups' => 'show_resellers']);
        }

        return $this->json(['message' => 'this reseller does not exist'], 404);
    }

    /**
     * Get the list of all resellers.
     *
     * @Route("/api/resellers", methods="GET", name="app_resellers")
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
    public function showResellers(ResellerRepository $resellerRepo): JsonResponse
    {
        $resellers = $resellerRepo->findAll();
        if (null !== $resellers) {
            return $this->json($resellers, 200, [], ['groups' => 'show_resellers']);
        }

        return $this->json(['message' => 'there is no reseller for the moment'], 404);
    }
}
