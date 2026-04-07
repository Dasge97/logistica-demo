<?php

namespace App\Modulos\Catalogos\Domain\Entity;

use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioTipoCliente;
use App\Shared\Domain\Model\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: RepositorioTipoCliente::class)]
#[ORM\Table(name: 'tipos_cliente')]
#[ORM\UniqueConstraint(name: 'uniq_tipo_cliente_codigo', columns: ['codigo'])]
#[ORM\HasLifecycleCallbacks]
class TipoCliente
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidV7 $id;

    #[ORM\Column(length: 80)]
    private string $nombre;

    #[ORM\Column(length: 40)]
    private string $codigo;

    #[ORM\Column(options: ['default' => true])]
    private bool $activo = true;

    public function __construct(string $nombre, string $codigo)
    {
        $this->id = new UuidV7();
        $this->setNombre($nombre);
        $this->setCodigo($codigo);
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function renombrar(string $nombre): void
    {
        $this->nombre = trim($nombre);
    }

    public function setNombre(string $nombre): void
    {
        $this->renombrar($nombre);
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function cambiarCodigo(string $codigo): void
    {
        $this->codigo = strtoupper(trim($codigo));
    }

    public function setCodigo(string $codigo): void
    {
        $this->cambiarCodigo($codigo);
    }

    public function isActivo(): bool
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

    public function setActivo(bool $activo): void
    {
        $activo ? $this->activar() : $this->desactivar();
    }

    public function __toString(): string
    {
        return $this->nombre;
    }
}
