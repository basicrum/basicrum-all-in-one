<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PageTypeConfigRepository")
 */
class PageTypeConfig
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $page_type_name;

    /**
     * @ORM\Column(type="text")
     */
    private $conditions_serialized;

    public function getId()
    {
        return $this->id;
    }

    public function getPageTypeName(): ?string
    {
        return $this->page_type_name;
    }

    public function setPageTypeName(string $page_type_name): self
    {
        $this->page_type_name = $page_type_name;

        return $this;
    }

    public function getConditionsSerialized(): ?string
    {
        return $this->conditions_serialized;
    }

    public function setConditionsSerialized(string $conditions_serialized): self
    {
        $this->conditions_serialized = $conditions_serialized;

        return $this;
    }
}
