<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NavigationTimingsQueryParams.
 *
 * @ORM\Table(name="navigation_timings_query_params")
 * @ORM\Entity
 */
class NavigationTimingsQueryParams
{
    /**
     * @var int
     *
     * @ORM\Column(name="page_view_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $pageViewId;

    /**
     * @var string
     *
     * @ORM\Column(name="query_params", type="text", length=65535, nullable=false)
     */
    private $queryParams;

    public function getPageViewId(): ?int
    {
        return $this->pageViewId;
    }

    public function setPageViewId(int $pageViewId): self
    {
        $this->pageViewId = $pageViewId;

        return $this;
    }

    public function getQueryParams(): ?string
    {
        return $this->queryParams;
    }

    public function setQueryParams(string $queryParams): self
    {
        $this->queryParams = $queryParams;

        return $this;
    }
}
