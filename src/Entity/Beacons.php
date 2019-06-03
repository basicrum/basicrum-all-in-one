<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Beacons
 *
 * @ORM\Table(name="beacons")
 * @ORM\Entity
 */
class Beacons
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="beacon_data", type="text", length=65535, nullable=false)
     */
    private $beaconData;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBeaconData(): ?string
    {
        return $this->beaconData;
    }

    public function setBeaconData(string $beaconData): self
    {
        $this->beaconData = $beaconData;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }


}
