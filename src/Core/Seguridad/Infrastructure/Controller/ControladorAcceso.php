<?php

namespace App\Core\Seguridad\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class ControladorAcceso extends AbstractController
{
    #[Route('/acceso', name: 'app_acceso', methods: ['GET', 'POST'])]
    public function __invoke(AuthenticationUtils $authenticationUtils): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('app_inicio');
        }

        return $this->render('seguridad/acceso.html.twig', [
            'ultimo_usuario' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/salir', name: 'app_salir', methods: ['GET'])]
    public function salir(): never
    {
        throw new \LogicException('La ruta de salida debe ser interceptada por el firewall.');
    }
}
