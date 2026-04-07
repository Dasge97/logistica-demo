<?php

namespace App\Modulos\Operaciones\Domain\Entity;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Operaciones\Infrastructure\Persistence\Doctrine\RepositorioReglaDisponibilidadServicio;
use App\Shared\Domain\Model\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: RepositorioReglaDisponibilidadServicio::class)]
#[ORM\Table(name: 'reglas_disponibilidad_servicio')]
#[ORM\HasLifecycleCallbacks]
class ReglaDisponibilidadServicio
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidV7 $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?NivelServicioEntrega $nivelServicioEntrega;

    #[ORM\Column]
    private float $distanciaMaximaKm;

    #[ORM\Column]
    private int $pesoMaximoGramos;

    #[ORM\Column]
    private int $volumenMaximoCm3;

    #[ORM\Column(options: ['default' => true])]
    private bool $activa = true;

    public function __construct(?NivelServicioEntrega $nivelServicioEntrega, float $distanciaMaximaKm, int $pesoMaximoGramos, int $volumenMaximoCm3)
    {
        $this->id = new UuidV7();
        $this->nivelServicioEntrega = $nivelServicioEntrega;
        $this->setDistanciaMaximaKm($distanciaMaximaKm);
        $this->setPesoMaximoGramos($pesoMaximoGramos);
        $this->setVolumenMaximoCm3($volumenMaximoCm3);
    }

    public function getId(): UuidV7
    {
        return $this->id;
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

    public function getDistanciaMaximaKm(): float
    {
        return $this->distanciaMaximaKm;
    }

    public function setDistanciaMaximaKm(float $distanciaMaximaKm): void
    {
        $this->distanciaMaximaKm = max(0, $distanciaMaximaKm);
    }

    public function getPesoMaximoGramos(): int
    {
        return $this->pesoMaximoGramos;
    }

    public function setPesoMaximoGramos(int $pesoMaximoGramos): void
    {
        $this->pesoMaximoGramos = max(0, $pesoMaximoGramos);
    }

    public function getVolumenMaximoCm3(): int
    {
        return $this->volumenMaximoCm3;
    }

    public function setVolumenMaximoCm3(int $volumenMaximoCm3): void
    {
        $this->volumenMaximoCm3 = max(0, $volumenMaximoCm3);
    }

    public function isActiva(): bool
    {
        return $this->activa;
    }

    public function activar(): void
    {
        $this->activa = true;
    }

    public function desactivar(): void
    {
        $this->activa = false;
    }

    public function setActiva(bool $activa): void
    {
        $activa ? $this->activar() : $this->desactivar();
    }

    public function admite(float $distanciaKm, int $pesoTotalGramos, int $volumenTotalCm3): bool
    {
        if (!$this->activa) {
            return false;
        }

        return $distanciaKm <= $this->distanciaMaximaKm
            && $pesoTotalGramos <= $this->pesoMaximoGramos
            && $volumenTotalCm3 <= $this->volumenMaximoCm3;
    }
}
