<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 */
#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car implements JsonSerializable
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $model = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private ?string $licensePlate = null;

    /**
     * @var Driver|null
     */
    #[ORM\ManyToOne(targetEntity: Driver::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Driver $driver = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * @param string $model
     * @return $this
     */
    public function setModel(string $model): static
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLicensePlate(): ?string
    {
        return $this->licensePlate;
    }

    /**
     * @param string $licensePlate
     * @return $this
     */
    public function setLicensePlate(string $licensePlate): static
    {
        $this->licensePlate = $licensePlate;
        return $this;
    }

    /**
     * @return Driver|null
     */
    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    /**
     * @param Driver $driver
     * @return $this
     */
    public function setDriver(Driver $driver): static
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @return array
     */
    #[ArrayShape(['id' => "int|null", 'model' => "null|string", 'licensePlate' => "null|string", 'driverId' => "int|null"])]
    public function jsonSerialize(): array
    {
        return [
            'id'           => $this->getId(),
            'model'        => $this->getModel(),
            'licensePlate' => $this->getLicensePlate(),
            'driverId'     => $this->getDriver()?->getId(),
        ];
    }
}
