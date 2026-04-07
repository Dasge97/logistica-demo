<?php

namespace App\Modulos\Operaciones\Infrastructure\Controller;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Operaciones\Domain\Entity\ReglaDisponibilidadServicio;
use App\Modulos\Operaciones\Infrastructure\Persistence\Doctrine\RepositorioReglaDisponibilidadServicio;
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

#[Route('/operaciones/reglas-disponibilidad')]
final class ControladorReglaDisponibilidadServicio extends AbstractController
{
    #[Route('', name: 'app_reglas_disponibilidad_index', methods: ['GET'])]
    public function index(RepositorioReglaDisponibilidadServicio $repositorioReglaDisponibilidadServicio): Response
    {
        return $this->render('operaciones/reglas_disponibilidad/index.html.twig', [
            'reglas' => $repositorioReglaDisponibilidadServicio->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/nueva', name: 'app_reglas_disponibilidad_nueva', methods: ['GET', 'POST'])]
    public function nueva(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->crearFormulario(new ReglaDisponibilidadServicio(null, 0, 0, 0));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($form->getData());
            $entityManager->flush();

            $this->addFlash('success', 'Regla de disponibilidad creada correctamente.');

            return $this->redirectToRoute('app_reglas_disponibilidad_index');
        }

        return $this->render('operaciones/reglas_disponibilidad/form.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Nueva regla de disponibilidad',
        ]);
    }

    #[Route('/{id}/editar', name: 'app_reglas_disponibilidad_editar', methods: ['GET', 'POST'])]
    public function editar(string $id, Request $request, RepositorioReglaDisponibilidadServicio $repositorioReglaDisponibilidadServicio, EntityManagerInterface $entityManager): Response
    {
        $regla = $repositorioReglaDisponibilidadServicio->find(Uuid::fromString($id));
        if (!$regla instanceof ReglaDisponibilidadServicio) {
            throw $this->createNotFoundException('Regla de disponibilidad no encontrada.');
        }

        $form = $this->crearFormulario($regla);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Regla de disponibilidad actualizada.');

            return $this->redirectToRoute('app_reglas_disponibilidad_index');
        }

        return $this->render('operaciones/reglas_disponibilidad/form.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Editar regla de disponibilidad',
        ]);
    }

    private function crearFormulario(ReglaDisponibilidadServicio $reglaDisponibilidadServicio)
    {
        return $this->createFormBuilder($reglaDisponibilidadServicio)
            ->add('nivelServicioEntrega', EntityType::class, [
                'label' => 'Nivel de servicio',
                'class' => NivelServicioEntrega::class,
                'choice_label' => 'nombre',
            ])
            ->add('distanciaMaximaKm', NumberType::class, ['label' => 'Distancia maxima (km)', 'scale' => 2])
            ->add('pesoMaximoGramos', IntegerType::class, ['label' => 'Peso maximo (g)'])
            ->add('volumenMaximoCm3', IntegerType::class, ['label' => 'Volumen maximo (cm3)'])
            ->add('activa', CheckboxType::class, ['label' => 'Activa', 'required' => false])
            ->add('guardar', SubmitType::class, ['label' => 'Guardar'])
            ->getForm();
    }
}
