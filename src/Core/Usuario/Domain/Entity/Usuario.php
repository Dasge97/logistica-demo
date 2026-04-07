<?php

namespace App\Core\Usuario\Domain\Entity;

use App\Core\Usuario\Domain\Enum\RolUsuario;
use App\Core\Usuario\Infrastructure\Persistence\Doctrine\RepositorioUsuario;
use App\Shared\Domain\Model\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: RepositorioUsuario::class)]
#[ORM\Table(name: 'usuarios')]
#[ORM\UniqueConstraint(name: 'uniq_usuario_email', columns: ['email'])]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'])]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface, PasswordHasherAwareInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidV7 $id;

    #[ORM\Column(length: 180)]
    private string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    private string $password;

    #[ORM\Column(options: ['default' => true])]
    private bool $activo = true;

    #[ORM\Column(length: 32)]
    private string $nombreCompleto;

    public function __construct(string $email, string $nombreCompleto, array $roles = [RolUsuario::ADMIN->value])
    {
        $this->id = new UuidV7();
        $this->email = mb_strtolower(trim($email));
        $this->nombreCompleto = trim($nombreCompleto);
        $this->roles = $this->normalizarRoles($roles);
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function cambiarEmail(string $email): void
    {
        $this->email = mb_strtolower(trim($email));
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = RolUsuario::ADMIN->value;

        return array_values(array_unique($roles));
    }

    public function asignarRoles(array $roles): void
    {
        $this->roles = $this->normalizarRoles($roles);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function establecerPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPasswordHasherName(): ?string
    {
        return null;
    }

    public function getNombreCompleto(): string
    {
        return $this->nombreCompleto;
    }

    public function renombrar(string $nombreCompleto): void
    {
        $this->nombreCompleto = trim($nombreCompleto);
    }

    public function estaActivo(): bool
    {
        return $this->activo;
    }

    public function activar(): void
    {
        $this->activo = true;
    }

    public function desactivar(): void
    {
        $this->activo = false;
    }

    public function eraseCredentials(): void
    {
    }

    private function normalizarRoles(array $roles): array
    {
        return array_values(array_unique(array_map(static fn (mixed $rol): string => (string) $rol, $roles)));
    }
}
