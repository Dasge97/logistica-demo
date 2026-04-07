<?php

namespace App\Core\Seguridad\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ControladorInicio extends AbstractController
{
    #[Route('/', name: 'app_inicio', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('inicio/index.html.twig');
    }
}
