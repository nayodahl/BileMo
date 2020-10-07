<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PhoneController extends AbstractController
{
    /**
     * Get the detail of a phone.
     *
     * @Route("/api/phones/{phoneId}", methods="GET", name="app_phone")
     * @OA\Get(
     *      path="/api/phones/{phoneId}",
     *      tags={"phone"},
     *      summary="Find phone by Id",
     *      description="Returns a single phone detail, you need to be an authenticated reseller",
     *      @OA\Parameter(
     *          name="phoneId",
     *          in="path",
     *          description="Id of phone to return",
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
    public function showPhone(int $phoneId, PhoneRepository $phoneRepo): JsonResponse
    {
        $phone = $phoneRepo->find($phoneId);
        if (null !== $phone) {
            return  $this->json($phone, 200);
        }

        return $this->json(['message' => 'Phone not found'], 404);
    }

    /**
     * Get a list of all phones.
     *
     * @Route("/api/phones/{page<\d+>?1}", methods="GET", name="app_phones")
     * @OA\Get(
     *      path="/api/phones",
     *      tags={"phone"},
     *      summary="Find all phones",
     *      description="Returns a paginated list of all phones, you need to be an authenticated reseller. The list of results is paginated, so if you need next page, add the page number as parameter in the query. Exemple : /api/phones?page=2 ",
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
    public function showPhones(PhoneRepository $phoneRepo, Request $request): JsonResponse
    {
        // pagination info
        $page = $request->query->get('page');
        if ((null === $page) || $page < 1) {
            $page = 1;
        }
        // get data
        $phones = $phoneRepo->findAllPhones($page, $this->getParameter('pagination_limit'));
        if (null !== $phones) {
            return  $this->json($phones, 200);
        }

        return $this->json(['message' => 'there is no phone for the moment'], 404);
    }
}
