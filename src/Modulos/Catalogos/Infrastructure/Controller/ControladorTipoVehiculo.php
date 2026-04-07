<?php

namespace App\Modulos\Catalogos\Infrastructure\Controller;

use App\Modulos\Catalogos\Domain\Entity\TipoVehiculo;
use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioTipoVehiculo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/catalogos/tipos-vehiculo')]
final class ControladorTipoVehiculo extends AbstractController
{
    #[Route('', name: 'app_tipos_vehiculo_index', methods: ['GET'])]
    public function index(RepositorioTipoVehiculo $repositorioTipoVehiculo): Response
    {
        return $this->render('catalogos/tipos_vehiculo/index.html.twig', [
            'tiposVehiculo' => $repositorioTipoVehiculo->findBy([], ['nombre' => 'ASC']),
        ]);
    }

    #[Route('/nuevo', name: 'app_tipos_vehiculo_nuevo', methods: ['GET', 'POST'])]
    public function nuevo(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tipoVehiculo = new TipoVehiculo('', '', 0, 0);
        $form = $this->crearFormularioTipoVehiculo($tipoVehiculo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tipoVehiculo);
            $entityManager->flush();

            $this->addFlash('success', 'Tipo de vehiculo creado correctamente.');

            return $this->redirectToRoute('app_tipos_vehiculo_index');
        }

        return $this->render('catalogos/tipos_vehiculo/form.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Nuevo tipo de vehiculo',
        ]);
    }

    #[Route('/{id}/editar', name: 'app_tipos_vehiculo_editar', methods: ['GET', 'POST'])]
    public function editar(string $id, Request $request, RepositorioTipoVehiculo $repositorioTipoVehiculo, EntityManagerInterface $entityManager): Response
    {
        $tipoVehiculo = $repositorioTipoVehiculo->find(Uuid::fromString($id));
        if (!$tipoVehiculo instanceof TipoVehiculo) {
            throw $this->createNotFoundException('Tipo de vehiculo no encontrado.');
        }

        $form = $this->crearFormularioTipoVehiculo($tipoVehiculo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Tipo de vehiculo actualizado.');

            return $this->redirectToRoute('app_tipos_vehiculo_index');
        }

        return $this->render('catalogos/tipos_vehiculo/form.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Editar tipo de vehiculo',
            'tipoVehiculo' => $tipoVehiculo,
        ]);
    }

    private function crearFormularioTipoVehiculo(TipoVehiculo $tipoVehiculo)
    {
        return $this->createFormBuilder($tipoVehiculo)
            ->add('nombre', TextType::class, ['label' => 'Nombre'])
            ->add('codigo', TextType::class, ['label' => 'Codigo'])
            ->add('pesoMaximoGramos', IntegerType::class, ['label' => 'Peso maximo (g)'])
            ->add('volumenMaximoCm3', IntegerType::class, ['label' => 'Volumen maximo (cm3)'])
            ->add('activo', CheckboxType::class, ['label' => 'Activo', 'required' => false])
            ->add('guardar', SubmitType::class, ['label' => 'Guardar'])
            ->getForm();
    }
}
