<?php

namespace App\Modulos\Operaciones\Infrastructure\Persistence\Doctrine;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Operaciones\Domain\Entity\ReglaDisponibilidadServicio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReglaDisponibilidadServicio>
 */
final class RepositorioReglaDisponibilidadServicio extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReglaDisponibilidadServicio::class);
    }

    public function buscarActivaPorNivel(NivelServicioEntrega $nivelServicioEntrega): ?ReglaDisponibilidadServicio
    {
        return $this->findOneBy([
            'nivelServicioEntrega' => $nivelServicioEntrega,
            'activa' => true,
        ]);
    }
}
