<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class BackofficeBasicoTest extends KernelTestCase
{
    public function testMuestraLaPantallaDeAcceso(): void
    {
        self::bootKernel();

        $response = self::$kernel->handle(Request::create('/acceso'), HttpKernelInterface::MAIN_REQUEST);

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('Accede al backoffice', $response->getContent() ?: '');
    }
}
