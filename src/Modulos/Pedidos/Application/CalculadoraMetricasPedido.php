<?php

namespace App\Modulos\Pedidos\Application;

use App\Modulos\Pedidos\Domain\Entity\Pedido;

final class CalculadoraMetricasPedido
{
    public function recalcular(Pedido $pedido): void
    {
        $pesoTotalGramos = 0;
        $volumenTotalCm3 = 0;

        foreach ($pedido->getLineas() as $lineaPedido) {
            $pesoTotalGramos += $lineaPedido->getSubtotalPesoGramos();
            $volumenTotalCm3 += $lineaPedido->getSubtotalVolumenCm3();
        }

        $pedido->actualizarMetricas($pesoTotalGramos, $volumenTotalCm3);
    }
}
