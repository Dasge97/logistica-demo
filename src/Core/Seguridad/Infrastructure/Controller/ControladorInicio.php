<?php

namespace App\Core\Seguridad\Infrastructure\Controller;

use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioNivelServicioEntrega;
use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioTipoCliente;
use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioTipoVehiculo;
use App\Modulos\Decisiones\Infrastructure\Persistence\Doctrine\RepositorioSnapshotTarificacionPedido;
use App\Modulos\Pedidos\Infrastructure\Persistence\Doctrine\RepositorioPedido;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ControladorInicio extends AbstractController
{
    #[Route('/', name: 'app_inicio', methods: ['GET'])]
    public function __invoke(
        RepositorioPedido $repositorioPedido,
        RepositorioSnapshotTarificacionPedido $repositorioSnapshotTarificacionPedido,
        RepositorioTipoCliente $repositorioTipoCliente,
        RepositorioNivelServicioEntrega $repositorioNivelServicioEntrega,
        RepositorioTipoVehiculo $repositorioTipoVehiculo,
    ): Response
    {
        return $this->render('inicio/index.html.twig', [
            'metricas' => [
                'pedidos' => $repositorioPedido->count([]),
                'snapshots' => $repositorioSnapshotTarificacionPedido->count([]),
                'tiposCliente' => $repositorioTipoCliente->count([]),
                'nivelesServicio' => $repositorioNivelServicioEntrega->count([]),
                'tiposVehiculo' => $repositorioTipoVehiculo->count([]),
            ],
            'ultimosPedidos' => $repositorioPedido->findBy([], ['createdAt' => 'DESC'], 5),
            'ultimosSnapshots' => $repositorioSnapshotTarificacionPedido->findBy([], ['createdAt' => 'DESC'], 5),
        ]);
    }
}
