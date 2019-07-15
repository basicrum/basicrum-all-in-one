<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LastBlockingResources
 *
 * @ORM\Table(name="last_blocking_resources")
 * @ORM\Entity
 */
class LastBlockingResources
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
     * @var int
     *
     * @ORM\Column(name="time", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text", length=65535, nullable=false)
     */
    private $url;

    /**
     * @var int
     *
     * @ORM\Column(name="first_paint", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $firstPaint;

    public function getPageViewId(): ?int
    {
        return $this->pageViewId;
    }

    public function setPageViewId(int $pageViewId): self
    {
        $this->pageViewId = $pageViewId;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(int $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getFirstPaint(): ?int
    {
        return $this->firstPaint;
    }

    public function setFirstPaint(int $firstPaint): self
    {
        $this->firstPaint = $firstPaint;

        return $this;
    }


}
