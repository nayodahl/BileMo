<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use App\Service\Paginator;
use JMS\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PhoneController extends AbstractController
{
    /**
     * Get the detail of a phone.
     *
     * @Route("/api/v1/phones/{phoneId}", methods="GET", name="app_phone")
     * @OA\Get(
     *      path="/api/v1/phones/{phoneId}",
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
     *              format="int",
     *          ),
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/Phone",
     *              example={"id": "36", "brand": "Samsung", "description": "Samsung Galaxy S10", "price": "759.0", "internal_reference": "S10-G981BLBDEUB", "_links": "..."},
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Phone not found, the id supplied does not exist",
     *          @OA\JsonContent(
     *              example={"message": "Phone not found"},
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Id supplied",
     *          @OA\JsonContent(
     *              example={"message": "Bad request. Check your parameters, reminder that documention is here : ..."},
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Bearer token missing",
     *          @OA\JsonContent(
     *              example={"code": "401", "message": "JWT Token not found"},
     *          ),
     *      ),
     * )
     */
    public function showPhone(int $phoneId, PhoneRepository $phoneRepo, SerializerInterface $serializer): Response
    {
        $phone = $phoneRepo->find($phoneId);
        if (null !== $phone) {
            $json = $serializer->serialize($phone, 'json');

            $response = new Response($json, 200, ['Content-Type' => 'application/json']);

            // cache publicly for 3600 seconds
            $response->setPublic();
            $response->setMaxAge($this->getParameter('cache_duration'));

            // (optional) set a custom Cache-Control directive
            $response->headers->addCacheControlDirective('must-revalidate', true);

            return $response;
        }

        return $this->json(['message' => 'Phone not found'], 404);
    }

    /**
     * Get a list of all phones.
     *
     * @Route("/api/v1/phones/{page<\d+>?1}", methods="GET", name="app_phones")
     * @OA\Get(
     *      path="/api/v1/phones",
     *      tags={"phone"},
     *      summary="Find all phones",
     *      description="Returns a paginated list of all phones, you need to be an authenticated reseller. The list of results is paginated, so if you need next page, add the page number as parameter in the query. Exemple : /api/v1/phones?page=2 ",
     *      @OA\Response(
     *          response="200",
     *          description="successful operation",
     *          @OA\JsonContent(
     *              example={
     *                  "current_page_number": "1",
     *                  "number_items_per_page": "10",
     *                  "total_items_count": "2",
     *                  "items": {
     *                      {"id": "36", "brand": "Samsung", "description": "Samsung Galaxy S10", "price": "759.0", "internal_reference": "S10-G981BLBDEUB", "_links": "..."},
     *                      {"id": "37", "brand": "Samsung", "description": "Samsung Galaxy S10+", "price": "859.0", "internal_reference": "S10+-G981BLBDEUA", "_links": "..."},
     *                  },
     *              },
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="There is no phone for the moment",
     *          @OA\JsonContent(
     *              example={"message": "There is no phone for the moment"},
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Bearer token missing",
     *          @OA\JsonContent(
     *              example={"code": "401", "message": "JWT Token not found"},
     *          ),
     *      ),
     * )
     */
    public function showPhones(PhoneRepository $phoneRepo, Request $request, SerializerInterface $serializer, Paginator $paginator): Response
    {
        // get data
        $phones = $phoneRepo->findAll();

        // pagination
        $paginated = $paginator->getPaginatedData(
            $phones,
            $request->query->getInt('page', 1), /*page number*/
            $this->getParameter('pagination_limit'),/*limit per page*/
            $request
        );

        if (null !== $paginated) {
            $json = $serializer->serialize($paginated, 'json');

            $response = new Response($json, 200, ['Content-Type' => 'application/json']);

            // cache publicly for 3600 seconds
            $response->setPublic();
            $response->setMaxAge($this->getParameter('cache_duration'));

            // (optional) set a custom Cache-Control directive
            $response->headers->addCacheControlDirective('must-revalidate', true);

            return $response;
        }

        return $this->json(['message' => 'There is no phone for the moment'], 404);
    }
}
