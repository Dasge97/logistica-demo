<?php

namespace App\Modulos\Pedidos\Domain\Entity;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Catalogos\Domain\Entity\TipoCliente;
use App\Modulos\Catalogos\Domain\Entity\TipoVehiculo;
use App\Modulos\Decisiones\Domain\Entity\SnapshotTarificacionPedido;
use App\Modulos\Pedidos\Domain\Enum\EstadoPedido;
use App\Modulos\Pedidos\Infrastructure\Persistence\Doctrine\RepositorioPedido;
use App\Shared\Domain\Model\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: RepositorioPedido::class)]
#[ORM\Table(name: 'pedidos')]
#[ORM\UniqueConstraint(name: 'uniq_pedido_referencia', columns: ['referencia'])]
#[ORM\HasLifecycleCallbacks]
class Pedido
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidV7 $id;

    #[ORM\Column(length: 32)]
    private string $referencia;

    #[ORM\Column(enumType: EstadoPedido::class)]
    private EstadoPedido $estado;

    #[ORM\Column(length: 140)]
    private string $nombreCliente;

    #[ORM\Column(length: 40)]
    private string $telefonoCliente;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?TipoCliente $tipoCliente;

    #[ORM\Column]
    private float $distanciaKm = 0;

    #[ORM\Column]
    private int $pesoTotalGramos = 0;

    #[ORM\Column]
    private int $volumenTotalCm3 = 0;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?NivelServicioEntrega $servicioElegido = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?TipoVehiculo $vehiculoElegido = null;

    #[ORM\Column(nullable: true)]
    private ?int $precioClienteCentimos = null;

    #[ORM\Column(nullable: true)]
    private ?int $costeLogisticoCentimos = null;

    #[ORM\Column(nullable: true)]
    private ?int $margenCentimos = null;

    #[ORM\OneToOne(mappedBy: 'pedido', targetEntity: SnapshotTarificacionPedido::class, cascade: ['persist'])]
    private ?SnapshotTarificacionPedido $snapshotTarificacion = null;

    /** @var Collection<int, LineaPedido> */
    #[ORM\OneToMany(mappedBy: 'pedido', targetEntity: LineaPedido::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $lineas;

    public function __construct(string $referencia, string $nombreCliente, string $telefonoCliente, ?TipoCliente $tipoCliente = null)
    {
        $this->id = new UuidV7();
        $this->referencia = strtoupper(trim($referencia));
        $this->nombreCliente = trim($nombreCliente);
        $this->telefonoCliente = trim($telefonoCliente);
        $this->tipoCliente = $tipoCliente;
        $this->estado = EstadoPedido::BORRADOR;
        $this->lineas = new ArrayCollection();
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    public function getReferencia(): string
    {
        return $this->referencia;
    }

    public function cambiarReferencia(string $referencia): void
    {
        $this->referencia = strtoupper(trim($referencia));
    }

    public function setReferencia(string $referencia): void
    {
        $this->cambiarReferencia($referencia);
    }

    public function getEstado(): EstadoPedido
    {
        return $this->estado;
    }

    public function confirmar(): void
    {
        $this->estado = EstadoPedido::CONFIRMADO;
    }

    public function cancelar(): void
    {
        $this->estado = EstadoPedido::CANCELADO;
    }

    public function volverABorrador(): void
    {
        $this->estado = EstadoPedido::BORRADOR;
    }

    public function getNombreCliente(): string
    {
        return $this->nombreCliente;
    }

    public function cambiarNombreCliente(string $nombreCliente): void
    {
        $this->nombreCliente = trim($nombreCliente);
    }

    public function setNombreCliente(string $nombreCliente): void
    {
        $this->cambiarNombreCliente($nombreCliente);
    }

    public function getTelefonoCliente(): string
    {
        return $this->telefonoCliente;
    }

    public function cambiarTelefonoCliente(string $telefonoCliente): void
    {
        $this->telefonoCliente = trim($telefonoCliente);
    }

    public function setTelefonoCliente(string $telefonoCliente): void
    {
        $this->cambiarTelefonoCliente($telefonoCliente);
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

    public function getDistanciaKm(): float
    {
        return $this->distanciaKm;
    }

    public function cambiarDistanciaKm(float $distanciaKm): void
    {
        $this->distanciaKm = max(0, $distanciaKm);
    }

    public function setDistanciaKm(float $distanciaKm): void
    {
        $this->cambiarDistanciaKm($distanciaKm);
    }

    public function getPesoTotalGramos(): int
    {
        return $this->pesoTotalGramos;
    }

    public function getVolumenTotalCm3(): int
    {
        return $this->volumenTotalCm3;
    }

    public function actualizarMetricas(int $pesoTotalGramos, int $volumenTotalCm3): void
    {
        $this->pesoTotalGramos = max(0, $pesoTotalGramos);
        $this->volumenTotalCm3 = max(0, $volumenTotalCm3);
    }

    public function getServicioElegido(): ?NivelServicioEntrega
    {
        return $this->servicioElegido;
    }

    public function getVehiculoElegido(): ?TipoVehiculo
    {
        return $this->vehiculoElegido;
    }

    public function getPrecioClienteCentimos(): ?int
    {
        return $this->precioClienteCentimos;
    }

    public function getCosteLogisticoCentimos(): ?int
    {
        return $this->costeLogisticoCentimos;
    }

    public function getMargenCentimos(): ?int
    {
        return $this->margenCentimos;
    }

    public function getSnapshotTarificacion(): ?SnapshotTarificacionPedido
    {
        return $this->snapshotTarificacion;
    }

    public function asignarDecisionEntrega(
        NivelServicioEntrega $nivelServicioEntrega,
        TipoVehiculo $tipoVehiculo,
        int $precioClienteCentimos,
        int $costeLogisticoCentimos,
        int $margenCentimos,
    ): void {
        $this->servicioElegido = $nivelServicioEntrega;
        $this->vehiculoElegido = $tipoVehiculo;
        $this->precioClienteCentimos = max(0, $precioClienteCentimos);
        $this->costeLogisticoCentimos = max(0, $costeLogisticoCentimos);
        $this->margenCentimos = $margenCentimos;
    }

    public function asignarSnapshotTarificacion(SnapshotTarificacionPedido $snapshotTarificacionPedido): void
    {
        $this->snapshotTarificacion = $snapshotTarificacionPedido;
    }

    /** @return Collection<int, LineaPedido> */
    public function getLineas(): Collection
    {
        return $this->lineas;
    }

    public function agregarLinea(LineaPedido $lineaPedido): void
    {
        if ($this->lineas->contains($lineaPedido)) {
            return;
        }

        $this->lineas->add($lineaPedido);
        $lineaPedido->asignarPedido($this);
    }

    public function quitarLinea(LineaPedido $lineaPedido): void
    {
        if (!$this->lineas->contains($lineaPedido)) {
            return;
        }

        $this->lineas->removeElement($lineaPedido);
    }
}
