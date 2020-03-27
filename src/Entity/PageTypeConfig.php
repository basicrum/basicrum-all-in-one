<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageTypeConfig.
 *
 * @ORM\Table(name="page_type_config")
 * @ORM\Entity
 */
class PageTypeConfig
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="page_type_name", type="string", length=255, nullable=false)
     */
    private $pageTypeName;

    /**
     * @var string
     *
     * @ORM\Column(name="condition_value", type="text", length=65535, nullable=false)
     */
    private $conditionValue;

    /**
     * @var string
     *
     * @ORM\Column(name="condition_term", type="text", length=65535, nullable=false)
     */
    private $conditionTerm;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPageTypeName(): ?string
    {
        return $this->pageTypeName;
    }

    public function setPageTypeName(string $pageTypeName): self
    {
        $this->pageTypeName = $pageTypeName;

        return $this;
    }

    public function getConditionValue(): ?string
    {
        return $this->conditionValue;
    }

    public function setConditionValue(string $conditionValue): self
    {
        $this->conditionValue = $conditionValue;

        return $this;
    }

    public function getConditionTerm(): ?string
    {
        return $this->conditionTerm;
    }

    public function setConditionTerm(string $conditionTerm): self
    {
        $this->conditionTerm = $conditionTerm;

        return $this;
    }
}
