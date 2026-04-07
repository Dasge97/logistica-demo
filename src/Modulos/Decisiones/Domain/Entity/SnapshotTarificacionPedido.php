<?php

namespace App\Modulos\Decisiones\Domain\Entity;

use App\Modulos\Decisiones\Infrastructure\Persistence\Doctrine\RepositorioSnapshotTarificacionPedido;
use App\Modulos\Pedidos\Domain\Entity\Pedido;
use App\Shared\Domain\Model\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: RepositorioSnapshotTarificacionPedido::class)]
#[ORM\Table(name: 'snapshots_tarificacion_pedido')]
#[ORM\UniqueConstraint(name: 'uniq_snapshot_pedido', columns: ['pedido_id'])]
#[ORM\HasLifecycleCallbacks]
class SnapshotTarificacionPedido
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidV7 $id;

    #[ORM\OneToOne(inversedBy: 'snapshotTarificacion', targetEntity: Pedido::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Pedido $pedido;

    #[ORM\Column(length: 80)]
    private string $nombreServicio;

    #[ORM\Column(length: 80)]
    private string $nombreVehiculo;

    #[ORM\Column]
    private float $distanciaKm;

    #[ORM\Column]
    private int $pesoTotalGramos;

    #[ORM\Column]
    private int $volumenTotalCm3;

    #[ORM\Column]
    private int $precioClienteCentimos;

    #[ORM\Column]
    private int $costeLogisticoCentimos;

    #[ORM\Column]
    private int $margenCentimos;

    #[ORM\Column(type: 'json')]
    private array $explicacionJson;

    public function __construct(
        Pedido $pedido,
        string $nombreServicio,
        string $nombreVehiculo,
        float $distanciaKm,
        int $pesoTotalGramos,
        int $volumenTotalCm3,
        int $precioClienteCentimos,
        int $costeLogisticoCentimos,
        int $margenCentimos,
        array $explicacionJson,
    ) {
        $this->id = new UuidV7();
        $this->pedido = $pedido;
        $this->nombreServicio = trim($nombreServicio);
        $this->nombreVehiculo = trim($nombreVehiculo);
        $this->distanciaKm = $distanciaKm;
        $this->pesoTotalGramos = $pesoTotalGramos;
        $this->volumenTotalCm3 = $volumenTotalCm3;
        $this->precioClienteCentimos = $precioClienteCentimos;
        $this->costeLogisticoCentimos = $costeLogisticoCentimos;
        $this->margenCentimos = $margenCentimos;
        $this->explicacionJson = $explicacionJson;
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    public function getPedido(): Pedido
    {
        return $this->pedido;
    }

    public function getNombreServicio(): string
    {
        return $this->nombreServicio;
    }

    public function getNombreVehiculo(): string
    {
        return $this->nombreVehiculo;
    }

    public function getDistanciaKm(): float
    {
        return $this->distanciaKm;
    }

    public function getPesoTotalGramos(): int
    {
        return $this->pesoTotalGramos;
    }

    public function getVolumenTotalCm3(): int
    {
        return $this->volumenTotalCm3;
    }

    public function getPrecioClienteCentimos(): int
    {
        return $this->precioClienteCentimos;
    }

    public function getCosteLogisticoCentimos(): int
    {
        return $this->costeLogisticoCentimos;
    }

    public function getMargenCentimos(): int
    {
        return $this->margenCentimos;
    }

    public function getExplicacionJson(): array
    {
        return $this->explicacionJson;
    }
}
