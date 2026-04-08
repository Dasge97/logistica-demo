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
    private float $distanciaMinimaKm;

    #[ORM\Column]
    private float $distanciaMaximaKm;

    #[ORM\Column]
    private int $pesoMinimoGramos;

    #[ORM\Column]
    private int $pesoMaximoGramos;

    #[ORM\Column]
    private int $volumenMinimoCm3;

    #[ORM\Column]
    private int $volumenMaximoCm3;

    #[ORM\Column]
    private int $precioBaseCentimos;

    #[ORM\Column]
    private float $distanciaIncluidaKm;

    #[ORM\Column]
    private int $precioKmExtraCentimos;

    #[ORM\Column(options: ['default' => true])]
    private bool $activa = true;

    public function __construct(
        ?TipoVehiculo $tipoVehiculo,
        ?NivelServicioEntrega $nivelServicioEntrega,
        float $distanciaMinimaKm,
        float $distanciaMaximaKm,
        int $pesoMinimoGramos,
        int $pesoMaximoGramos,
        int $volumenMinimoCm3,
        int $volumenMaximoCm3,
        int $precioBaseCentimos,
        float $distanciaIncluidaKm,
        int $precioKmExtraCentimos,
    )
    {
        $this->id = new UuidV7();
        $this->tipoVehiculo = $tipoVehiculo;
        $this->nivelServicioEntrega = $nivelServicioEntrega;
        $this->setDistanciaMinimaKm($distanciaMinimaKm);
        $this->setDistanciaMaximaKm($distanciaMaximaKm);
        $this->setPesoMinimoGramos($pesoMinimoGramos);
        $this->setPesoMaximoGramos($pesoMaximoGramos);
        $this->setVolumenMinimoCm3($volumenMinimoCm3);
        $this->setVolumenMaximoCm3($volumenMaximoCm3);
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

    public function getDistanciaMinimaKm(): float
    {
        return $this->distanciaMinimaKm;
    }

    public function setDistanciaMinimaKm(float $distanciaMinimaKm): void
    {
        $this->distanciaMinimaKm = max(0, $distanciaMinimaKm);
    }

    public function getDistanciaMaximaKm(): float
    {
        return $this->distanciaMaximaKm;
    }

    public function setDistanciaMaximaKm(float $distanciaMaximaKm): void
    {
        $this->distanciaMaximaKm = max($this->distanciaMinimaKm, $distanciaMaximaKm);
    }

    public function getPesoMinimoGramos(): int
    {
        return $this->pesoMinimoGramos;
    }

    public function setPesoMinimoGramos(int $pesoMinimoGramos): void
    {
        $this->pesoMinimoGramos = max(0, $pesoMinimoGramos);
    }

    public function getPesoMaximoGramos(): int
    {
        return $this->pesoMaximoGramos;
    }

    public function setPesoMaximoGramos(int $pesoMaximoGramos): void
    {
        $this->pesoMaximoGramos = max($this->pesoMinimoGramos, $pesoMaximoGramos);
    }

    public function getVolumenMinimoCm3(): int
    {
        return $this->volumenMinimoCm3;
    }

    public function setVolumenMinimoCm3(int $volumenMinimoCm3): void
    {
        $this->volumenMinimoCm3 = max(0, $volumenMinimoCm3);
    }

    public function getVolumenMaximoCm3(): int
    {
        return $this->volumenMaximoCm3;
    }

    public function setVolumenMaximoCm3(int $volumenMaximoCm3): void
    {
        $this->volumenMaximoCm3 = max($this->volumenMinimoCm3, $volumenMaximoCm3);
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

    public function aplicaA(float $distanciaKm, int $pesoTotalGramos, int $volumenTotalCm3): bool
    {
        return $this->activa
            && $distanciaKm >= $this->distanciaMinimaKm
            && $distanciaKm <= $this->distanciaMaximaKm
            && $pesoTotalGramos >= $this->pesoMinimoGramos
            && $pesoTotalGramos <= $this->pesoMaximoGramos
            && $volumenTotalCm3 >= $this->volumenMinimoCm3
            && $volumenTotalCm3 <= $this->volumenMaximoCm3;
    }

    public function calcularCosteCentimos(float $distanciaKm): int
    {
        $kmExtra = max(0, $distanciaKm - $this->distanciaIncluidaKm);

        return $this->precioBaseCentimos + (int) round($kmExtra * $this->precioKmExtraCentimos);
    }
}
