<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VisitsOverview
 *
 * @ORM\Table(name="visits_overview", indexes={@ORM\Index(name="completed", columns={"completed"}), @ORM\Index(name="first_page_view_id", columns={"first_page_view_id"}), @ORM\Index(name="last_page_view_id", columns={"last_page_view_id"}), @ORM\Index(name="guid", columns={"guid"})})
 * @ORM\Entity
 */
class VisitsOverview
{
    /**
     * @var int
     *
     * @ORM\Column(name="visit_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $visitId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="guid", type="string", length=128, nullable=true, options={"fixed"=true})
     */
    private $guid;

    /**
     * @var int
     *
     * @ORM\Column(name="page_views_count", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $pageViewsCount;

    /**
     * @var int
     *
     * @ORM\Column(name="first_page_view_id", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $firstPageViewId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="last_page_view_id", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $lastPageViewId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="visit_duration", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $visitDuration;

    /**
     * @var int
     *
     * @ORM\Column(name="after_last_visit_duration", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $afterLastVisitDuration;

    /**
     * @var int
     *
     * @ORM\Column(name="first_url_id", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $firstUrlId;

    /**
     * @var int
     *
     * @ORM\Column(name="last_url_id", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $lastUrlId;

    /**
     * @var bool
     *
     * @ORM\Column(name="completed", type="boolean", nullable=false)
     */
    private $completed;

    public function getVisitId(): ?int
    {
        return $this->visitId;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(?string $guid): self
    {
        $this->guid = $guid;

        return $this;
    }

    public function getPageViewsCount(): ?int
    {
        return $this->pageViewsCount;
    }

    public function setPageViewsCount(int $pageViewsCount): self
    {
        $this->pageViewsCount = $pageViewsCount;

        return $this;
    }

    public function getFirstPageViewId(): ?int
    {
        return $this->firstPageViewId;
    }

    public function setFirstPageViewId(int $firstPageViewId): self
    {
        $this->firstPageViewId = $firstPageViewId;

        return $this;
    }

    public function getLastPageViewId(): ?int
    {
        return $this->lastPageViewId;
    }

    public function setLastPageViewId(?int $lastPageViewId): self
    {
        $this->lastPageViewId = $lastPageViewId;

        return $this;
    }

    public function getVisitDuration(): ?int
    {
        return $this->visitDuration;
    }

    public function setVisitDuration(?int $visitDuration): self
    {
        $this->visitDuration = $visitDuration;

        return $this;
    }

    public function getAfterLastVisitDuration(): ?int
    {
        return $this->afterLastVisitDuration;
    }

    public function setAfterLastVisitDuration(int $afterLastVisitDuration): self
    {
        $this->afterLastVisitDuration = $afterLastVisitDuration;

        return $this;
    }

    public function getFirstUrlId(): ?int
    {
        return $this->firstUrlId;
    }

    public function setFirstUrlId(int $firstUrlId): self
    {
        $this->firstUrlId = $firstUrlId;

        return $this;
    }

    public function getLastUrlId(): ?int
    {
        return $this->lastUrlId;
    }

    public function setLastUrlId(int $lastUrlId): self
    {
        $this->lastUrlId = $lastUrlId;

        return $this;
    }

    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }


}
