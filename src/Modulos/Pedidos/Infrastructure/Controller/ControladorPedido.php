<?php

namespace App\Modulos\Pedidos\Infrastructure\Controller;

use App\Modulos\Catalogos\Domain\Entity\TipoCliente;
use App\Modulos\Decisiones\Application\ServicioSnapshotPedido;
use App\Modulos\Decisiones\Application\ResolutorOpcionesEntrega;
use App\Modulos\Pedidos\Domain\Enum\EstadoPedido;
use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioTipoCliente;
use App\Modulos\Pedidos\Application\CalculadoraMetricasPedido;
use App\Modulos\Pedidos\Domain\Entity\LineaPedido;
use App\Modulos\Pedidos\Domain\Entity\Pedido;
use App\Modulos\Pedidos\Infrastructure\Persistence\Doctrine\RepositorioLineaPedido;
use App\Modulos\Pedidos\Infrastructure\Persistence\Doctrine\RepositorioPedido;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/pedidos')]
final class ControladorPedido extends AbstractController
{
    #[Route('', name: 'app_pedidos_index', methods: ['GET'])]
    public function index(RepositorioPedido $repositorioPedido): Response
    {
        return $this->render('pedidos/index.html.twig', [
            'pedidos' => $repositorioPedido->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/nuevo', name: 'app_pedidos_nuevo', methods: ['GET', 'POST'])]
    public function nuevo(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pedido = new Pedido('PED-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)), '', '');
        $form = $this->crearFormularioPedido($pedido);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pedido);
            $entityManager->flush();

            $this->addFlash('success', 'Pedido creado correctamente.');

            return $this->redirectToRoute('app_pedidos_mostrar', ['id' => (string) $pedido->getId()]);
        }

        return $this->render('pedidos/form.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Nuevo pedido',
        ]);
    }

    #[Route('/{id}', name: 'app_pedidos_mostrar', methods: ['GET', 'POST'])]
    public function mostrar(string $id, Request $request, RepositorioPedido $repositorioPedido, EntityManagerInterface $entityManager, CalculadoraMetricasPedido $calculadoraMetricasPedido, ResolutorOpcionesEntrega $resolutorOpcionesEntrega): Response
    {
        $pedido = $this->buscarPedido($repositorioPedido, $id);
        $opcionesEntrega = $resolutorOpcionesEntrega->resolverParaPedido($pedido);

        $lineaPedido = new LineaPedido($pedido, '', 1, 0, 0);
        $formLinea = $this->crearFormularioLinea($lineaPedido);
        $formLinea->handleRequest($request);

        if ($formLinea->isSubmitted() && $formLinea->isValid()) {
            if ($this->pedidoBloqueadoParaEdicion($pedido)) {
                $this->addFlash('error', 'No se pueden anadir lineas a un pedido confirmado o cancelado.');

                return $this->redirectToRoute('app_pedidos_mostrar', ['id' => $id]);
            }

            $pedido->agregarLinea($lineaPedido);
            $calculadoraMetricasPedido->recalcular($pedido);
            $entityManager->persist($lineaPedido);
            $entityManager->flush();

            $this->addFlash('success', 'Linea de pedido anadida correctamente.');

            return $this->redirectToRoute('app_pedidos_mostrar', ['id' => $id]);
        }

        return $this->render('pedidos/show.html.twig', [
            'pedido' => $pedido,
            'formLinea' => $formLinea->createView(),
            'resultadoEntrega' => $opcionesEntrega,
        ]);
    }

    #[Route('/{id}/editar', name: 'app_pedidos_editar', methods: ['GET', 'POST'])]
    public function editar(string $id, Request $request, RepositorioPedido $repositorioPedido, EntityManagerInterface $entityManager, CalculadoraMetricasPedido $calculadoraMetricasPedido): Response
    {
        $pedido = $this->buscarPedido($repositorioPedido, $id);
        if ($this->pedidoBloqueadoParaEdicion($pedido)) {
            $this->addFlash('error', 'No se puede editar un pedido confirmado o cancelado.');

            return $this->redirectToRoute('app_pedidos_mostrar', ['id' => $id]);
        }

        $form = $this->crearFormularioPedido($pedido);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $calculadoraMetricasPedido->recalcular($pedido);
            $entityManager->flush();

            $this->addFlash('success', 'Pedido actualizado.');

            return $this->redirectToRoute('app_pedidos_mostrar', ['id' => $id]);
        }

        return $this->render('pedidos/form.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Editar pedido',
            'pedido' => $pedido,
        ]);
    }

    #[Route('/lineas/{id}/editar', name: 'app_pedidos_lineas_editar', methods: ['GET', 'POST'])]
    public function editarLinea(string $id, Request $request, RepositorioLineaPedido $repositorioLineaPedido, EntityManagerInterface $entityManager, CalculadoraMetricasPedido $calculadoraMetricasPedido): Response
    {
        $lineaPedido = $repositorioLineaPedido->find(Uuid::fromString($id));
        if (!$lineaPedido instanceof LineaPedido) {
            throw $this->createNotFoundException('Linea de pedido no encontrada.');
        }

        if ($this->pedidoBloqueadoParaEdicion($lineaPedido->getPedido())) {
            $this->addFlash('error', 'No se puede editar lineas de un pedido confirmado o cancelado.');

            return $this->redirectToRoute('app_pedidos_mostrar', ['id' => (string) $lineaPedido->getPedido()->getId()]);
        }

        $form = $this->crearFormularioLinea($lineaPedido);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $calculadoraMetricasPedido->recalcular($lineaPedido->getPedido());
            $entityManager->flush();

            $this->addFlash('success', 'Linea de pedido actualizada.');

            return $this->redirectToRoute('app_pedidos_mostrar', ['id' => (string) $lineaPedido->getPedido()->getId()]);
        }

        return $this->render('pedidos/linea_form.html.twig', [
            'form' => $form->createView(),
            'pedido' => $lineaPedido->getPedido(),
            'titulo' => 'Editar linea de pedido',
        ]);
    }

    #[Route('/lineas/{id}/eliminar', name: 'app_pedidos_lineas_eliminar', methods: ['POST'])]
    public function eliminarLinea(string $id, Request $request, RepositorioLineaPedido $repositorioLineaPedido, EntityManagerInterface $entityManager, CalculadoraMetricasPedido $calculadoraMetricasPedido): Response
    {
        $lineaPedido = $repositorioLineaPedido->find(Uuid::fromString($id));
        if (!$lineaPedido instanceof LineaPedido) {
            throw $this->createNotFoundException('Linea de pedido no encontrada.');
        }

        $pedido = $lineaPedido->getPedido();
        if ($this->pedidoBloqueadoParaEdicion($pedido)) {
            $this->addFlash('error', 'No se puede eliminar lineas de un pedido confirmado o cancelado.');

            return $this->redirectToRoute('app_pedidos_mostrar', ['id' => (string) $pedido->getId()]);
        }

        if (!$this->isCsrfTokenValid('eliminar_linea_' . $id, (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'No se pudo validar la eliminacion de la linea.');

            return $this->redirectToRoute('app_pedidos_mostrar', ['id' => (string) $pedido->getId()]);
        }

        $pedido->quitarLinea($lineaPedido);
        $entityManager->remove($lineaPedido);
        $calculadoraMetricasPedido->recalcular($pedido);
        $entityManager->flush();

        $this->addFlash('success', 'Linea de pedido eliminada.');

        return $this->redirectToRoute('app_pedidos_mostrar', ['id' => (string) $pedido->getId()]);
    }

    #[Route('/{id}/confirmar/{servicioId}', name: 'app_pedidos_confirmar_servicio', methods: ['POST'])]
    public function confirmarServicio(string $id, string $servicioId, Request $request, RepositorioPedido $repositorioPedido, EntityManagerInterface $entityManager, ResolutorOpcionesEntrega $resolutorOpcionesEntrega, ServicioSnapshotPedido $servicioSnapshotPedido): Response
    {
        $pedido = $this->buscarPedido($repositorioPedido, $id);
        if ($this->pedidoBloqueadoParaEdicion($pedido)) {
            $this->addFlash('error', 'El pedido ya no admite cambios de servicio.');

            return $this->redirectToRoute('app_pedidos_mostrar', ['id' => $id]);
        }

        if (!$this->isCsrfTokenValid('confirmar_servicio_' . $id . '_' . $servicioId, (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'No se pudo validar la confirmacion del servicio.');

            return $this->redirectToRoute('app_pedidos_mostrar', ['id' => $id]);
        }

        $resultado = $resolutorOpcionesEntrega->resolverParaPedido($pedido);
        foreach ($resultado->opciones as $opcion) {
            if ((string) $opcion->nivelServicioEntrega->getId() !== $servicioId || null === $opcion->vehiculoOptimo || null === $opcion->getMargenCentimos()) {
                continue;
            }

            $pedido->asignarDecisionEntrega(
                $opcion->nivelServicioEntrega,
                $opcion->vehiculoOptimo->tipoVehiculo,
                $opcion->precioClienteCentimos,
                $opcion->vehiculoOptimo->costeCentimos,
                $opcion->getMargenCentimos(),
            );
            $pedido->confirmar();
            $servicioSnapshotPedido->crearSnapshot($pedido, $resultado, $opcion);
            $entityManager->flush();

            $this->addFlash('success', 'Servicio confirmado, snapshot creado y pedido bloqueado para cambios operativos.');

            return $this->redirectToRoute('app_pedidos_mostrar', ['id' => $id]);
        }

        $this->addFlash('error', 'La opcion seleccionada ya no es valida para este pedido.');

        return $this->redirectToRoute('app_pedidos_mostrar', ['id' => $id]);
    }

    private function crearFormularioPedido(Pedido $pedido)
    {
        return $this->createFormBuilder($pedido)
            ->add('referencia', TextType::class, ['label' => 'Referencia'])
            ->add('nombreCliente', TextType::class, ['label' => 'Nombre del cliente'])
            ->add('telefonoCliente', TextType::class, ['label' => 'Telefono del cliente'])
            ->add('tipoCliente', EntityType::class, [
                'label' => 'Tipo de cliente',
                'class' => TipoCliente::class,
                'choice_label' => 'nombre',
                'query_builder' => static fn (RepositorioTipoCliente $repo) => $repo->createQueryBuilder('tipo')->orderBy('tipo.nombre', 'ASC'),
            ])
            ->add('distanciaKm', NumberType::class, ['label' => 'Distancia manual (km)', 'scale' => 2])
            ->add('guardar', SubmitType::class, ['label' => 'Guardar'])
            ->getForm();
    }

    private function crearFormularioLinea(LineaPedido $lineaPedido)
    {
        return $this->createFormBuilder($lineaPedido)
            ->add('descripcion', TextType::class, ['label' => 'Descripcion'])
            ->add('cantidad', IntegerType::class, ['label' => 'Cantidad'])
            ->add('pesoUnitarioGramos', IntegerType::class, ['label' => 'Peso unitario (g)'])
            ->add('volumenUnitarioCm3', IntegerType::class, ['label' => 'Volumen unitario (cm3)'])
            ->add('guardar', SubmitType::class, ['label' => 'Guardar linea'])
            ->getForm();
    }

    private function buscarPedido(RepositorioPedido $repositorioPedido, string $id): Pedido
    {
        $pedido = $repositorioPedido->find(Uuid::fromString($id));
        if (!$pedido instanceof Pedido) {
            throw $this->createNotFoundException('Pedido no encontrado.');
        }

        return $pedido;
    }

    private function pedidoBloqueadoParaEdicion(Pedido $pedido): bool
    {
        return in_array($pedido->getEstado(), [EstadoPedido::CONFIRMADO, EstadoPedido::CANCELADO], true);
    }
}
