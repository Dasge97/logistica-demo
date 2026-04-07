<?php

namespace App\Modulos\Pedidos\Domain\Enum;

enum EstadoPedido: string
{
    case BORRADOR = 'borrador';
    case CONFIRMADO = 'confirmado';
    case CANCELADO = 'cancelado';
}
