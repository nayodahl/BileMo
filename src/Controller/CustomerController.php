<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CustomerController extends AbstractController
{
    /**
     * @Route("/api/resellers/{resellerId}/customers/{customerId}", name="customer")
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
     * @Route("/api/resellers/{resellerId}/customers", name="customers")
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
}
