<?php

namespace App\Modulos\Catalogos\Infrastructure\Controller;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioNivelServicioEntrega;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/catalogos/niveles-servicio')]
final class ControladorNivelServicioEntrega extends AbstractController
{
    #[Route('', name: 'app_niveles_servicio_index', methods: ['GET'])]
    public function index(RepositorioNivelServicioEntrega $repositorioNivelServicioEntrega): Response
    {
        return $this->render('catalogos/niveles_servicio/index.html.twig', [
            'nivelesServicio' => $repositorioNivelServicioEntrega->findBy([], ['ordenVisual' => 'ASC', 'nombre' => 'ASC']),
        ]);
    }

    #[Route('/nuevo', name: 'app_niveles_servicio_nuevo', methods: ['GET', 'POST'])]
    public function nuevo(Request $request, EntityManagerInterface $entityManager): Response
    {
        $nivelServicio = new NivelServicioEntrega('', '');
        $form = $this->crearFormularioNivelServicio($nivelServicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($nivelServicio);
            $entityManager->flush();

            $this->addFlash('success', 'Nivel de servicio creado correctamente.');

            return $this->redirectToRoute('app_niveles_servicio_index');
        }

        return $this->render('catalogos/niveles_servicio/form.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Nuevo nivel de servicio',
        ]);
    }

    #[Route('/{id}/editar', name: 'app_niveles_servicio_editar', methods: ['GET', 'POST'])]
    public function editar(string $id, Request $request, RepositorioNivelServicioEntrega $repositorioNivelServicioEntrega, EntityManagerInterface $entityManager): Response
    {
        $nivelServicio = $repositorioNivelServicioEntrega->find(Uuid::fromString($id));
        if (!$nivelServicio instanceof NivelServicioEntrega) {
            throw $this->createNotFoundException('Nivel de servicio no encontrado.');
        }

        $form = $this->crearFormularioNivelServicio($nivelServicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Nivel de servicio actualizado.');

            return $this->redirectToRoute('app_niveles_servicio_index');
        }

        return $this->render('catalogos/niveles_servicio/form.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Editar nivel de servicio',
            'nivelServicio' => $nivelServicio,
        ]);
    }

    private function crearFormularioNivelServicio(NivelServicioEntrega $nivelServicio)
    {
        return $this->createFormBuilder($nivelServicio)
            ->add('nombre', TextType::class, ['label' => 'Nombre'])
            ->add('codigo', TextType::class, ['label' => 'Codigo'])
            ->add('ordenVisual', IntegerType::class, ['label' => 'Orden visual'])
            ->add('activo', CheckboxType::class, ['label' => 'Activo', 'required' => false])
            ->add('guardar', SubmitType::class, ['label' => 'Guardar'])
            ->getForm();
    }
}
