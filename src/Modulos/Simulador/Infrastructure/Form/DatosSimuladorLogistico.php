<?php

namespace App\Modulos\Simulador\Infrastructure\Form;

use App\Modulos\Catalogos\Domain\Entity\TipoCliente;

final class DatosSimuladorLogistico
{
    public ?TipoCliente $tipoCliente = null;

    public float $distanciaKm = 0;

    public int $pesoTotalGramos = 0;

    public int $volumenTotalCm3 = 0;
}
