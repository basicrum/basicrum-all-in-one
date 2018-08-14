<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NavigationTimings
 *
 * @ORM\Table(name="navigation_timings", indexes={@ORM\Index(name="guid", columns={"guid"}), @ORM\Index(name="created_at", columns={"created_at"})})
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
     * @var string
     *
     * @ORM\Column(name="url", type="text", length=65535, nullable=false)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="boomerang_version", type="string", length=4, nullable=false)
     */
    private $boomerangVersion = '';

    /**
     * @var string
     *
     * @ORM\Column(name="vis_st", type="string", length=64, nullable=false)
     */
    private $visSt = '';

    /**
     * @var string
     *
     * @ORM\Column(name="ua_plt", type="string", length=64, nullable=false)
     */
    private $uaPlt = '';

    /**
     * @var string
     *
     * @ORM\Column(name="ua_vnd", type="string", length=64, nullable=false)
     */
    private $uaVnd = '';

    /**
     * @var string
     *
     * @ORM\Column(name="pid", type="string", length=32, nullable=false)
     */
    private $pid = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="nt_red_cnt", type="boolean", nullable=false)
     */
    private $ntRedCnt;

    /**
     * @var bool
     *
     * @ORM\Column(name="nt_nav_type", type="boolean", nullable=false)
     */
    private $ntNavType;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_nav_st", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntNavSt;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_red_st", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntRedSt;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_red_end", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntRedEnd;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_fet_st", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntFetSt;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_dns_st", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntDnsSt;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_dns_end", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntDnsEnd;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_con_st", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntConSt;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_con_end", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntConEnd;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_req_st", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntReqSt;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_res_st", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntResSt;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_res_end", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntResEnd;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_domloading", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntDomloading;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_domint", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntDomint;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_domcontloaded_st", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntDomcontloadedSt;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_domcontloaded_end", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntDomcontloadedEnd;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_domcomp", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntDomcomp;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_load_st", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntLoadSt;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_load_end", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntLoadEnd;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_unload_st", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntUnloadSt;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_unload_end", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntUnloadEnd;

    /**
     * @var int
     *
     * @ORM\Column(name="nt_first_paint", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $ntFirstPaint;

    /**
     * @var bool
     *
     * @ORM\Column(name="nt_spdy", type="boolean", nullable=false)
     */
    private $ntSpdy;

    /**
     * @var string
     *
     * @ORM\Column(name="nt_cinf", type="string", length=64, nullable=false)
     */
    private $ntCinf = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt = 'CURRENT_TIMESTAMP';

    /**
     * @var string|null
     *
     * @ORM\Column(name="guid", type="string", length=128, nullable=true)
     */
    private $guid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_agent", type="text", length=65535, nullable=true)
     */
    private $userAgent;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pt_fp", type="string", length=11, nullable=true)
     */
    private $ptFp;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pt_fcp", type="string", length=11, nullable=true)
     */
    private $ptFcp;

    public function getPageViewId(): ?int
    {
        return $this->pageViewId;
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

    public function getBoomerangVersion(): ?string
    {
        return $this->boomerangVersion;
    }

    public function setBoomerangVersion(string $boomerangVersion): self
    {
        $this->boomerangVersion = $boomerangVersion;

        return $this;
    }

    public function getVisSt(): ?string
    {
        return $this->visSt;
    }

    public function setVisSt(string $visSt): self
    {
        $this->visSt = $visSt;

        return $this;
    }

    public function getUaPlt(): ?string
    {
        return $this->uaPlt;
    }

    public function setUaPlt(string $uaPlt): self
    {
        $this->uaPlt = $uaPlt;

        return $this;
    }

    public function getUaVnd(): ?string
    {
        return $this->uaVnd;
    }

    public function setUaVnd(string $uaVnd): self
    {
        $this->uaVnd = $uaVnd;

        return $this;
    }

    public function getPid(): ?string
    {
        return $this->pid;
    }

    public function setPid(string $pid): self
    {
        $this->pid = $pid;

        return $this;
    }

    public function getNtRedCnt(): ?bool
    {
        return $this->ntRedCnt;
    }

    public function setNtRedCnt(bool $ntRedCnt): self
    {
        $this->ntRedCnt = $ntRedCnt;

        return $this;
    }

    public function getNtNavType(): ?bool
    {
        return $this->ntNavType;
    }

    public function setNtNavType(bool $ntNavType): self
    {
        $this->ntNavType = $ntNavType;

        return $this;
    }

    public function getNtNavSt(): ?int
    {
        return $this->ntNavSt;
    }

    public function setNtNavSt(int $ntNavSt): self
    {
        $this->ntNavSt = $ntNavSt;

        return $this;
    }

    public function getNtRedSt(): ?int
    {
        return $this->ntRedSt;
    }

    public function setNtRedSt(int $ntRedSt): self
    {
        $this->ntRedSt = $ntRedSt;

        return $this;
    }

    public function getNtRedEnd(): ?int
    {
        return $this->ntRedEnd;
    }

    public function setNtRedEnd(int $ntRedEnd): self
    {
        $this->ntRedEnd = $ntRedEnd;

        return $this;
    }

    public function getNtFetSt(): ?int
    {
        return $this->ntFetSt;
    }

    public function setNtFetSt(int $ntFetSt): self
    {
        $this->ntFetSt = $ntFetSt;

        return $this;
    }

    public function getNtDnsSt(): ?int
    {
        return $this->ntDnsSt;
    }

    public function setNtDnsSt(int $ntDnsSt): self
    {
        $this->ntDnsSt = $ntDnsSt;

        return $this;
    }

    public function getNtDnsEnd(): ?int
    {
        return $this->ntDnsEnd;
    }

    public function setNtDnsEnd(int $ntDnsEnd): self
    {
        $this->ntDnsEnd = $ntDnsEnd;

        return $this;
    }

    public function getNtConSt(): ?int
    {
        return $this->ntConSt;
    }

    public function setNtConSt(int $ntConSt): self
    {
        $this->ntConSt = $ntConSt;

        return $this;
    }

    public function getNtConEnd(): ?int
    {
        return $this->ntConEnd;
    }

    public function setNtConEnd(int $ntConEnd): self
    {
        $this->ntConEnd = $ntConEnd;

        return $this;
    }

    public function getNtReqSt(): ?int
    {
        return $this->ntReqSt;
    }

    public function setNtReqSt(int $ntReqSt): self
    {
        $this->ntReqSt = $ntReqSt;

        return $this;
    }

    public function getNtResSt(): ?int
    {
        return $this->ntResSt;
    }

    public function setNtResSt(int $ntResSt): self
    {
        $this->ntResSt = $ntResSt;

        return $this;
    }

    public function getNtResEnd(): ?int
    {
        return $this->ntResEnd;
    }

    public function setNtResEnd(int $ntResEnd): self
    {
        $this->ntResEnd = $ntResEnd;

        return $this;
    }

    public function getNtDomloading(): ?int
    {
        return $this->ntDomloading;
    }

    public function setNtDomloading(int $ntDomloading): self
    {
        $this->ntDomloading = $ntDomloading;

        return $this;
    }

    public function getNtDomint(): ?int
    {
        return $this->ntDomint;
    }

    public function setNtDomint(int $ntDomint): self
    {
        $this->ntDomint = $ntDomint;

        return $this;
    }

    public function getNtDomcontloadedSt(): ?int
    {
        return $this->ntDomcontloadedSt;
    }

    public function setNtDomcontloadedSt(int $ntDomcontloadedSt): self
    {
        $this->ntDomcontloadedSt = $ntDomcontloadedSt;

        return $this;
    }

    public function getNtDomcontloadedEnd(): ?int
    {
        return $this->ntDomcontloadedEnd;
    }

    public function setNtDomcontloadedEnd(int $ntDomcontloadedEnd): self
    {
        $this->ntDomcontloadedEnd = $ntDomcontloadedEnd;

        return $this;
    }

    public function getNtDomcomp(): ?int
    {
        return $this->ntDomcomp;
    }

    public function setNtDomcomp(int $ntDomcomp): self
    {
        $this->ntDomcomp = $ntDomcomp;

        return $this;
    }

    public function getNtLoadSt(): ?int
    {
        return $this->ntLoadSt;
    }

    public function setNtLoadSt(int $ntLoadSt): self
    {
        $this->ntLoadSt = $ntLoadSt;

        return $this;
    }

    public function getNtLoadEnd(): ?int
    {
        return $this->ntLoadEnd;
    }

    public function setNtLoadEnd(int $ntLoadEnd): self
    {
        $this->ntLoadEnd = $ntLoadEnd;

        return $this;
    }

    public function getNtUnloadSt(): ?int
    {
        return $this->ntUnloadSt;
    }

    public function setNtUnloadSt(int $ntUnloadSt): self
    {
        $this->ntUnloadSt = $ntUnloadSt;

        return $this;
    }

    public function getNtUnloadEnd(): ?int
    {
        return $this->ntUnloadEnd;
    }

    public function setNtUnloadEnd(int $ntUnloadEnd): self
    {
        $this->ntUnloadEnd = $ntUnloadEnd;

        return $this;
    }

    public function getNtFirstPaint(): ?int
    {
        return $this->ntFirstPaint;
    }

    public function setNtFirstPaint(int $ntFirstPaint): self
    {
        $this->ntFirstPaint = $ntFirstPaint;

        return $this;
    }

    public function getNtSpdy(): ?bool
    {
        return $this->ntSpdy;
    }

    public function setNtSpdy(bool $ntSpdy): self
    {
        $this->ntSpdy = $ntSpdy;

        return $this;
    }

    public function getNtCinf(): ?string
    {
        return $this->ntCinf;
    }

    public function setNtCinf(string $ntCinf): self
    {
        $this->ntCinf = $ntCinf;

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

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(?string $guid): self
    {
        $this->guid = $guid;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getPtFp(): ?string
    {
        return $this->ptFp;
    }

    public function setPtFp(?string $ptFp): self
    {
        $this->ptFp = $ptFp;

        return $this;
    }

    public function getPtFcp(): ?string
    {
        return $this->ptFcp;
    }

    public function setPtFcp(?string $ptFcp): self
    {
        $this->ptFcp = $ptFcp;

        return $this;
    }


}
