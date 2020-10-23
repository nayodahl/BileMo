<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Service\Paginator;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController extends AbstractController
{
    /**
     * Get the detail of a customer of a logged reseller.
     *
     * @Route("/api/v1/customers/{customerId}", methods="GET", name="app_customer")
     * @OA\Get(
     *      path="/api/v1/customers/{customerId}",
     *      tags={"customer"},
     *      summary="Find customer by Id",
     *      description="Returns a single customer detail, you need to be an authenticated reseller",
     *      @OA\Parameter(
     *          name="customerId",
     *          in="path",
     *          description="Id of customer to return",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int"
     *               )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              ref="#/components/schemas/Customer",
     *              example={
     *                  "id": "200", "firstname": "Michel", "lastname": "Cooper", "email": "m.cooper@exemple.com", "reseller": { "id":"24", "email":"exemple@reseller.com", "customer":"[]", "_links":"..."},
     *                  "_links": {
     *                      "self": "...", "create":"...", "delete":"..."
     *                  },
     *              },
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Customer not found",
     *          @OA\JsonContent(
     *              example={"message": "This customer does not exist, or is not yours"},
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid Id supplied",
     *          @OA\JsonContent(
     *              example={"message": "Bad request. Check your parameters, reminder that documention is here : ..."},
     *          ),
     *     ),
     *      @OA\Response(
     *          response=401,
     *          description="Bearer token missing",
     *          @OA\JsonContent(
     *              example={"code": "401", "message": "JWT Token not found"},
     *          ),
     *      ),
     * )
     */
    public function showCustomer(int $customerId, CustomerRepository $customerRepo, Security $security, SerializerInterface $serializer): Response
    {
        $customer = $customerRepo->findOneCustomerofOneReseller($security->getUser()->getId(), $customerId);
        if (null !== $customer) {
            $json = $serializer->serialize($customer, 'json', SerializationContext::create()->enableMaxDepthChecks());

            $response = new Response($json, 200, ['Content-Type' => 'application/json']);

            // cache publicly for 3600 seconds
            $response->setPublic();
            $response->setMaxAge($this->getParameter('cache_duration'));

            // (optional) set a custom Cache-Control directive
            $response->headers->addCacheControlDirective('must-revalidate', true);

            return $response;
        }

        return $this->json(['message' => 'This customer does not exist, or is not yours'], 404);
    }

    /**
     * Get the list of all customers of a logged reseller.
     *
     * @Route("/api/v1/customers/{page<\d+>?1}", methods="GET", name="app_customers")
     * @OA\Get(
     *      path="/api/v1/customers",
     *      tags={"customer"},
     *      summary="Find all your customers",
     *      description="Returns a paginated list of all your customers, you need to be an authenticated reseller. The list of results is paginated, so if you need next page, add the page number as parameter in the query. Exemple : /api/v1/customers?page=2 ",
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
     *                      {"id": "200", "firstname":"Alice", "lastname":"Cooper", "email": "a.cooper@exemple.com", "reseller": {
     *                              "id":"24", "email":"dev@phonecompany.com", "customer":"[]", "_links": "{...}",
     *                          },
     *                          "_links": {
     *                              "self": "{...}",
     *                              "create": "{...}",
     *                              "delete": "{...}",
     *                          },
     *                      },
     *                      {"id": "202", "firstname":"Emily", "lastname":"Alphin", "email": "alphin@exemple.com", "reseller": {
     *                              "id":"24", "email":"dev@phonecompany.com", "customer":"[]", "_links": "{...}",
     *                          },
     *                          "_links": {
     *                              "self": "{...}",
     *                              "create": "{...}",
     *                              "delete": "{...}",
     *                          },
     *                      },
     *                  },
     *              },
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Customers not found",
     *          @OA\JsonContent(
     *              example={"message": "There is no customer for the moment"},
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid request",
     *          @OA\JsonContent(
     *              example={"message": "Bad request. Check your parameters, reminder that documention is here : ..."},
     *          ),
     *     ),
     *      @OA\Response(
     *          response=401,
     *          description="Bearer token missing",
     *          @OA\JsonContent(
     *              example={"code": "401", "message": "JWT Token not found"},
     *          ),
     *      ),
     * )
     */
    public function showCustomers(CustomerRepository $customerRepo, Security $security, Request $request, SerializerInterface $serializer, Paginator $paginator): Response
    {
        //get data
        $customers = $customerRepo->findAllCustomersofOneReseller($security->getUser()->getId());

        // pagination
        $paginated = $paginator->getPaginatedData(
            $customers,
            $request->query->getInt('page', 1), /*page number*/
            $this->getParameter('pagination_limit'), /*limit per page*/
            $request
        );

        if (null !== $paginated) {
            $json = $serializer->serialize($paginated, 'json', SerializationContext::create()->enableMaxDepthChecks()->setSerializeNull(true));

            $response = new Response($json, 200, ['Content-Type' => 'application/json']);

            // cache publicly for 3600 seconds
            $response->setPublic();
            $response->setMaxAge($this->getParameter('cache_duration'));

            // (optional) set a custom Cache-Control directive
            $response->headers->addCacheControlDirective('must-revalidate', true);

            return $response;
        }

        return $this->json(['message' => 'There is no customer for the moment'], 404);
    }

    /**
     * Create a new customer.
     *
     * @Route("/api/v1/customers", methods="POST", name="app_create_customer")
     * @OA\Post(
     *      path="/api/v1/customers",
     *      tags={"customer"},
     *      summary="Creates a new customer",
     *      description="Creates a new customer linked to your account, you need to be an authenticated reseller",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="firstname",
     *                     description="Enter the firstname of the customer",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="lastname",
     *                     description="Enter the lastname of the customer",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     description="Enter the email of the customer",
     *                     type="string"
     *                 ),
     *                 example={"firstname": "Emily", "lastname": "Cooper", "email": "emily.cooper@mymail.com"}
     *             ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response="201",
     *          description="Customer created",
     *          @OA\JsonContent(
     *              example={"message": "Customer created"},
     *          ),
     *      ),
     *      @OA\Response(
     *         response=400,
     *         description="Invalid input, or duplicate customer",
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
    public function CreateCustomer(CustomerRepository $customerRepo, Request $request, ValidatorInterface $validator, Security $security, SerializerInterface $serializer, LoggerInterface $logger): JsonResponse
    {
        $reseller = $security->getUser();

        $context = new DeserializationContext();
        $context->setGroups('create_customer');

        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json', $context);
        $customer->setReseller($reseller);

        // check if input data is valid, checking entity assertions
        $errors = $validator->validate($customer);
        if (count($errors) > 0) {
            $logger->warning('Customer creation input is invalid', [
                'errors' => $errors,
            ]);

            return $this->json(['message' => $errors], 400);
        }

        // check if customer already exists and linked to this reseller.
        // a customer email must be unique but only to one reseller, indeed a customer can be registred to more than one reseller.
        $customers = $customerRepo->findAllCustomersofOneReseller($reseller->getId())->getResult();
        $match = false;
        foreach ($customers as $value) {
            if ($customer->getEmail() === $value->getEmail()) {
                $match = true;
            }
        }
        if ($match === true) {
            $logger->warning('customer already exists', [
                'cause' => 'email : '.$customer->getEmail().' already exists with reseller id = '.$reseller->getId(),
            ]);

            return $this->json(['message' => 'This customer already exists'], 400);
        }

        // persist new customer and write some log
        $em = $this->getDoctrine()->getManager();
        $em->persist($customer);
        $em->flush();

        $logger->info('new customer created', [
            'email' => $customer->getEmail(),
            'reseller' => $reseller->getId(),
        ]);

        return $this->json(['message' => 'Customer created'], 201);
    }

    /**
     * Delete a customer.
     *
     * @Route("/api/v1/customers/{customerId}", methods="DELETE", name="app_delete_customer")
     * @OA\Delete(
     *      path="/api/v1/customers/{customerId}",
     *      tags={"customer"},
     *      summary="Deletes a customer",
     *      description="Delete a customer linked to your account, you need to be an authenticated reseller.",
     *      @OA\Parameter(
     *         name="customerId",
     *         in="path",
     *         description="Customer Id to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int"
     *         ),
     *      ),
     *      @OA\Response(
     *          response="204",
     *          description="Customer deleted",
     *      ),
     *      @OA\Response(
     *         response=404,
     *         description="Customer not found",
     *          @OA\JsonContent(
     *              example={"message": "Customer does not exist"},
     *          ),
     *      ),
     *      @OA\Response(
     *         response=403,
     *         description="Not authorized deletion",
     *          @OA\JsonContent(
     *              example={"message": "You are not authorized to delete this customer"},
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
    public function DeleteCustomer(int $customerId, CustomerRepository $customerRepo, Security $security, LoggerInterface $logger): JsonResponse
    {
        $reseller = $security->getUser();
        $customer = $customerRepo->find($customerId);

        if (null === $customer) {
            return $this->json(['message' => 'Customer does not exist'], 404);
        }

        // check if the logged reseller is the one that owns the customer
        if ($reseller !== $customer->getReseller()) {
            $logger->warning('customer deletion denied', [
                'customer id' => $customer->getId(),
                'owning reseller id' => $customer->getReseller()->getId(),
                'requestor reseller id' => $reseller->getId(),
            ]);

            return $this->json(['message' => 'You are not authorized to delete this customer'], 403);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($customer);
        $em->flush();

        $logger->info('customer deleted', [
            'id' => $customerId,
            'email' => $customer->getEmail(),
            'reseller' => $reseller->getId(),
        ]);

        return $this->json([], 204);
    }
}
