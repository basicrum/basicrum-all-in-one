<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NavigationTimings
 *
 * @ORM\Table(name="navigation_timings", indexes={@ORM\Index(name="created_at", columns={"created_at"}), @ORM\Index(name="guid", columns={"guid"}), @ORM\Index(name="url_id_2", columns={"url_id", "created_at"}), @ORM\Index(name="created_at_2", columns={"created_at", "user_agent_id"}), @ORM\Index(name="url_id", columns={"url_id"})})
 * @ORM\Entity
 */
class NavigationTimings
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
     * @var int
     *
     * @ORM\Column(name="dns_duration", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $dnsDuration;

    /**
     * @var int
     *
     * @ORM\Column(name="connect_duration", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $connectDuration;

    /**
     * @var int
     *
     * @ORM\Column(name="first_byte", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $firstByte;

    /**
     * @var int
     *
     * @ORM\Column(name="redirect_duration", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $redirectDuration;

    /**
     * @var int
     *
     * @ORM\Column(name="last_byte_duration", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $lastByteDuration;

    /**
     * @var int
     *
     * @ORM\Column(name="first_paint", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $firstPaint;

    /**
     * @var int
     *
     * @ORM\Column(name="first_contentful_paint", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $firstContentfulPaint;

    /**
     * @var bool
     *
     * @ORM\Column(name="redirects_count", type="boolean", nullable=false)
     */
    private $redirectsCount;

    /**
     * @var int
     *
     * @ORM\Column(name="url_id", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $urlId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_agent_id", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $userAgentId;

    /**
     * @var string
     *
     * @ORM\Column(name="process_id", type="string", length=8, nullable=false, options={"fixed"=true})
     */
    private $processId;

    /**
     * @var string
     *
     * @ORM\Column(name="guid", type="string", length=128, nullable=false, options={"fixed"=true})
     */
    private $guid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt = 'CURRENT_TIMESTAMP';

    public function getPageViewId(): ?int
    {
        return $this->pageViewId;
    }

    public function getDnsDuration(): ?int
    {
        return $this->dnsDuration;
    }

    public function setDnsDuration(int $dnsDuration): self
    {
        $this->dnsDuration = $dnsDuration;

        return $this;
    }

    public function getConnectDuration(): ?int
    {
        return $this->connectDuration;
    }

    public function setConnectDuration(int $connectDuration): self
    {
        $this->connectDuration = $connectDuration;

        return $this;
    }

    public function getFirstByte(): ?int
    {
        return $this->firstByte;
    }

    public function setFirstByte(int $firstByte): self
    {
        $this->firstByte = $firstByte;

        return $this;
    }

    public function getRedirectDuration(): ?int
    {
        return $this->redirectDuration;
    }

    public function setRedirectDuration(int $redirectDuration): self
    {
        $this->redirectDuration = $redirectDuration;

        return $this;
    }

    public function getLastByteDuration(): ?int
    {
        return $this->lastByteDuration;
    }

    public function setLastByteDuration(int $lastByteDuration): self
    {
        $this->lastByteDuration = $lastByteDuration;

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

    public function getFirstContentfulPaint(): ?int
    {
        return $this->firstContentfulPaint;
    }

    public function setFirstContentfulPaint(int $firstContentfulPaint): self
    {
        $this->firstContentfulPaint = $firstContentfulPaint;

        return $this;
    }

    public function getRedirectsCount(): ?bool
    {
        return $this->redirectsCount;
    }

    public function setRedirectsCount(bool $redirectsCount): self
    {
        $this->redirectsCount = $redirectsCount;

        return $this;
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

    public function getUserAgentId(): ?int
    {
        return $this->userAgentId;
    }

    public function setUserAgentId(int $userAgentId): self
    {
        $this->userAgentId = $userAgentId;

        return $this;
    }

    public function getProcessId(): ?string
    {
        return $this->processId;
    }

    public function setProcessId(string $processId): self
    {
        $this->processId = $processId;

        return $this;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(string $guid): self
    {
        $this->guid = $guid;

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
