<?php

namespace App\Modulos\Simulador\Infrastructure\Controller;

use App\Modulos\Catalogos\Domain\Entity\TipoCliente;
use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioTipoCliente;
use App\Modulos\Decisiones\Application\ResolutorOpcionesEntrega;
use App\Modulos\Pedidos\Domain\Entity\Pedido;
use App\Modulos\Simulador\Infrastructure\Form\DatosSimuladorLogistico;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

final class ControladorSimuladorLogistico extends AbstractController
{
    #[Route('/simulador-logistico', name: 'app_simulador_logistico', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, ResolutorOpcionesEntrega $resolutorOpcionesEntrega, RepositorioTipoCliente $repositorioTipoCliente): Response
    {
        $datos = $this->crearDatosDesdeRequest($request, $repositorioTipoCliente);

        $form = $this->createFormBuilder($datos)
            ->add('tipoCliente', EntityType::class, [
                'label' => 'Tipo de cliente',
                'class' => TipoCliente::class,
                'choice_label' => 'nombre',
            ])
            ->add('distanciaKm', NumberType::class, [
                'label' => 'Distancia manual (km)',
                'scale' => 2,
            ])
            ->add('pesoTotalGramos', IntegerType::class, [
                'label' => 'Peso total (g)',
            ])
            ->add('volumenTotalCm3', IntegerType::class, [
                'label' => 'Volumen total (cm3)',
            ])
            ->add('simular', SubmitType::class, [
                'label' => 'Simular escenario',
            ])
            ->getForm();

        $form->handleRequest($request);

        $resultado = null;

        if ($form->isSubmitted() && $form->isValid() && $datos->tipoCliente instanceof TipoCliente) {
            return $this->redirectToRoute('app_simulador_logistico', [
                'tipoCliente' => (string) $datos->tipoCliente->getId(),
                'distancia' => $datos->distanciaKm,
                'peso' => $datos->pesoTotalGramos,
                'volumen' => $datos->volumenTotalCm3,
            ]);
        }

        if ($this->tieneParametrosDeSimulacion($request) && $datos->tipoCliente instanceof TipoCliente) {
            $pedido = new Pedido('SIMULACION', 'Simulacion', 'N/A', $datos->tipoCliente);
            $pedido->setDistanciaKm($datos->distanciaKm);
            $pedido->actualizarMetricas($datos->pesoTotalGramos, $datos->volumenTotalCm3);
            $resultado = $resolutorOpcionesEntrega->resolverParaPedido($pedido);
        }

        return $this->render('simulador/logistico.html.twig', [
            'form' => $form->createView(),
            'datos' => $datos,
            'resultado' => $resultado,
        ]);
    }

    private function crearDatosDesdeRequest(Request $request, RepositorioTipoCliente $repositorioTipoCliente): DatosSimuladorLogistico
    {
        $datos = new DatosSimuladorLogistico();
        $datos->distanciaKm = (float) $request->query->get('distancia', 3);
        $datos->pesoTotalGramos = (int) $request->query->get('peso', 4000);
        $datos->volumenTotalCm3 = (int) $request->query->get('volumen', 18000);

        $tipoClienteId = $request->query->get('tipoCliente');
        if (is_string($tipoClienteId) && '' !== $tipoClienteId) {
            $tipoCliente = $repositorioTipoCliente->find(Uuid::fromString($tipoClienteId));
            if ($tipoCliente instanceof TipoCliente) {
                $datos->tipoCliente = $tipoCliente;
            }
        }

        return $datos;
    }

    private function tieneParametrosDeSimulacion(Request $request): bool
    {
        return $request->query->has('tipoCliente')
            && $request->query->has('distancia')
            && $request->query->has('peso')
            && $request->query->has('volumen');
    }

}
