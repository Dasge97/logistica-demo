<?php

namespace App\Modulos\Operaciones\Application;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Operaciones\Infrastructure\Persistence\Doctrine\RepositorioReglaDisponibilidadServicio;

final class ServicioDisponibilidadEntrega
{
    public function __construct(private readonly RepositorioReglaDisponibilidadServicio $repositorioReglaDisponibilidadServicio)
    {
    }

    public function esViable(NivelServicioEntrega $nivelServicioEntrega, float $distanciaKm, int $pesoTotalGramos, int $volumenTotalCm3): bool
    {
        $regla = $this->repositorioReglaDisponibilidadServicio->buscarActivaPorNivel($nivelServicioEntrega);
        if (null === $regla) {
            return false;
        }

        return $regla->admite($distanciaKm, $pesoTotalGramos, $volumenTotalCm3);
    }
}
