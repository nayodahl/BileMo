<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController extends AbstractController
{
    /**
     * Get the detail of a customer of a logged reseller.
     *
     * @Route("/api/customers/{customerId}", methods="GET", name="app_customer")
     * @OA\Get(
     *      path="/api/customers/{customerId}",
     *      tags={"customer"},
     *      summary="Find customer by ID",
     *      description="Returns a single customer",
     *      @OA\Parameter(
     *          name="customerId",
     *          in="path",
     *          description="ID of customer to return",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int"
     *               )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Customer"),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="customer not found"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid ID supplier"
     *     )
     * )
     */
    public function showCustomer(int $customerId, CustomerRepository $customerRepo, Security $security): JsonResponse
    {
        $customer = $customerRepo->findOneCustomerofOneReseller($security->getUser()->getId(), $customerId);
        if (null !== $customer) {
            return $this->json($customer, 200, [], ['groups' => 'show_customers']);
        }

        return $this->json(['message' => 'this customer does not exist'], 404);
    }

    /**
     * Get the list of all customers of a logged reseller.
     *
     * @Route("/api/customers", methods="GET", name="app_customers")
     * @OA\Get(
     *      path="/api/customers",
     *      tags={"customer"},
     *      summary="Find all your customers",
     *      description="Returns the list of all your customers",
     *      @OA\Response(
     *          response="200",
     *          description="successful operation",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="there is no customer for the moment"
     *      ),
     * )
     */
    public function showCustomers(CustomerRepository $customerRepo, Security $security): JsonResponse
    {
        $customers = $customerRepo->findAllCustomersofOneReseller($security->getUser()->getId());
        if (null !== $customers) {
            return $this->json($customers, 200, [], ['groups' => 'show_customers']);
        }

        return $this->json(['message' => 'there is no customer for the moment'], 404);
    }

    /**
     * Create a new customer.
     *
     * @Route("/api/customers", methods="POST", name="app_create_customer")
     * @OA\Post(
     *      path="/api/customers",
     *      tags={"customer"},
     *      summary="Creates a new customer",
     *      description="Creates a new customer linked to a logged reseller",
     *      @OA\RequestBody(
     *         description="Creates a new customer linked to a logged reseller",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="firstname",
     *                     description="enter the firstname of the customer",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="lastname",
     *                     description="enter the lastname of the customer",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     description="enter the email of the customer",
     *                     type="string"
     *                 ),
     *                 example={"firstname": "Emily", "lastname": "Cooper", "email": "emily.cooper@mymail.com"}
     *             ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response="201",
     *          description="Create a new customer"
     *      ),
     *      @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *      ),
     * )
     */
    public function CreateCustomer(Request $request, ValidatorInterface $validator, Security $security): JsonResponse
    {
        $reseller = $security->getUser();

        $encoder = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoder);

        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $customer->setReseller($reseller);

        $errors = $validator->validate($customer);
        if (count($errors) > 0) {
            return $this->json(['message' => $errors], 400);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($customer);
        $em->flush();

        return $this->json(['message' => 'customer created'], 201);
    }

    /**
     * Delete a customer.
     *
     * @Route("/api/customers/{customerId}", methods="DELETE", name="app_delete_customer")
     * @OA\Delete(
     *      path="/api/customers/{customerId}",
     *      tags={"customer"},
     *      summary="Deletes a customer",
     *      description="Delete a customer linked to a logged reseller",
     *      @OA\Parameter(
     *         name="customerId",
     *         in="path",
     *         description="Customer ID to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int"
     *         ),
     *      ),
     *      @OA\Response(
     *          response="204",
     *          description="Delete a customer"
     *      ),
     *      @OA\Response(
     *         response=404,
     *         description="Customer not found",
     *      ),
     * )
     */
    public function DeleteCustomer(int $customerId, CustomerRepository $customerRepo, Security $security): JsonResponse
    {
        $reseller = $security->getUser();
        $customer = $customerRepo->find($customerId);

        if (null === $customer) {
            return $this->json(['message' => 'customer does not exist'], 404);
        }

        // check if the logged reseller is the one that owns the customer
        if ($reseller !== $customer->getReseller()) {
            return $this->json(['message' => 'you are not authorized to delete this customer'], 403);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($customer);
        $em->flush();

        return $this->json(['message' => 'customer has been deleted'], 204);
    }
}
