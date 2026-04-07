<?php

namespace App\Modulos\Tarifas\Domain\Entity;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Catalogos\Domain\Entity\TipoVehiculo;
use App\Modulos\Tarifas\Infrastructure\Persistence\Doctrine\RepositorioReglaTarifaTransportista;
use App\Shared\Domain\Model\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: RepositorioReglaTarifaTransportista::class)]
#[ORM\Table(name: 'reglas_tarifa_transportista')]
#[ORM\HasLifecycleCallbacks]
class ReglaTarifaTransportista
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidV7 $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?TipoVehiculo $tipoVehiculo;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?NivelServicioEntrega $nivelServicioEntrega;

    #[ORM\Column]
    private int $precioBaseCentimos;

    #[ORM\Column]
    private float $distanciaIncluidaKm;

    #[ORM\Column]
    private int $precioKmExtraCentimos;

    #[ORM\Column(options: ['default' => true])]
    private bool $activa = true;

    public function __construct(?TipoVehiculo $tipoVehiculo, ?NivelServicioEntrega $nivelServicioEntrega, int $precioBaseCentimos, float $distanciaIncluidaKm, int $precioKmExtraCentimos)
    {
        $this->id = new UuidV7();
        $this->tipoVehiculo = $tipoVehiculo;
        $this->nivelServicioEntrega = $nivelServicioEntrega;
        $this->setPrecioBaseCentimos($precioBaseCentimos);
        $this->setDistanciaIncluidaKm($distanciaIncluidaKm);
        $this->setPrecioKmExtraCentimos($precioKmExtraCentimos);
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    public function getTipoVehiculo(): ?TipoVehiculo
    {
        return $this->tipoVehiculo;
    }

    public function cambiarTipoVehiculo(TipoVehiculo $tipoVehiculo): void
    {
        $this->tipoVehiculo = $tipoVehiculo;
    }

    public function setTipoVehiculo(TipoVehiculo $tipoVehiculo): void
    {
        $this->cambiarTipoVehiculo($tipoVehiculo);
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

    public function getPrecioBaseCentimos(): int
    {
        return $this->precioBaseCentimos;
    }

    public function setPrecioBaseCentimos(int $precioBaseCentimos): void
    {
        $this->precioBaseCentimos = max(0, $precioBaseCentimos);
    }

    public function getDistanciaIncluidaKm(): float
    {
        return $this->distanciaIncluidaKm;
    }

    public function setDistanciaIncluidaKm(float $distanciaIncluidaKm): void
    {
        $this->distanciaIncluidaKm = max(0, $distanciaIncluidaKm);
    }

    public function getPrecioKmExtraCentimos(): int
    {
        return $this->precioKmExtraCentimos;
    }

    public function setPrecioKmExtraCentimos(int $precioKmExtraCentimos): void
    {
        $this->precioKmExtraCentimos = max(0, $precioKmExtraCentimos);
    }

    public function isActiva(): bool
    {
        return $this->activa;
    }

    public function setActiva(bool $activa): void
    {
        $this->activa = $activa;
    }

    public function calcularCosteCentimos(float $distanciaKm): int
    {
        $kmExtra = max(0, $distanciaKm - $this->distanciaIncluidaKm);

        return $this->precioBaseCentimos + (int) round($kmExtra * $this->precioKmExtraCentimos);
    }
}
