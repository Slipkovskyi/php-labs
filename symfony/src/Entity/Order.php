<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 */
#[ORM\Entity(repositoryClass: OrderRepository::class)]
class Order implements JsonSerializable
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Client|null
     */
    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Client $client = null;

    /**
     * @var Driver|null
     */
    #[ORM\ManyToOne(targetEntity: Driver::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Driver $driver = null;

    /**
     * @var Route|null
     */
    #[ORM\ManyToOne(targetEntity: Route::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Route $route = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?string $price = null;

    /**
     * @var string|null
     */
    #[ORM\Column(length: 20)]
    #[Assert\Choice(['new', 'done', 'canceled'])]
    private ?string $status = 'new';

    /**
     * @var \DateTimeInterface|null
     */
    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull]
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @var \DateTimeInterface|null
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $completedAt = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Client|null
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     * @return $this
     */
    public function setClient(Client $client): static
    {
        $this->client = $client;
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
     * @return Route|null
     */
    public function getRoute(): ?Route
    {
        return $this->route;
    }

    /**
     * @param Route $route
     * @return $this
     */
    public function setRoute(Route $route): static
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * @param string $price
     * @return $this
     */
    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCompletedAt(): ?\DateTimeInterface
    {
        return $this->completedAt;
    }

    /**
     * @param \DateTimeInterface|null $completedAt
     * @return $this
     */
    public function setCompletedAt(?\DateTimeInterface $completedAt): static
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    /**
     * @return array
     */
    #[ArrayShape([
        'id' => "int|null",
        'clientId' => "int|null",
        'driverId' => "int|null",
        'routeId' => "int|null",
        'price' => "null|string",
        'status' => "null|string",
        'createdAt' => "null|string",
        'completedAt' => "null|string"
    ])]
    public function jsonSerialize(): array
    {
        return [
            'id'          => $this->getId(),
            'clientId'    => $this->getClient()?->getId(),
            'driverId'    => $this->getDriver()?->getId(),
            'routeId'     => $this->getRoute()?->getId(),
            'price'       => $this->getPrice(),
            'status'      => $this->getStatus(),
            'createdAt'   => $this->getCreatedAt()?->format('Y-m-d H:i:s'),
            'completedAt' => $this->getCompletedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
