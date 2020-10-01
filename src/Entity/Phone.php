<?php

namespace App\Entity;

use App\Repository\PhoneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=PhoneRepository::class)
 * @UniqueEntity("internalReference")
 * @OA\Schema(
 *     description="Phone model",
 *     title="Phone",
 * )
 */
class Phone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @OA\Property(
     *     format="int64",
     *     description="ID",
     *     title="ID",
     * )
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @OA\Property(
     *     description="phone Brand",
     *     title="Brand",
     * )
     */
    private $brand;
    
    /**
     * @ORM\Column(type="string", length=255)
     * @OA\Property(
     *     description="phone Model",
     *     title="Model",
     * )
     */
    private $model;

    /**
     * @ORM\Column(type="text")
     * @OA\Property(
     *     description="phone Description",
     *     title="Description",
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     * @OA\Property(
     *     format="float",
     *     description="phone Price",
     *     title="Price",
     * )
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @OA\Property(
     *     description="phone Internal Reference",
     *     title="Internal Reference",
     * )
     */
    private $internalReference;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getInternalReference(): ?string
    {
        return $this->internalReference;
    }

    public function setInternalReference(?string $internalReference): self
    {
        $this->internalReference = $internalReference;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }
}
