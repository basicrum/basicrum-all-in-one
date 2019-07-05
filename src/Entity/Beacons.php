<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResourceTimingsUrls
 *
 * @ORM\Table(name="beacons")
 * @ORM\Entity
 */
class Beacons
{
    /**
     * @var int
     *
     * @ORM\Column(name="page_view_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $pageViewId;

    /**
     * @var string
     *
     * @ORM\Column(name="beacon", type="text", length=65535, nullable=false)
     */
    private $beacon;


    public function getPageViewId(): ?int
    {
        return $this->pageViewId;
    }

    public function setPageViewId(int $pageViewId): self
    {
        $this->pageViewId = $pageViewId;

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
