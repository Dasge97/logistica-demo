<?php

namespace App\Shared\Infrastructure\Twig;

use App\Shared\Domain\ValueObject\Dinero;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ExtensionFormatoApp extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('dinero', [$this, 'formatearDinero']),
        ];
    }

    public function formatearDinero(Dinero|int $valor, string $moneda = 'EUR'): string
    {
        if ($valor instanceof Dinero) {
            return $valor->format();
        }

        return (new Dinero($valor, $moneda))->format();
    }
}
