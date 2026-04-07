<?php

namespace App\Modulos\Catalogos\Infrastructure\Controller;

use App\Modulos\Catalogos\Domain\Entity\TipoCliente;
use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioTipoCliente;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/catalogos/tipos-cliente')]
final class ControladorTipoCliente extends AbstractController
{
    #[Route('', name: 'app_tipos_cliente_index', methods: ['GET'])]
    public function index(RepositorioTipoCliente $repositorioTipoCliente): Response
    {
        return $this->render('catalogos/tipos_cliente/index.html.twig', [
            'tiposCliente' => $repositorioTipoCliente->findBy([], ['nombre' => 'ASC']),
        ]);
    }

    #[Route('/nuevo', name: 'app_tipos_cliente_nuevo', methods: ['GET', 'POST'])]
    public function nuevo(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tipoCliente = new TipoCliente('', '');
        $form = $this->crearFormularioTipoCliente($tipoCliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tipoCliente);
            $entityManager->flush();

            $this->addFlash('success', 'Tipo de cliente creado correctamente.');

            return $this->redirectToRoute('app_tipos_cliente_index');
        }

        return $this->render('catalogos/tipos_cliente/form.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Nuevo tipo de cliente',
        ]);
    }

    #[Route('/{id}/editar', name: 'app_tipos_cliente_editar', methods: ['GET', 'POST'])]
    public function editar(string $id, Request $request, RepositorioTipoCliente $repositorioTipoCliente, EntityManagerInterface $entityManager): Response
    {
        $tipoCliente = $repositorioTipoCliente->find(Uuid::fromString($id));
        if (!$tipoCliente instanceof TipoCliente) {
            throw $this->createNotFoundException('Tipo de cliente no encontrado.');
        }

        $form = $this->crearFormularioTipoCliente($tipoCliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Tipo de cliente actualizado.');

            return $this->redirectToRoute('app_tipos_cliente_index');
        }

        return $this->render('catalogos/tipos_cliente/form.html.twig', [
            'form' => $form->createView(),
            'titulo' => 'Editar tipo de cliente',
            'tipoCliente' => $tipoCliente,
        ]);
    }

    private function crearFormularioTipoCliente(TipoCliente $tipoCliente)
    {
        return $this->createFormBuilder($tipoCliente)
            ->add('nombre', TextType::class, ['label' => 'Nombre'])
            ->add('codigo', TextType::class, ['label' => 'Codigo'])
            ->add('activo', CheckboxType::class, ['label' => 'Activo', 'required' => false])
            ->add('guardar', SubmitType::class, ['label' => 'Guardar'])
            ->getForm();
    }
}
