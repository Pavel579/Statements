<?php

namespace App\Entity\DTO;

use Prugala\RequestDto\Dto\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

class StatementDto implements RequestDtoInterface
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    private string $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
