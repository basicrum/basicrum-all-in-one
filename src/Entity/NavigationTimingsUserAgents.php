<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NavigationTimingsUserAgents
 *
 * @ORM\Table(name="navigation_timings_user_agents", indexes={@ORM\Index(name="device_type_id", columns={"device_type_id"}), @ORM\Index(name="created_at", columns={"created_at"}), @ORM\Index(name="os_id", columns={"os_id"})})
 * @ORM\Entity
 */
class NavigationTimingsUserAgents
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
     * @ORM\Column(name="user_agent", type="text", length=65535, nullable=false)
     */
    private $userAgent;

    /**
     * @var string
     *
     * @ORM\Column(name="device_type", type="text", length=65535, nullable=false)
     */
    private $deviceType;

    /**
     * @var bool
     *
     * @ORM\Column(name="device_type_id", type="integer", nullable=false)
     */
    private $deviceTypeId;

    /**
     * @var string
     *
     * @ORM\Column(name="device_model", type="text", length=65535, nullable=false)
     */
    private $deviceModel;

    /**
     * @var string
     *
     * @ORM\Column(name="device_manufacturer", type="text", length=65535, nullable=false)
     */
    private $deviceManufacturer;

    /**
     * @var string
     *
     * @ORM\Column(name="browser_name", type="text", length=65535, nullable=false)
     */
    private $browserName;

    /**
     * @var string
     *
     * @ORM\Column(name="browser_version", type="text", length=65535, nullable=false)
     */
    private $browserVersion;

    /**
     * @var string
     *
     * @ORM\Column(name="os_name", type="text", length=65535, nullable=false)
     */
    private $osName;

    /**
     * @var string
     *
     * @ORM\Column(name="os_version", type="text", length=65535, nullable=false)
     */
    private $osVersion;

    /**
     * @var bool
     *
     * @ORM\Column(name="os_id", type="integer", nullable=false)
     */
    private $osId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt = 'CURRENT_TIMESTAMP';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getDeviceType(): ?string
    {
        return $this->deviceType;
    }

    public function setDeviceType(string $deviceType): self
    {
        $this->deviceType = $deviceType;

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

    public function getDeviceModel(): ?string
    {
        return $this->deviceModel;
    }

    public function setDeviceModel(string $deviceModel): self
    {
        $this->deviceModel = $deviceModel;

        return $this;
    }

    public function getDeviceManufacturer(): ?string
    {
        return $this->deviceManufacturer;
    }

    public function setDeviceManufacturer(string $deviceManufacturer): self
    {
        $this->deviceManufacturer = $deviceManufacturer;

        return $this;
    }

    public function getBrowserName(): ?string
    {
        return $this->browserName;
    }

    public function setBrowserName(string $browserName): self
    {
        $this->browserName = $browserName;

        return $this;
    }

    public function getBrowserVersion(): ?string
    {
        return $this->browserVersion;
    }

    public function setBrowserVersion(string $browserVersion): self
    {
        $this->browserVersion = $browserVersion;

        return $this;
    }

    public function getOsName(): ?string
    {
        return $this->osName;
    }

    public function setOsName(string $osName): self
    {
        $this->osName = $osName;

        return $this;
    }

    public function getOsVersion(): ?string
    {
        return $this->osVersion;
    }

    public function setOsVersion(string $osVersion): self
    {
        $this->osVersion = $osVersion;

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
