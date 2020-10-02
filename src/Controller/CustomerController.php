<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Reseller;
use App\Repository\CustomerRepository;
use App\Repository\ResellerRepository;
use Doctrine\Common\Annotations\AnnotationReader;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController extends AbstractController
{
    /**
     * Get the detail of a customer.
     *
     * @Route("/api/resellers/{resellerId}/customers/{customerId}", methods="GET", name="app_customer")
     * @OA\Get(
     *      path="/api/resellers/{resellerId}/customers/{customerId}",
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
    public function showCustomer(int $customerId, CustomerRepository $customerRepo)
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);

        $customer = $customerRepo->find($customerId);
        if (null !== $customer) {
            $data = $serializer->normalize($customer, 'json', ['groups' => 'show_customers']);

            return new JsonResponse($data);
        }

        return $this->json(null, 404);
    }

    /**
     * Get the list of all customers of a given reseller.
     *
     * @Route("/api/resellers/{resellerId}/customers", methods="GET", name="app_customers")
     * @OA\Get(
     *      path="/api/resellers/{resellerId}/customers",
     *      tags={"customer"},
     *      summary="Find all phones",
     *      description="Returns a list of all customers of a given reseller",
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
    public function showCustomers(CustomerRepository $customerRepo)
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);

        $customers = $customerRepo->findAll();
        if (null !== $customers) {
            $data = $serializer->normalize($customers, 'json', ['groups' => 'show_customers']);

            return new JsonResponse($data);
        }

        return $this->json(null, 404);
    }

    /**
     * Create a new customer.
     *
     * @Route("/api/resellers/{resellerId}/customers", methods="POST", name="app_create_customer")
     * @OA\Post(
     *      path="/api/resellers/{resellerId}/customers",
     *      tags={"customer"},
     *      summary="Creates a new customer",
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
    public function CreateCustomer(int $resellerId, ResellerRepository $resellerRepo, Request $request, ValidatorInterface $validator)
    {
        $reseller = new Reseller();
        $reseller = $resellerRepo->find($resellerId);

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
     * @Route("/api/resellers/{resellerId}/customers/{customerId}", methods="DELETE", name="app_delete_customer")
     * @OA\Delete(
     *      path="/api/resellers/{resellerId}/customers/{customerId}",
     *      tags={"customer"},
     *      summary="Deletes a customer",
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
    public function DeleteCustomer(int $customerId, CustomerRepository $customerRepo)
    {
        $customer = $customerRepo->find($customerId);

        if (null !== $customer) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($customer);
            $em->flush();

            return $this->json(['message' => 'customer has been deleted'], 204);
        }

        return $this->json(['message' => 'customer does not exist'], 404);
    }
}
