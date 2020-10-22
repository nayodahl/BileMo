<?php

namespace App\Entity;

use Ambta\DoctrineEncryptBundle\Configuration\Encrypted;
use App\Repository\CustomerRepository;
use App\Validator as UserAssert;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\Type;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * @OA\Schema(
 *      description="Customer model",
 *      title="Customer",
 * )
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "app_customer",
 *          parameters = { "customerId" = "expr(object.getId())" },
 *          absolute = true,
 *      )
 * )
 * @Hateoas\Relation(
 *      "create",
 *      href = @Hateoas\Route(
 *          "app_create_customer",
 *          absolute = true,
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "app_delete_customer",
 *          parameters = { "customerId" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Serializer\XmlRoot("customer")
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"show_resellers", "show_customers"})
     * @OA\Property(
     *     format="int64",
     *     description="Id",
     *     title="Id",
     * )
     * @Serializer\XmlAttribute
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_resellers", "show_customers", "create_customer"})
     * @Type("string")
     * @Encrypted
     * @Assert\NotBlank
     * @UserAssert\IsValidName
     * @OA\Property(
     *     description="customer Firstname",
     *     title="Firstname",
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_resellers", "show_customers", "create_customer"})
     * @Type("string")
     * @Encrypted
     * @Assert\NotBlank
     * @UserAssert\IsValidName
     * @OA\Property(
     *     description="customer Lastname",
     *     title="Lastname",
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_resellers", "show_customers", "create_customer"})
     * @Type("string")
     * @Encrypted
     * @Assert\NotBlank
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     * @OA\Property(
     *     description="customer Email",
     *     title="Email",
     * )
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=Reseller::class, inversedBy="customer")
     * @ORM\JoinColumn(nullable=false)
     * @OA\Property(
     *     description="customer parent Reseller",
     *     title="Reseller",
     * )
     * @MaxDepth(1)
     */
    private $reseller;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getReseller(): ?Reseller
    {
        return $this->reseller;
    }

    public function setReseller(?Reseller $reseller): self
    {
        $this->reseller = $reseller;

        return $this;
    }
}
