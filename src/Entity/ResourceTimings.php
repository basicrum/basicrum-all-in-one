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
     * @ORM\Column(name="resource_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $resourceId;

    /**
     * @var int
     *
     * @ORM\Column(name="url_id", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $urlId;

    /**
     * @var int
     *
     * @ORM\Column(name="page_view_id", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $pageViewId;

    /**
     * @var int
     *
     * @ORM\Column(name="start", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $start;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $duration;

    public function getResourceId(): ?int
    {
        return $this->resourceId;
    }

    public function getUrlId(): ?int
    {
        return $this->urlId;
    }

    public function setUrlId(int $urlId): self
    {
        $this->urlId = $urlId;

        return $this;
    }

    public function getPageViewId(): ?int
    {
        return $this->pageViewId;
    }

    public function setPageViewId(int $pageViewId): self
    {
        $this->pageViewId = $pageViewId;

        return $this;
    }

    public function getStart(): ?int
    {
        return $this->start;
    }

    public function setStart(int $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }


}
