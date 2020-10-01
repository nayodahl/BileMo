<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *   title="BileMo API", 
 *   version="1.0.0"
 * )
 */

class PhoneController extends AbstractController
{   
    
    /**
     * Get the detail of a phone
     * 
     * @Route("/api/phones/{phoneId}", methods="GET", name="phone")
     * @OA\Get(
     *      path="/api/phones/{phoneId}", 
     *      tags={"phone"},
     *      summary="Find phone by ID",
     *      description="Returns a single phone",
     *      @OA\Parameter(
     *          name="phoneId",
     *          in="path",
     *          description="ID of phone to return",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int"
     *               )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Phone"),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Phone not found"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid ID supplier"
     *     )
     * )
     */
    public function showPhone(int $phoneId, PhoneRepository $phoneRepo)
    {
        $phone = $phoneRepo->find($phoneId);
        if (null !== $phone) {
            return  $this->json($phone, 200);
        }

        return $this->json(['message' => 'Phone not found'], 404);
    }

    /**
     * Get a list of all phones
     * 
     * @Route("/api/phones", methods="GET", name="phones")
     * @OA\Get(
     *      path="/api/phones", 
     *      tags={"phone"},
     *      summary="Find all phones",
     *      description="Returns a list of all phones",
     *      @OA\Response(
     *          response="200",
     *          description="successful operation",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="there is no phone for the moment"
     *      ),
     * )
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
