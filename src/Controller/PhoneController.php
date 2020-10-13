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
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="there is no phone for the moment"
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

        if (null !== $phones) {
            $json = $serializer->serialize($paginated, 'json');

            $response = new Response($json, 200, ['Content-Type' => 'application/json']);

            // cache publicly for 3600 seconds
            $response->setPublic();
            $response->setMaxAge($this->getParameter('cache_duration'));

            // (optional) set a custom Cache-Control directive
            $response->headers->addCacheControlDirective('must-revalidate', true);

            return $response;
        }

        return $this->json(['message' => 'there is no phone for the moment'], 404);
    }
}
