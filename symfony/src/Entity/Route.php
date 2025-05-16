<?php

namespace App\Entity;

use App\Repository\RouteRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 */
#[ORM\Entity(repositoryClass: RouteRepository::class)]
class Route implements JsonSerializable
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
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $startPoint = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $endPoint = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'decimal', precision: 6, scale: 2)]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?string $distanceKm = null;

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
    public function getStartPoint(): ?string
    {
        return $this->startPoint;
    }

    /**
     * @param string $startPoint
     * @return $this
     */
    public function setStartPoint(string $startPoint): static
    {
        $this->startPoint = $startPoint;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEndPoint(): ?string
    {
        return $this->endPoint;
    }

    /**
     * @param string $endPoint
     * @return $this
     */
    public function setEndPoint(string $endPoint): static
    {
        $this->endPoint = $endPoint;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDistanceKm(): ?string
    {
        return $this->distanceKm;
    }

    /**
     * @param string $distanceKm
     * @return $this
     */
    public function setDistanceKm(string $distanceKm): static
    {
        $this->distanceKm = $distanceKm;
        return $this;
    }

    /**
     * @return array
     */
    #[ArrayShape(['id' => "int|null", 'startPoint' => "null|string", 'endPoint' => "null|string", 'distanceKm' => "null|string"])]
    public function jsonSerialize(): array
    {
        return [
            'id'         => $this->getId(),
            'startPoint' => $this->getStartPoint(),
            'endPoint'   => $this->getEndPoint(),
            'distanceKm' => $this->getDistanceKm(),
        ];
    }
}
