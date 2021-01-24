<?php

namespace App\Entity;

use App\Repository\MaterialRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MaterialRepository::class)
 */
class Material
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $size;

    /**
     * @ORM\Column(type="integer")
     */
    private $panelPrice;

    /**
     * @ORM\Column(type="integer")
     */
    private $meterPrice;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getPanelPrice(): ?int
    {
        return $this->panelPrice;
    }

    public function setPanelPrice(int $panelPrice): self
    {
        $this->panelPrice = $panelPrice;

        return $this;
    }

    public function getMeterPrice(): ?int
    {
        return $this->meterPrice;
    }

    public function setMeterPrice(int $meterPrice): self
    {
        $this->meterPrice = $meterPrice;

        return $this;
    }
}
