<?php

namespace App\Modulos\Tarifas\Infrastructure\Controller;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Catalogos\Domain\Entity\TipoVehiculo;
use App\Modulos\Tarifas\Domain\Entity\ReglaTarifaTransportista;
use App\Modulos\Tarifas\Infrastructure\Persistence\Doctrine\RepositorioReglaTarifaTransportista;
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

#[Route('/tarifas/transportistas')]
final class ControladorReglaTarifaTransportista extends AbstractController
{
    #[Route('', name: 'app_tarifas_transportista_index', methods: ['GET'])]
    public function index(RepositorioReglaTarifaTransportista $repositorioReglaTarifaTransportista): Response
    {
        return $this->render('tarifas/transportistas/index.html.twig', [
            'reglas' => $repositorioReglaTarifaTransportista->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/nueva', name: 'app_tarifas_transportista_nueva', methods: ['GET', 'POST'])]
    public function nueva(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->crearFormulario(new ReglaTarifaTransportista(null, null, 0, 0, 0, 0, 0, 0, 0, 0, 0));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($form->getData());
            $entityManager->flush();

            $this->addFlash('success', 'Tarifa transportista creada correctamente.');

            return $this->redirectToRoute('app_tarifas_transportista_index');
        }

        return $this->render('tarifas/transportistas/form.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Nueva tarifa transportista',
        ]);
    }

    #[Route('/{id}/editar', name: 'app_tarifas_transportista_editar', methods: ['GET', 'POST'])]
    public function editar(string $id, Request $request, RepositorioReglaTarifaTransportista $repositorioReglaTarifaTransportista, EntityManagerInterface $entityManager): Response
    {
        $regla = $repositorioReglaTarifaTransportista->find(Uuid::fromString($id));
        if (!$regla instanceof ReglaTarifaTransportista) {
            throw $this->createNotFoundException('Tarifa transportista no encontrada.');
        }

        $form = $this->crearFormulario($regla);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Tarifa transportista actualizada.');

            return $this->redirectToRoute('app_tarifas_transportista_index');
        }

        return $this->render('tarifas/transportistas/form.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Editar tarifa transportista',
        ]);
    }

    private function crearFormulario(ReglaTarifaTransportista $reglaTarifaTransportista)
    {
        return $this->createFormBuilder($reglaTarifaTransportista)
            ->add('tipoVehiculo', EntityType::class, ['label' => 'Tipo de vehiculo', 'class' => TipoVehiculo::class, 'choice_label' => 'nombre'])
            ->add('nivelServicioEntrega', EntityType::class, ['label' => 'Nivel de servicio', 'class' => NivelServicioEntrega::class, 'choice_label' => 'nombre'])
            ->add('distanciaMinimaKm', NumberType::class, ['label' => 'Distancia minima (km)', 'scale' => 2])
            ->add('distanciaMaximaKm', NumberType::class, ['label' => 'Distancia maxima (km)', 'scale' => 2])
            ->add('pesoMinimoGramos', IntegerType::class, ['label' => 'Peso minimo (g)'])
            ->add('pesoMaximoGramos', IntegerType::class, ['label' => 'Peso maximo (g)'])
            ->add('volumenMinimoCm3', IntegerType::class, ['label' => 'Volumen minimo (cm3)'])
            ->add('volumenMaximoCm3', IntegerType::class, ['label' => 'Volumen maximo (cm3)'])
            ->add('precioBaseCentimos', IntegerType::class, ['label' => 'Precio base (centimos)'])
            ->add('distanciaIncluidaKm', NumberType::class, ['label' => 'Distancia incluida (km)', 'scale' => 2])
            ->add('precioKmExtraCentimos', IntegerType::class, ['label' => 'Precio por km extra (centimos)'])
            ->add('activa', CheckboxType::class, ['label' => 'Activa', 'required' => false])
            ->add('guardar', SubmitType::class, ['label' => 'Guardar'])
            ->getForm();
    }
}
