<?php

namespace App\Modulos\Catalogos\Infrastructure\Command;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Catalogos\Domain\Entity\TipoCliente;
use App\Modulos\Catalogos\Domain\Entity\TipoVehiculo;
use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioNivelServicioEntrega;
use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioTipoCliente;
use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioTipoVehiculo;
use App\Modulos\Decisiones\Domain\Entity\SnapshotTarificacionPedido;
use App\Modulos\Operaciones\Domain\Entity\ReglaDisponibilidadServicio;
use App\Modulos\Pedidos\Domain\Entity\Pedido;
use App\Modulos\Tarifas\Domain\Entity\ReglaTarifaCliente;
use App\Modulos\Tarifas\Domain\Entity\ReglaTarifaTransportista;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:demo:recrear-configuracion', description: 'Limpia reglas demo y crea una configuracion coherente para pruebas visuales.')]
final class RecrearConfiguracionDemoCommand extends Command
{
    public function __construct(
        private readonly RepositorioTipoCliente $repositorioTipoCliente,
        private readonly RepositorioNivelServicioEntrega $repositorioNivelServicioEntrega,
        private readonly RepositorioTipoVehiculo $repositorioTipoVehiculo,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->limpiarConfiguracionDemo();

        $particular = $this->buscarOCrearTipoCliente('Particular', 'PARTICULAR');
        $profesional = $this->buscarOCrearTipoCliente('Profesional', 'PROFESIONAL');

        $express = $this->buscarOCrearServicio('Express', 'EXPRESS', 10);
        $standard = $this->buscarOCrearServicio('Standard', 'STANDARD', 20);
        $programado = $this->buscarOCrearServicio('Programado', 'PROGRAMADO', 30);

        $patinete = $this->buscarOCrearVehiculo('Patinete', 'PATINETE', 5000, 25000);
        $moto = $this->buscarOCrearVehiculo('Moto', 'MOTO', 25000, 120000);
        $coche = $this->buscarOCrearVehiculo('Coche', 'COCHE', 120000, 900000);
        $furgoneta = $this->buscarOCrearVehiculo('Furgoneta', 'FURGONETA', 500000, 4500000);

        foreach ([
            new ReglaDisponibilidadServicio($express, 5, 20000, 90000),
            new ReglaDisponibilidadServicio($standard, 15, 70000, 850000),
            new ReglaDisponibilidadServicio($programado, 40, 500000, 4500000),
        ] as $regla) {
            $this->entityManager->persist($regla);
        }

        foreach ([
            new ReglaTarifaCliente($particular, $express, 0, 3, 1200),
            new ReglaTarifaCliente($particular, $express, 3.01, 5, 1400),
            new ReglaTarifaCliente($particular, $standard, 0, 7, 700),
            new ReglaTarifaCliente($particular, $standard, 7.01, 15, 950),
            new ReglaTarifaCliente($particular, $programado, 0, 10, 600),
            new ReglaTarifaCliente($particular, $programado, 10.01, 40, 900),
            new ReglaTarifaCliente($profesional, $express, 0, 3, 1050),
            new ReglaTarifaCliente($profesional, $express, 3.01, 5, 1250),
            new ReglaTarifaCliente($profesional, $standard, 0, 7, 650),
            new ReglaTarifaCliente($profesional, $standard, 7.01, 15, 850),
            new ReglaTarifaCliente($profesional, $programado, 0, 10, 550),
            new ReglaTarifaCliente($profesional, $programado, 10.01, 40, 800),
        ] as $regla) {
            $this->entityManager->persist($regla);
        }

        foreach ([
            new ReglaTarifaTransportista($moto, $express, 0, 5, 0, 12000, 0, 90000, 850, 3, 100),
            new ReglaTarifaTransportista($coche, $express, 0, 5, 8000, 20000, 0, 180000, 1150, 4, 95),
            new ReglaTarifaTransportista($patinete, $standard, 0, 4, 0, 4000, 0, 25000, 420, 2, 80),
            new ReglaTarifaTransportista($moto, $standard, 0, 8, 0, 18000, 0, 100000, 560, 4, 70),
            new ReglaTarifaTransportista($moto, $standard, 5, 12, 12000, 25000, 0, 120000, 620, 5, 65),
            new ReglaTarifaTransportista($coche, $standard, 4, 15, 8000, 70000, 20000, 850000, 760, 6, 75),
            new ReglaTarifaTransportista($furgoneta, $standard, 8, 15, 50000, 500000, 250000, 4500000, 1080, 8, 95),
            new ReglaTarifaTransportista($moto, $programado, 0, 10, 0, 15000, 0, 100000, 500, 5, 55),
            new ReglaTarifaTransportista($coche, $programado, 6, 25, 10000, 120000, 20000, 900000, 650, 8, 60),
            new ReglaTarifaTransportista($furgoneta, $programado, 10, 40, 60000, 500000, 250000, 4500000, 920, 10, 80),
        ] as $regla) {
            $this->entityManager->persist($regla);
        }

        $this->entityManager->flush();

        $output->writeln('<info>Configuracion demo recreada correctamente.</info>');

        return Command::SUCCESS;
    }

    private function limpiarConfiguracionDemo(): void
    {
        $this->entityManager->createQuery('DELETE FROM ' . SnapshotTarificacionPedido::class . ' s')->execute();
        $this->entityManager->createQuery("DELETE FROM " . Pedido::class . " p WHERE p.referencia LIKE 'PED-DEMO-%'")->execute();
        $this->entityManager->createQuery('DELETE FROM ' . ReglaTarifaTransportista::class . ' r')->execute();
        $this->entityManager->createQuery('DELETE FROM ' . ReglaTarifaCliente::class . ' r')->execute();
        $this->entityManager->createQuery('DELETE FROM ' . ReglaDisponibilidadServicio::class . ' r')->execute();
        $this->entityManager->clear();
    }

    private function buscarOCrearTipoCliente(string $nombre, string $codigo): TipoCliente
    {
        $tipoCliente = $this->repositorioTipoCliente->findOneBy(['codigo' => $codigo]);
        if ($tipoCliente instanceof TipoCliente) {
            return $tipoCliente;
        }

        $tipoCliente = new TipoCliente($nombre, $codigo);
        $this->entityManager->persist($tipoCliente);

        return $tipoCliente;
    }

    private function buscarOCrearServicio(string $nombre, string $codigo, int $ordenVisual): NivelServicioEntrega
    {
        $nivelServicioEntrega = $this->repositorioNivelServicioEntrega->findOneBy(['codigo' => $codigo]);
        if ($nivelServicioEntrega instanceof NivelServicioEntrega) {
            return $nivelServicioEntrega;
        }

        $nivelServicioEntrega = new NivelServicioEntrega($nombre, $codigo);
        $nivelServicioEntrega->reordenar($ordenVisual);
        $this->entityManager->persist($nivelServicioEntrega);

        return $nivelServicioEntrega;
    }

    private function buscarOCrearVehiculo(string $nombre, string $codigo, int $pesoMaximoGramos, int $volumenMaximoCm3): TipoVehiculo
    {
        $tipoVehiculo = $this->repositorioTipoVehiculo->findOneBy(['codigo' => $codigo]);
        if ($tipoVehiculo instanceof TipoVehiculo) {
            return $tipoVehiculo;
        }

        $tipoVehiculo = new TipoVehiculo($nombre, $codigo, $pesoMaximoGramos, $volumenMaximoCm3);
        $this->entityManager->persist($tipoVehiculo);

        return $tipoVehiculo;
    }
}
