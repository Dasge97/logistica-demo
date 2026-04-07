<?php

namespace App\Core\Usuario\Infrastructure\Persistence\Doctrine;

use App\Core\Usuario\Domain\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Usuario>
 */
final class RepositorioUsuario extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    public function buscarPorEmail(string $email): ?Usuario
    {
        return $this->findOneBy(['email' => mb_strtolower(trim($email))]);
    }
}
