<?php

namespace App\Modulos\Decisiones\Infrastructure\Controller;

use App\Modulos\Decisiones\Domain\Entity\SnapshotTarificacionPedido;
use App\Modulos\Decisiones\Infrastructure\Persistence\Doctrine\RepositorioSnapshotTarificacionPedido;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/snapshots-pedidos')]
final class ControladorSnapshotTarificacionPedido extends AbstractController
{
    #[Route('', name: 'app_snapshots_pedido_index', methods: ['GET'])]
    public function index(RepositorioSnapshotTarificacionPedido $repositorioSnapshotTarificacionPedido): Response
    {
        return $this->render('snapshots/index.html.twig', [
            'snapshots' => $repositorioSnapshotTarificacionPedido->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/{id}', name: 'app_snapshots_pedido_mostrar', methods: ['GET'])]
    public function mostrar(string $id, RepositorioSnapshotTarificacionPedido $repositorioSnapshotTarificacionPedido): Response
    {
        $snapshot = $repositorioSnapshotTarificacionPedido->find(Uuid::fromString($id));
        if (!$snapshot instanceof SnapshotTarificacionPedido) {
            throw $this->createNotFoundException('Snapshot no encontrado.');
        }

        return $this->render('snapshots/show.html.twig', [
            'snapshot' => $snapshot,
        ]);
    }
}
