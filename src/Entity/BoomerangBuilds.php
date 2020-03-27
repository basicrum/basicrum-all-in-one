<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoomerangBuilds.
 *
 * @ORM\Table(name="boomerang_builds")
 * @ORM\Entity
 */
class BoomerangBuilds
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
     * @ORM\Column(name="build_params", type="text", length=65535, nullable=false)
     */
    private $buildParams;

    /**
     * @var string
     *
     * @ORM\Column(name="build_result", type="text", length=65535, nullable=false)
     */
    private $buildResult;

    /**
     * @var string
     *
     * @ORM\Column(name="boomerang_version", type="string", length=128, nullable=false)
     */
    private $boomerangVersion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuildParams(): ?string
    {
        return $this->buildParams;
    }

    public function setBuildParams(string $buildParams): self
    {
        $this->buildParams = $buildParams;

        return $this;
    }

    public function getBuildResult(): ?string
    {
        return $this->buildResult;
    }

    public function setBuildResult(string $buildResult): self
    {
        $this->buildResult = $buildResult;

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
