<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NavigationTimings.
 *
 * @ORM\Table(name="navigation_timings", indexes={@ORM\Index(name="os_id", columns={"os_id"}), @ORM\Index(name="url_id", columns={"url_id"}), @ORM\Index(name="url_id_2", columns={"url_id", "created_at"}), @ORM\Index(name="user_agent_id", columns={"user_agent_id"}), @ORM\Index(name="device_type_id", columns={"device_type_id"}), @ORM\Index(name="created_at", columns={"created_at"}), @ORM\Index(name="rt_si", columns={"rt_si"}), @ORM\Index(name="page_view_id", columns={"page_view_id", "user_agent_id"})})
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
     * @ORM\Column(name="redirects_count", type="smallint", nullable=false)
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
     * @var bool
     *
     * @ORM\Column(name="device_type_id", type="integer", nullable=false)
     */
    private $deviceTypeId;

    /**
     * @var bool
     *
     * @ORM\Column(name="os_id", type="integer", nullable=false)
     */
    private $osId;

    /**
     * @var string
     *
     * @ORM\Column(name="process_id", type="string", length=8, nullable=false, options={"fixed"=true})
     */
    private $processId;

    /**
     * @var string
     *
     * @ORM\Column(name="rt_si", type="string", length=128, nullable=false, options={"fixed"=true})
     */
    private $rt_si;

    /**
     * @var int
     *
     * @ORM\Column(name="stay_on_page_time", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $stayOnPageTime = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt = 'CURRENT_TIMESTAMP';

    /**
     * @var int
     *
     * @ORM\Column(name="load_event_end", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $loadEventEnd;

    /**
     * @ORM\Column(type="smallint")
     */
    private $ttfb;

    /**
     * @ORM\Column(type="integer")
     */
    private $total_img_size;

    /**
     * @ORM\Column(type="integer")
     */
    private $total_js_compressed_size;

    /**
     * @ORM\Column(type="integer")
     */
    private $total_js_uncomressed_size;

    /**
     * @ORM\Column(type="integer")
     */
    private $total_css_compressed_size;

    /**
     * @ORM\Column(type="integer")
     */
    private $total_css_uncomressed_size;

    /**
     * @ORM\Column(type="smallint")
     */
    private $number_js_files;

    /**
     * @ORM\Column(type="smallint")
     */
    private $number_css_files;

    /**
     * @ORM\Column(type="smallint")
     */
    private $number_img_files;

    /**
     * @ORM\Column(type="smallint")
     */
    private $download_time;

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

    public function getRedirectsCount(): ?int
    {
        return $this->redirectsCount;
    }

    public function setRedirectsCount(int $redirectsCount): self
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

    public function getDeviceTypeId(): int
    {
        return $this->deviceTypeId;
    }

    public function setDeviceTypeId(int $deviceTypeId): self
    {
        $this->deviceTypeId = $deviceTypeId;

        return $this;
    }

    public function getOsId(): int
    {
        return $this->osId;
    }

    public function setOsId(int $osId): self
    {
        $this->osId = $osId;

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

    public function getRtsi(): ?string
    {
        return $this->rt_si;
    }

    public function setRtsi(string $rt_si): self
    {
        $this->rt_si = $rt_si;

        return $this;
    }

    public function getStayOnPageTime(): ?int
    {
        return $this->stayOnPageTime;
    }

    public function setStayOnPageTime(int $stayOnPageTime): self
    {
        $this->stayOnPageTime = $stayOnPageTime;

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

    public function getLoadEventEnd(): ?int
    {
        return $this->loadEventEnd;
    }

    public function setLoadEventEnd(int $loadEventEnd): self
    {
        $this->loadEventEnd = $loadEventEnd;

        return $this;
    }

    public function getTtfb(): ?int
    {
        return $this->ttfb;
    }

    public function setTtfb(int $ttfb): self
    {
        $this->ttfb = $ttfb;

        return $this;
    }

    public function getTotalImgSize(): ?int
    {
        return $this->total_img_size;
    }

    public function setTotalImgSize(int $total_img_size): self
    {
        $this->total_img_size = $total_img_size;

        return $this;
    }

    public function getTotalJsCompressedSize(): ?int
    {
        return $this->total_js_compressed_size;
    }

    public function setTotalJsCompressedSize(int $total_js_compressed_size): self
    {
        $this->total_js_compressed_size = $total_js_compressed_size;

        return $this;
    }

    public function getTotalJsUncomressedSize(): ?int
    {
        return $this->total_js_uncomressed_size;
    }

    public function setTotalJsUncomressedSize(int $total_js_uncomressed_size): self
    {
        $this->total_js_uncomressed_size = $total_js_uncomressed_size;

        return $this;
    }

    public function getTotalCssCompressedSize(): ?int
    {
        return $this->total_css_compressed_size;
    }

    public function setTotalCssCompressedSize(int $total_css_compressed_size): self
    {
        $this->total_css_compressed_size = $total_css_compressed_size;

        return $this;
    }

    public function getTotalCssUncomressedSize(): ?int
    {
        return $this->total_css_uncomressed_size;
    }

    public function setTotalCssUncomressedSize(int $total_css_uncomressed_size): self
    {
        $this->total_css_uncomressed_size = $total_css_uncomressed_size;

        return $this;
    }

    public function getNumberJsFiles(): ?int
    {
        return $this->number_js_files;
    }

    public function setNumberJsFiles(int $number_js_files): self
    {
        $this->number_js_files = $number_js_files;

        return $this;
    }

    public function getNumberCssFiles(): ?int
    {
        return $this->number_css_files;
    }

    public function setNumberCssFiles(int $number_css_files): self
    {
        $this->number_css_files = $number_css_files;

        return $this;
    }

    public function getNumberImgFiles(): ?int
    {
        return $this->number_img_files;
    }

    public function setNumberImgFiles(int $number_img_files): self
    {
        $this->number_img_files = $number_img_files;

        return $this;
    }

    public function getDownloadTime(): ?int
    {
        return $this->download_time;
    }

    public function setDownloadTime(int $download_time): self
    {
        $this->download_time = $download_time;

        return $this;
    }
}
