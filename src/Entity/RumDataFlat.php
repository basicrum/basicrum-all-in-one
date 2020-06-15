<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NavigationTimings.
 *
 * @ORM\Table(name="rum_data_flat", indexes={@ORM\Index(name="os_id", columns={"os_id"}), @ORM\Index(name="url_id", columns={"url_id"}), @ORM\Index(name="url_id_2", columns={"url_id", "created_at"}), @ORM\Index(name="user_agent_id", columns={"user_agent_id"}), @ORM\Index(name="device_type_id", columns={"device_type_id"}), @ORM\Index(name="created_at", columns={"created_at"}), @ORM\Index(name="rt_si", columns={"rt_si"}), @ORM\Index(name="rum_data_id", columns={"rum_data_id", "user_agent_id"})})
 * @ORM\Entity
 */
class RumDataFlat
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
    private $rtSi;

    /**
     * @var int
     *
     * @ORM\Column(name="stay_on_page_time", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $stayOnPageTime = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="t_done", type="bigint", length=14, nullable=false, options={"unsigned"=true})
     */
    private $tDone;

    /**
     * @var int
     *
     * @ORM\Column(name="t_page", type="bigint", length=14, nullable=false, options={"unsigned"=true})
     */
    private $tPage;

    /**
     * @var int
     *
     * @ORM\Column(name="t_resp", type="bigint", length=14, nullable=false, options={"unsigned"=true})
     */
    private $tResp;

    /**
     * @var int
     *
     * @ORM\Column(name="t_load", type="bigint", length=14, nullable=false, options={"unsigned"=true})
     */
    private $tLoad;

    /**
     * @var int
     *
     * @ORM\Column(name="rt_tstart", type="bigint", length=14, nullable=false, options={"unsigned"=true})
     */
    private $rtTstart;

    /**
     * @var int
     *
     * @ORM\Column(name="rt_end", type="bigint", length=14, nullable=false, options={"unsigned"=true})
     */
    private $rtEnd;

    /**
     * @var int
     *
     * @ORM\Column(name="rt_quit", type="smallint", length=1, nullable=false, options={"unsigned"=true})
     */
    private $rtQuit;

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
    private $totalImgSize;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalJsCompressedSize;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalJsUncomressedSize;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalCssCompressedSize;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalCssUncomressedSize;

    /**
     * @ORM\Column(type="smallint")
     */
    private $numberJsFiles;

    /**
     * @ORM\Column(type="smallint")
     */
    private $numberCssFiles;

    /**
     * @ORM\Column(type="smallint")
     */
    private $numberImgFiles;

    /**
     * @ORM\Column(type="smallint")
     */
    private $downloadTime;

    public function getRumDataId(): ?int
    {
        return $this->rumDataId;
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
        return $this->rtSi;
    }

    public function setRtsi(string $rtSi): self
    {
        $this->rtSi = $rtSi;

        return $this;
    }

    public function getTdone(): ?int
    {
        return $this->tDone;
    }

    public function setTdone(int $tDone): self
    {
        $this->tDone = $tDone;

        return $this;
    }

    public function getTpage(): ?int
    {
        return $this->tPage;
    }

    public function setTpage(int $tPage): self
    {
        $this->tPage = $tPage;

        return $this;
    }

    public function getTresp(): ?int
    {
        return $this->tResp;
    }

    public function setTresp(int $tResp): self
    {
        $this->tResp = $tResp;

        return $this;
    }

    public function getTload(): ?int
    {
        return $this->tLoad;
    }

    public function setTload(int $tLoad): self
    {
        $this->tLoad = $tLoad;

        return $this;
    }

    public function getRtTstart(): ?int
    {
        return $this->rtTstart;
    }

    public function setRtTstart(int $rtTstart): self
    {
        $this->rtTstart = $rtTstart;

        return $this;
    }

    public function getRtEnd(): ?int
    {
        return $this->rtEnd;
    }

    public function setRtEnd(int $rtEnd): self
    {
        $this->rtEnd = $rtEnd;

        return $this;
    }

    public function getRtQuit(): ?int
    {
        return $this->rtQuit;
    }

    public function setRtQuit(int $rtQuit): self
    {
        $this->rtQuit = $rtQuit;

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
        return $this->totalImgSize;
    }

    public function setTotalImgSize(int $totalImgSize): self
    {
        $this->totalImgSize = $totalImgSize;

        return $this;
    }

    public function getTotalJsCompressedSize(): ?int
    {
        return $this->totalJsCompressedSize;
    }

    public function setTotalJsCompressedSize(int $totalJsCompressedSize): self
    {
        $this->totalJsCompressedSize = $totalJsCompressedSize;

        return $this;
    }

    public function getTotalJsUncomressedSize(): ?int
    {
        return $this->totalJsUncomressedSize;
    }

    public function setTotalJsUncomressedSize(int $totalJsUncomressedSize): self
    {
        $this->totalJsUncomressedSize = $totalJsUncomressedSize;

        return $this;
    }

    public function getTotalCssCompressedSize(): ?int
    {
        return $this->totalCssCompressedSize;
    }

    public function setTotalCssCompressedSize(int $totalCssCompressedSize): self
    {
        $this->totalCssCompressedSize = $totalCssCompressedSize;

        return $this;
    }

    public function getTotalCssUncomressedSize(): ?int
    {
        return $this->totalCssUncomressedSize;
    }

    public function setTotalCssUncomressedSize(int $totalCssUncomressedSize): self
    {
        $this->totalCssUncomressedSize = $totalCssUncomressedSize;

        return $this;
    }

    public function getNumberJsFiles(): ?int
    {
        return $this->numberJsFiles;
    }

    public function setNumberJsFiles(int $numberJsFiles): self
    {
        $this->numberJsFiles = $numberJsFiles;

        return $this;
    }

    public function getNumberCssFiles(): ?int
    {
        return $this->numberCssFiles;
    }

    public function setNumberCssFiles(int $numberCssFiles): self
    {
        $this->numberCssFiles = $numberCssFiles;

        return $this;
    }

    public function getNumberImgFiles(): ?int
    {
        return $this->numberImgFiles;
    }

    public function setNumberImgFiles(int $numberImgFiles): self
    {
        $this->numberImgFiles = $numberImgFiles;

        return $this;
    }

    public function getDownloadTime(): ?int
    {
        return $this->downloadTime;
    }

    public function setDownloadTime(int $downloadTime): self
    {
        $this->downloadTime = $downloadTime;

        return $this;
    }
}
