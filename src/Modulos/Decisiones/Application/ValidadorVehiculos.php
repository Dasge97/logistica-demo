<?php

namespace App\Modulos\Decisiones\Application;

use App\Modulos\Catalogos\Domain\Entity\TipoVehiculo;

final class ValidadorVehiculos
{
    public function esCompatible(TipoVehiculo $tipoVehiculo, int $pesoTotalGramos, int $volumenTotalCm3): bool
    {
        if (!$tipoVehiculo->isActivo()) {
            return false;
        }

        return $pesoTotalGramos <= $tipoVehiculo->getPesoMaximoGramos()
            && $volumenTotalCm3 <= $tipoVehiculo->getVolumenMaximoCm3();
    }
}
