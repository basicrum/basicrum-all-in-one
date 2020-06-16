<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RumDataFlatQueryParams.
 *
 * @ORM\Table(name="rum_data_flat_query_params")
 * @ORM\Entity
 */
class RumDataFlatQueryParams
{
    /**
     * @var int
     *
     * @ORM\Column(name="rum_data_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $rumDataId;

    /**
     * @var string
     *
     * @ORM\Column(name="query_params", type="text", length=65535, nullable=false)
     */
    private $queryParams;

    public function getRumDataId(): ?int
    {
        return $this->rumDataId;
    }

    public function setRumDataId(int $rumDataId): self
    {
        $this->rumDataId = $rumDataId;

        return $this;
    }

    public function getQueryParams(): ?string
    {
        return $this->queryParams;
    }

    public function setQueryParams(string $queryParams): self
    {
        $this->queryParams = $queryParams;

        return $this;
    }
}
