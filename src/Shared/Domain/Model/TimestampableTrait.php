<?php

namespace App\Shared\Domain\Model;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

trait TimestampableTrait
{
    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function inicializarTimestamps(): void
    {
        $ahora = new DateTimeImmutable();
        $this->createdAt = $ahora;
        $this->updatedAt = $ahora;
    }

    #[ORM\PreUpdate]
    public function actualizarTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
