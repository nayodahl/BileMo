<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use App\Validator as UserAssert;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * @UniqueEntity("email")
 * @OA\Schema(
 *      description="Customer model",
 *      title="Customer",
 * )
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
     *     description="ID",
     *     title="ID",
     * )
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show_resellers", "show_customers"})
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
     * @Groups({"show_resellers", "show_customers"})
     * @Assert\NotBlank
     * @UserAssert\IsValidName
     * @OA\Property(
     *     description="customer Lastname",
     *     title="Lastname",
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"show_resellers", "show_customers"})
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
