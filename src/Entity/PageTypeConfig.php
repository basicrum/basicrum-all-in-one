<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageTypeConfig
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
     * @ORM\Column(name="conditions_serialized", type="text", length=0, nullable=false)
     */
    private $conditionsSerialized;

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

    public function getConditionsSerialized(): ?string
    {
        return $this->conditionsSerialized;
    }

    public function setConditionsSerialized(string $conditionsSerialized): self
    {
        $this->conditionsSerialized = $conditionsSerialized;

        return $this;
    }


}
