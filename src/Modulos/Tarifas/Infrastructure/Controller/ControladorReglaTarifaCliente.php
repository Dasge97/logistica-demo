<?php

namespace App\Modulos\Tarifas\Infrastructure\Controller;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Catalogos\Domain\Entity\TipoCliente;
use App\Modulos\Tarifas\Domain\Entity\ReglaTarifaCliente;
use App\Modulos\Tarifas\Infrastructure\Persistence\Doctrine\RepositorioReglaTarifaCliente;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/tarifas/clientes')]
final class ControladorReglaTarifaCliente extends AbstractController
{
    #[Route('', name: 'app_tarifas_cliente_index', methods: ['GET'])]
    public function index(RepositorioReglaTarifaCliente $repositorioReglaTarifaCliente): Response
    {
        return $this->render('tarifas/clientes/index.html.twig', [
            'reglas' => $repositorioReglaTarifaCliente->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/nueva', name: 'app_tarifas_cliente_nueva', methods: ['GET', 'POST'])]
    public function nueva(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->crearFormulario(new ReglaTarifaCliente(null, null, 0, 0, 0));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($form->getData());
            $entityManager->flush();

            $this->addFlash('success', 'Tarifa cliente creada correctamente.');

            return $this->redirectToRoute('app_tarifas_cliente_index');
        }

        return $this->render('tarifas/clientes/form.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Nueva tarifa cliente',
        ]);
    }

    #[Route('/{id}/editar', name: 'app_tarifas_cliente_editar', methods: ['GET', 'POST'])]
    public function editar(string $id, Request $request, RepositorioReglaTarifaCliente $repositorioReglaTarifaCliente, EntityManagerInterface $entityManager): Response
    {
        $regla = $repositorioReglaTarifaCliente->find(Uuid::fromString($id));
        if (!$regla instanceof ReglaTarifaCliente) {
            throw $this->createNotFoundException('Tarifa cliente no encontrada.');
        }

        $form = $this->crearFormulario($regla);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Tarifa cliente actualizada.');

            return $this->redirectToRoute('app_tarifas_cliente_index');
        }

        return $this->render('tarifas/clientes/form.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Editar tarifa cliente',
        ]);
    }

    private function crearFormulario(ReglaTarifaCliente $reglaTarifaCliente)
    {
        return $this->createFormBuilder($reglaTarifaCliente)
            ->add('tipoCliente', EntityType::class, ['label' => 'Tipo de cliente', 'class' => TipoCliente::class, 'choice_label' => 'nombre'])
            ->add('nivelServicioEntrega', EntityType::class, ['label' => 'Nivel de servicio', 'class' => NivelServicioEntrega::class, 'choice_label' => 'nombre'])
            ->add('distanciaDesdeKm', NumberType::class, ['label' => 'Distancia desde (km)', 'scale' => 2])
            ->add('distanciaHastaKm', NumberType::class, ['label' => 'Distancia hasta (km)', 'scale' => 2])
            ->add('precioClienteCentimos', IntegerType::class, ['label' => 'Precio cliente (centimos)'])
            ->add('activa', CheckboxType::class, ['label' => 'Activa', 'required' => false])
            ->add('guardar', SubmitType::class, ['label' => 'Guardar'])
            ->getForm();
    }
}
