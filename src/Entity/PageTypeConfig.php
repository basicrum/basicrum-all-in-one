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

    public function getConditionsSerialized(): ?array
    {
        /** @todo: Check if we need to json_decode every time */
        return empty($this->conditions_serialized) ?
            ['page_type_rule_value' => '', 'page_type_rule_condition' => ''] :
            json_decode($this->conditions_serialized, true);
    }

    public function setConditionsSerialized(array $conditions_serialized): self
    {
        $this->conditions_serialized = json_encode($conditions_serialized);

        return $this;
    }
}
