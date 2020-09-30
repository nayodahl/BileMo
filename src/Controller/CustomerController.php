<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Reseller;
use App\Repository\CustomerRepository;
use App\Repository\ResellerRepository;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CustomerController extends AbstractController
{
    /**
     * @Route("/api/resellers/{resellerId}/customers/{customerId}", methods="GET", name="app_customer")
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
     * @Route("/api/resellers/{resellerId}/customers", methods="GET", name="app_customers")
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
     * @Route("/api/resellers/{resellerId}/customers", methods="POST", name="app_create_customer")
     */
    public function CreateCustomer(int $resellerId, ResellerRepository $resellerRepo, Request $request)
    {
        $reseller = new Reseller();
        $reseller = $resellerRepo->find($resellerId);

        $encoder = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoder);

        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $customer->setReseller($reseller);

        $em = $this->getDoctrine()->getManager();
        $em->persist($customer);
        $em->flush();

        return $this->json('customer created', 201);
    }

    /**
     * @Route("/api/resellers/{resellerId}/customers/{customerId}", methods="DELETE", name="app_delete_customer")
     */
    public function DeleteCustomer(int $customerId, CustomerRepository $customerRepo)
    {
        $customer = $customerRepo->find($customerId);

        if (null !== $customer) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($customer);
            $em->flush();

            return $this->json(null, 204);
        }

        return $this->json(null, 404);
    }
}
