<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResourceTimingsUrls.
 *
 * @ORM\Table(name="beacons")
 * @ORM\Entity
 */
class Beacons
{
    /**
     * @var int
     *
     * @ORM\Column(name="rum_data_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $rumDataId;

    /**
     * @var string
     *
     * @ORM\Column(name="beacon", type="text", length=65535, nullable=false)
     */
    private $beacon;

    public function getRumDataId(): ?int
    {
        return $this->rumDataId;
    }

    public function setRumDataId(int $rumDataId): self
    {
        $this->rumDataId = $rumDataId;

        return $this;
    }

    public function getBeacon(): ?string
    {
        return $this->beacon;
    }

    public function setBeacon(string $beacon): self
    {
        $this->beacon = $beacon;

        return $this;
    }
}
