<?php

namespace App\Controller;

use App\Entity\Reseller;
use App\Repository\ResellerRepository;
use App\Service\Paginator;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResellerController extends AbstractController
{
    /**
     * Get the detail of a reseller.
     *
     * @Route("/api/v1/resellers/{resellerId}", methods="GET", name="app_reseller")
     * @OA\Get(
     *      path="/api/v1/resellers/{resellerId}",
     *      tags={"reseller"},
     *      summary="Find reseller by Id",
     *      description="Returns a single reseller detail with all its customers, you need to be an authenticated admin",
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
     *          @OA\JsonContent(
     *              ref="#/components/schemas/Reseller",
     *              example={
     *                  "id": "22", "email": "exemple@phonecompany.com", "customer": {
     *                      {"id": "155", "firstname":"Natalia", "lastname":"Bashirian", "email":"coldy.san@yahoo.fr", "_links": "..."},
     *                      {"id": "156", "firstname":"Rachel", "lastname":"Dupont", "email":"rachel.d@free.fr", "_links": "..."},
     *                  },
     *                  "_links": "..."
     *                  },
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Reseller not found, the id supplied does not exist",
     *          @OA\JsonContent(
     *              example={"message": "Reseller not found"},
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
     *      @OA\Response(
     *          response=403,
     *          description="Not authorized access",
     *          @OA\JsonContent(
     *              example={"message": "You must be an admin to access this"},
     *          ),
     *      ),
     * )
     */
    public function showReseller(int $resellerId, ResellerRepository $resellerRepo, SerializerInterface $serializer): Response
    {
        if (false === $this->isGranted('ROLE_ADMIN')) {
            return $this->json(['message' => 'You must be an admin to access this'], 403);
        }

        $reseller = $resellerRepo->find($resellerId);
        if (null !== $reseller) {
            $json = $serializer->serialize($reseller, 'json');

            $response = new Response($json, 200, ['Content-Type' => 'application/json']);

            // cache publicly for 3600 seconds
            $response->setPublic();
            $response->setMaxAge($this->getParameter('cache_duration'));

            // (optional) set a custom Cache-Control directive
            $response->headers->addCacheControlDirective('must-revalidate', true);

            return $response;
        }

        return $this->json(['message' => 'Reseller not found'], 404);
    }

    /**
     * Get the list of all resellers.
     *
     * @Route("/api/v1/resellers", methods="GET", name="app_resellers")
     * @OA\Get(
     *      path="/api/v1/resellers",
     *      tags={"reseller"},
     *      summary="Find all resellers",
     *      description="Returns a paginated list of all resellers, you need to be an authenticated admin. The list of results is paginated, so if you need next page, add the page number as parameter in the query. Exemple : /api/v1/resellers?page=2 ",
     *      @OA\Response(
     *          response="200",
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              example={
     *                  "current_page_number": "1",
     *                  "number_items_per_page": "10",
     *                  "total_items_count": "2",
     *                  "previous_page_link": "null",
     *                  "next_page_link": "null",
     *                  "items": {
     *                      {"id": "22", "email": "exemple@phonecompany.com", "_links": "..."},
     *                      {"id": "23", "email": "dev@phonereseller.com", "_links": "..."},
     *                  },
     *              },
     *          ),
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="There is no reseller for the moment",
     *          @OA\JsonContent(
     *              example={"message": "There is no reseller for the moment"},
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Bearer token missing",
     *          @OA\JsonContent(
     *              example={"code": "401", "message": "JWT Token not found"},
     *          ),
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Not authorized access",
     *          @OA\JsonContent(
     *              example={"message": "You must be an admin to access this"},
     *          ),
     *      ),
     * )
     */
    public function showResellers(ResellerRepository $resellerRepo, Request $request, SerializerInterface $serializer, Paginator $paginator): Response
    {
        // we first test if the reseller has admin rights
        if (false === $this->isGranted('ROLE_ADMIN')) {
            return $this->json(['message' => 'you must be an admin to access this'], 403);
        }

        // we now know that there is at least one reseller, there can not be empty data from pagination
        $resellers = $resellerRepo->findAll();

        // pagination
        $paginated = $paginator->getPaginatedData(
            $resellers,
            $request->query->getInt('page', 1), /*page number*/
            $this->getParameter('pagination_limit'), /*limit per page*/
            $request
        );

        $json = $serializer->serialize($paginated, 'json', SerializationContext::create()->setSerializeNull(true));

        $response = new Response($json, 200, ['Content-Type' => 'application/json']);

        // cache publicly for 3600 seconds
        $response->setPublic();
        $response->setMaxAge($this->getParameter('cache_duration'));

        // (optional) set a custom Cache-Control directive
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }

    /**
     * @Route ("/api/v1/auth/signin", name = "api_signin", methods = "POST")
     * @OA\Post(
     *      path="/api/v1/auth/signin",
     *      tags={"login and signin"},
     *      summary="Signin to BileMo API",
     *      description="Signin to BileMo API, to create your reseller account",
     *      @OA\RequestBody(
     *         description="Enter your email and the password of your choice. The password must respects following rules : minimum 8 characters, one uppercase, one lowercase, one number and one special character among #?!@$ %^&*-).",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     description="Enter your email as identifier, it must not be already taken",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="Enter your chosen password, minimum 8 characters, one uppercase, one lowercase, one number and one special character among #?!@$ %^&*-).",
     *                     type="string"
     *                 ),
     *                 example={"email": "exemple@mymail.com", "password": "mychosenpassword"},
     *             ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response="201",
     *          description="successful operation",
     *          @OA\JsonContent(
     *              example={"message": "You registered as a Reseller with success"},
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid input",
     *          @OA\JsonContent(
     *              example={"message": "Bad request. Check your parameters, reminder that documention is here : ..."},
     *          ),
     *      ),
     * )
     */
    public function register(UserPasswordEncoderInterface $passwordEncoder, Request $request, ValidatorInterface $validator, LoggerInterface $logger): JsonResponse
    {
        $encoder = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoder);

        $data = $request->getContent();

        $reseller = $serializer->deserialize($data, Reseller::class, 'json');

        // check if input data is valid (email valid and unique, password complex enough)
        $errors = $validator->validate($reseller);
        if (count($errors) > 0) {
            $logger->warning('registration input is invalid', [
                'errors' => $errors,
            ]);

            return $this->json(['message' => $errors], 400);
        }

        // encode password
        $reseller->setPassword($passwordEncoder->encodePassword($reseller, $reseller->getPassword()));

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($reseller);
        $manager->flush();

        $logger->info('new reseller registrated', [
            'email' => $reseller->getEmail(),
        ]);

        return $this->json(['result' => 'You registered as a Reseller with success'], 201);
    }

    /*
     * @OA\Post(
     *      path="/api/v1/auth/login",
     *      tags={"login and signin"},
     *      summary="Login to BileMo API to get your authentication token (Bearer token)",
     *      description="This can only be done by a registred reseller. It will let you obtain a token to make all others requests to the API that need authentication (almost all requests).",
     *      @OA\RequestBody(
     *         description="Enter your credentials",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     description="Enter the email you registred with as identifier",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="Enter your password",
     *                     type="string"
     *                 ),
     *                 example={"email": "exemple@mymail.com", "password": "mypassword"}
     *             ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="successful operation",
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="token",
     *                     description="Your precious token",
     *                     type="string",
     *                 ),
     *                 example={"token": "eyJ7eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MDIwNTU3NjAsImV4cCI6MTYwMjA1OTM2MCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiZGV2QGxkbGMuY29tIn0.SI5UDCNxGewjFJt86olg4DbmHx6Hl9E1UqGHAWhEXIiDJNWlKVq4_evwIuuk-EPoZV7BfEuAXU19_VFg1sGbEDhs20pzOC3G8pwKNZb_NTJ1E_tZ2Wq5GQpGw38uJa6qbYg4LoVs8EyMKrul-GQXA__Tm7blr9CU40PRrhMU4LdNf9wSitYFQ_9PJS0KpvjRfDgEMmt41QB-uUh2rUbNXcfUzfake5zeQQq_AoWMZBas3mUYdZe5np0jQvNHyuw2rit2OEIhVnZzHtMbVg6XACmYy9hHw--gQ7sjiSpqTq5ZeXW1b8AWTLQRiYMC3gLU89lvRHZs4GZLUZ4_c-4mxVNMBSf5J0yjHGW4buzVy5lx9rEY1tW9XeuYPKXKODisPNcX3p1j8XKwgEdjBC4LkhlDERFoADCYH75F5IURaMpj-HSs2U6fNcduQlm8NHd_y_ziywjj6a8qjvnIvUqWOMgYjSeesVBTZvWvNBiOqZ1yRdjGAmDw5KSPReTKPsq6IBHQersaZ_YMXwakVaTdJi7IZ-IhjJTIHuBxtlfYQLNyJWHQTTMfoJPto4FFwtNysKvus1v9RIKACoB9KZcYm2gN9dbKFZFenCWHm-pGeLWGzpKdI-2Km-egT7WX9X27BHHhhqx7RfKa7AWO9JR3G20vpbSBfx8YeVXWofesW2I"}
     *             ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid input",
     *          @OA\JsonContent(
     *              example={"message": "Bad request. Check your parameters, reminder that documention is here : ..."},
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Invalid credentials",
     *          @OA\JsonContent(
     *              example={"code": "401", "message": "Invalid credentials."},
     *          ),
     *      ),
     * )
     **/
}
