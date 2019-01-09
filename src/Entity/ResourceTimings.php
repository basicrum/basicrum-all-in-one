<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResourceTimings
 *
 * @ORM\Table(name="resource_timings")
 * @ORM\Entity
 */
class ResourceTimings
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
     * @ORM\Column(name="resource_timings", type="text", length=65535, nullable=false)
     */
    private $resourceTimings;

    public function getPageViewId(): ?int
    {
        return $this->pageViewId;
    }

    public function getResourceTimings(): ?string
    {
        return $this->resourceTimings;
    }

    public function setResourceTimings(string $resourceTimings): self
    {
        $this->resourceTimings = $resourceTimings;

        return $this;
    }


}
