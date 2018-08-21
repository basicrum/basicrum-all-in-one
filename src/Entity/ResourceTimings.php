<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResourceTimings
 *
 * @ORM\Table(name="resource_timings", indexes={@ORM\Index(name="page_view_id", columns={"page_view_id"})})
 * @ORM\Entity
 */
class ResourceTimings
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text", length=65535, nullable=false)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="initiatorType", type="string", length=64, nullable=false)
     */
    private $initiatortype = '';

    /**
     * @var int
     *
     * @ORM\Column(name="startTime", type="integer", nullable=false)
     */
    private $starttime;

    /**
     * @var int
     *
     * @ORM\Column(name="responseEnd", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $responseend;

    /**
     * @var int
     *
     * @ORM\Column(name="responseStart", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $responsestart;

    /**
     * @var int
     *
     * @ORM\Column(name="requestStart", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $requeststart;

    /**
     * @var int
     *
     * @ORM\Column(name="connectEnd", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $connectend;

    /**
     * @var int
     *
     * @ORM\Column(name="secureConnectionStart", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $secureconnectionstart;

    /**
     * @var int
     *
     * @ORM\Column(name="connectStart", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $connectstart;

    /**
     * @var int
     *
     * @ORM\Column(name="domainLookupEnd", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $domainlookupend;

    /**
     * @var int
     *
     * @ORM\Column(name="domainLookupStart", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $domainlookupstart;

    /**
     * @var int
     *
     * @ORM\Column(name="redirectEnd", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $redirectend;

    /**
     * @var int
     *
     * @ORM\Column(name="redirectStart", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $redirectstart;

    /**
     * @var int
     *
     * @ORM\Column(name="fetchStart", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $fetchstart;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $duration;

    /**
     * @var int|null
     *
     * @ORM\Column(name="encodedBodySize", type="bigint", nullable=true, options={"unsigned"=true})
     */
    private $encodedbodysize;

    /**
     * @var int|null
     *
     * @ORM\Column(name="transferSize", type="bigint", nullable=true, options={"unsigned"=true})
     */
    private $transfersize;

    /**
     * @var int|null
     *
     * @ORM\Column(name="decodedBodySize", type="bigint", nullable=true, options={"unsigned"=true})
     */
    private $decodedbodysize;

    /**
     * @var int|null
     *
     * @ORM\Column(name="height", type="bigint", nullable=true, options={"unsigned"=true})
     */
    private $height;

    /**
     * @var int|null
     *
     * @ORM\Column(name="width", type="bigint", nullable=true, options={"unsigned"=true})
     */
    private $width;

    /**
     * @var int|null
     *
     * @ORM\Column(name="x", type="bigint", nullable=true, options={"unsigned"=true})
     */
    private $x;

    /**
     * @var int|null
     *
     * @ORM\Column(name="y", type="bigint", nullable=true, options={"unsigned"=true})
     */
    private $y;

    /**
     * @var int|null
     *
     * @ORM\Column(name="naturalHeight", type="bigint", nullable=true, options={"unsigned"=true})
     */
    private $naturalheight;

    /**
     * @var int|null
     *
     * @ORM\Column(name="naturalWidth", type="bigint", nullable=true, options={"unsigned"=true})
     */
    private $naturalwidth;

    /**
     * @var \NavigationTimings
     *
     * @ORM\ManyToOne(targetEntity="NavigationTimings")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="page_view_id", referencedColumnName="page_view_id")
     * })
     */
    private $pageView;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getInitiatortype(): ?string
    {
        return $this->initiatortype;
    }

    public function setInitiatortype(string $initiatortype): self
    {
        $this->initiatortype = $initiatortype;

        return $this;
    }

    public function getStarttime(): ?int
    {
        return $this->starttime;
    }

    public function setStarttime(int $starttime): self
    {
        $this->starttime = $starttime;

        return $this;
    }

    public function getResponseend(): ?int
    {
        return $this->responseend;
    }

    public function setResponseend(int $responseend): self
    {
        $this->responseend = $responseend;

        return $this;
    }

    public function getResponsestart(): ?int
    {
        return $this->responsestart;
    }

    public function setResponsestart(int $responsestart): self
    {
        $this->responsestart = $responsestart;

        return $this;
    }

    public function getRequeststart(): ?int
    {
        return $this->requeststart;
    }

    public function setRequeststart(int $requeststart): self
    {
        $this->requeststart = $requeststart;

        return $this;
    }

    public function getConnectend(): ?int
    {
        return $this->connectend;
    }

    public function setConnectend(int $connectend): self
    {
        $this->connectend = $connectend;

        return $this;
    }

    public function getSecureconnectionstart(): ?int
    {
        return $this->secureconnectionstart;
    }

    public function setSecureconnectionstart(int $secureconnectionstart): self
    {
        $this->secureconnectionstart = $secureconnectionstart;

        return $this;
    }

    public function getConnectstart(): ?int
    {
        return $this->connectstart;
    }

    public function setConnectstart(int $connectstart): self
    {
        $this->connectstart = $connectstart;

        return $this;
    }

    public function getDomainlookupend(): ?int
    {
        return $this->domainlookupend;
    }

    public function setDomainlookupend(int $domainlookupend): self
    {
        $this->domainlookupend = $domainlookupend;

        return $this;
    }

    public function getDomainlookupstart(): ?int
    {
        return $this->domainlookupstart;
    }

    public function setDomainlookupstart(int $domainlookupstart): self
    {
        $this->domainlookupstart = $domainlookupstart;

        return $this;
    }

    public function getRedirectend(): ?int
    {
        return $this->redirectend;
    }

    public function setRedirectend(int $redirectend): self
    {
        $this->redirectend = $redirectend;

        return $this;
    }

    public function getRedirectstart(): ?int
    {
        return $this->redirectstart;
    }

    public function setRedirectstart(int $redirectstart): self
    {
        $this->redirectstart = $redirectstart;

        return $this;
    }

    public function getFetchstart(): ?int
    {
        return $this->fetchstart;
    }

    public function setFetchstart(int $fetchstart): self
    {
        $this->fetchstart = $fetchstart;

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

    public function getEncodedbodysize(): ?int
    {
        return $this->encodedbodysize;
    }

    public function setEncodedbodysize(?int $encodedbodysize): self
    {
        $this->encodedbodysize = $encodedbodysize;

        return $this;
    }

    public function getTransfersize(): ?int
    {
        return $this->transfersize;
    }

    public function setTransfersize(?int $transfersize): self
    {
        $this->transfersize = $transfersize;

        return $this;
    }

    public function getDecodedbodysize(): ?int
    {
        return $this->decodedbodysize;
    }

    public function setDecodedbodysize(?int $decodedbodysize): self
    {
        $this->decodedbodysize = $decodedbodysize;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getX(): ?int
    {
        return $this->x;
    }

    public function setX(?int $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?int
    {
        return $this->y;
    }

    public function setY(?int $y): self
    {
        $this->y = $y;

        return $this;
    }

    public function getNaturalheight(): ?int
    {
        return $this->naturalheight;
    }

    public function setNaturalheight(?int $naturalheight): self
    {
        $this->naturalheight = $naturalheight;

        return $this;
    }

    public function getNaturalwidth(): ?int
    {
        return $this->naturalwidth;
    }

    public function setNaturalwidth(?int $naturalwidth): self
    {
        $this->naturalwidth = $naturalwidth;

        return $this;
    }

    public function getPageView(): ?NavigationTimings
    {
        return $this->pageView;
    }

    public function setPageView(?NavigationTimings $pageView): self
    {
        $this->pageView = $pageView;

        return $this;
    }


}
