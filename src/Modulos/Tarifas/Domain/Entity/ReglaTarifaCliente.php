<?php

namespace App\Modulos\Tarifas\Domain\Entity;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Catalogos\Domain\Entity\TipoCliente;
use App\Modulos\Tarifas\Infrastructure\Persistence\Doctrine\RepositorioReglaTarifaCliente;
use App\Shared\Domain\Model\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: RepositorioReglaTarifaCliente::class)]
#[ORM\Table(name: 'reglas_tarifa_cliente')]
#[ORM\HasLifecycleCallbacks]
class ReglaTarifaCliente
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidV7 $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?TipoCliente $tipoCliente;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?NivelServicioEntrega $nivelServicioEntrega;

    #[ORM\Column]
    private float $distanciaDesdeKm;

    #[ORM\Column]
    private float $distanciaHastaKm;

    #[ORM\Column]
    private int $precioClienteCentimos;

    #[ORM\Column(options: ['default' => true])]
    private bool $activa = true;

    public function __construct(?TipoCliente $tipoCliente, ?NivelServicioEntrega $nivelServicioEntrega, float $distanciaDesdeKm, float $distanciaHastaKm, int $precioClienteCentimos)
    {
        $this->id = new UuidV7();
        $this->tipoCliente = $tipoCliente;
        $this->nivelServicioEntrega = $nivelServicioEntrega;
        $this->setDistanciaDesdeKm($distanciaDesdeKm);
        $this->setDistanciaHastaKm($distanciaHastaKm);
        $this->setPrecioClienteCentimos($precioClienteCentimos);
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    public function getTipoCliente(): ?TipoCliente
    {
        return $this->tipoCliente;
    }

    public function cambiarTipoCliente(TipoCliente $tipoCliente): void
    {
        $this->tipoCliente = $tipoCliente;
    }

    public function setTipoCliente(TipoCliente $tipoCliente): void
    {
        $this->cambiarTipoCliente($tipoCliente);
    }

    public function getNivelServicioEntrega(): ?NivelServicioEntrega
    {
        return $this->nivelServicioEntrega;
    }

    public function cambiarNivelServicioEntrega(NivelServicioEntrega $nivelServicioEntrega): void
    {
        $this->nivelServicioEntrega = $nivelServicioEntrega;
    }

    public function setNivelServicioEntrega(NivelServicioEntrega $nivelServicioEntrega): void
    {
        $this->cambiarNivelServicioEntrega($nivelServicioEntrega);
    }

    public function getDistanciaDesdeKm(): float
    {
        return $this->distanciaDesdeKm;
    }

    public function setDistanciaDesdeKm(float $distanciaDesdeKm): void
    {
        $this->distanciaDesdeKm = max(0, $distanciaDesdeKm);
    }

    public function getDistanciaHastaKm(): float
    {
        return $this->distanciaHastaKm;
    }

    public function setDistanciaHastaKm(float $distanciaHastaKm): void
    {
        $this->distanciaHastaKm = max($this->distanciaDesdeKm, $distanciaHastaKm);
    }

    public function getPrecioClienteCentimos(): int
    {
        return $this->precioClienteCentimos;
    }

    public function setPrecioClienteCentimos(int $precioClienteCentimos): void
    {
        $this->precioClienteCentimos = max(0, $precioClienteCentimos);
    }

    public function isActiva(): bool
    {
        return $this->activa;
    }

    public function setActiva(bool $activa): void
    {
        $this->activa = $activa;
    }

    public function aplicaA(TipoCliente $tipoCliente, NivelServicioEntrega $nivelServicioEntrega, float $distanciaKm): bool
    {
        return $this->activa
            && $this->tipoCliente === $tipoCliente
            && $this->nivelServicioEntrega === $nivelServicioEntrega
            && $distanciaKm >= $this->distanciaDesdeKm
            && $distanciaKm <= $this->distanciaHastaKm;
    }
}
