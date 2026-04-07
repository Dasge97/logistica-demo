<?php

namespace App\Modulos\Pedidos\Domain\Entity;

use App\Modulos\Pedidos\Infrastructure\Persistence\Doctrine\RepositorioLineaPedido;
use App\Shared\Domain\Model\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: RepositorioLineaPedido::class)]
#[ORM\Table(name: 'lineas_pedido')]
#[ORM\HasLifecycleCallbacks]
class LineaPedido
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidV7 $id;

    #[ORM\ManyToOne(inversedBy: 'lineas')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Pedido $pedido;

    #[ORM\Column(length: 180)]
    private string $descripcion;

    #[ORM\Column]
    private int $cantidad;

    #[ORM\Column]
    private int $pesoUnitarioGramos;

    #[ORM\Column]
    private int $volumenUnitarioCm3;

    public function __construct(Pedido $pedido, string $descripcion, int $cantidad, int $pesoUnitarioGramos, int $volumenUnitarioCm3)
    {
        $this->id = new UuidV7();
        $this->pedido = $pedido;
        $this->descripcion = trim($descripcion);
        $this->cantidad = max(1, $cantidad);
        $this->pesoUnitarioGramos = max(0, $pesoUnitarioGramos);
        $this->volumenUnitarioCm3 = max(0, $volumenUnitarioCm3);
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    public function getPedido(): Pedido
    {
        return $this->pedido;
    }

    public function asignarPedido(Pedido $pedido): void
    {
        $this->pedido = $pedido;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function cambiarDescripcion(string $descripcion): void
    {
        $this->descripcion = trim($descripcion);
    }

    public function setDescripcion(string $descripcion): void
    {
        $this->cambiarDescripcion($descripcion);
    }

    public function getCantidad(): int
    {
        return $this->cantidad;
    }

    public function cambiarCantidad(int $cantidad): void
    {
        $this->cantidad = max(1, $cantidad);
    }

    public function setCantidad(int $cantidad): void
    {
        $this->cambiarCantidad($cantidad);
    }

    public function getPesoUnitarioGramos(): int
    {
        return $this->pesoUnitarioGramos;
    }

    public function cambiarPesoUnitarioGramos(int $pesoUnitarioGramos): void
    {
        $this->pesoUnitarioGramos = max(0, $pesoUnitarioGramos);
    }

    public function setPesoUnitarioGramos(int $pesoUnitarioGramos): void
    {
        $this->cambiarPesoUnitarioGramos($pesoUnitarioGramos);
    }

    public function getVolumenUnitarioCm3(): int
    {
        return $this->volumenUnitarioCm3;
    }

    public function cambiarVolumenUnitarioCm3(int $volumenUnitarioCm3): void
    {
        $this->volumenUnitarioCm3 = max(0, $volumenUnitarioCm3);
    }

    public function setVolumenUnitarioCm3(int $volumenUnitarioCm3): void
    {
        $this->cambiarVolumenUnitarioCm3($volumenUnitarioCm3);
    }

    public function getSubtotalPesoGramos(): int
    {
        return $this->cantidad * $this->pesoUnitarioGramos;
    }

    public function getSubtotalVolumenCm3(): int
    {
        return $this->cantidad * $this->volumenUnitarioCm3;
    }
}
