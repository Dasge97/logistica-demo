<?php

namespace App\Shared\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
final class Dinero
{
    #[ORM\Column(name: 'importe_centimos')]
    private int $importeCentimos;

    #[ORM\Column(length: 3)]
    private string $moneda;

    public function __construct(int $importeCentimos, string $moneda = 'EUR')
    {
        if ($importeCentimos < 0) {
            throw new InvalidArgumentException('El importe no puede ser negativo.');
        }

        $moneda = strtoupper(trim($moneda));
        if (3 !== strlen($moneda)) {
            throw new InvalidArgumentException('La moneda debe usar formato ISO-4217.');
        }

        $this->importeCentimos = $importeCentimos;
        $this->moneda = $moneda;
    }

    public function getImporteCentimos(): int
    {
        return $this->importeCentimos;
    }

    public function getMoneda(): string
    {
        return $this->moneda;
    }

    public function format(): string
    {
        return number_format($this->importeCentimos / 100, 2, ',', '.') . ' ' . $this->moneda;
    }
}
