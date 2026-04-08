<?php

namespace App\Modulos\Catalogos\Infrastructure\Command;

use App\Modulos\Catalogos\Domain\Entity\NivelServicioEntrega;
use App\Modulos\Catalogos\Domain\Entity\TipoCliente;
use App\Modulos\Catalogos\Domain\Entity\TipoVehiculo;
use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioNivelServicioEntrega;
use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioTipoCliente;
use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioTipoVehiculo;
use App\Modulos\Operaciones\Domain\Entity\ReglaDisponibilidadServicio;
use App\Modulos\Operaciones\Infrastructure\Persistence\Doctrine\RepositorioReglaDisponibilidadServicio;
use App\Modulos\Tarifas\Domain\Entity\ReglaTarifaCliente;
use App\Modulos\Tarifas\Domain\Entity\ReglaTarifaTransportista;
use App\Modulos\Tarifas\Infrastructure\Persistence\Doctrine\RepositorioReglaTarifaCliente;
use App\Modulos\Tarifas\Infrastructure\Persistence\Doctrine\RepositorioReglaTarifaTransportista;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:catalogos:cargar-base', description: 'Carga catalogos base para la demo inicial.')]
final class CargarCatalogosBaseCommand extends Command
{
    public function __construct(
        private readonly RepositorioTipoCliente $repositorioTipoCliente,
        private readonly RepositorioNivelServicioEntrega $repositorioNivelServicioEntrega,
        private readonly RepositorioTipoVehiculo $repositorioTipoVehiculo,
        private readonly RepositorioReglaDisponibilidadServicio $repositorioReglaDisponibilidadServicio,
        private readonly RepositorioReglaTarifaCliente $repositorioReglaTarifaCliente,
        private readonly RepositorioReglaTarifaTransportista $repositorioReglaTarifaTransportista,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (0 === $this->repositorioTipoCliente->count([])) {
            $this->entityManager->persist(new TipoCliente('Particular', 'PARTICULAR'));
            $this->entityManager->persist(new TipoCliente('Profesional', 'PROFESIONAL'));
        }

        if (0 === $this->repositorioNivelServicioEntrega->count([])) {
            $express = new NivelServicioEntrega('Express', 'EXPRESS');
            $express->reordenar(10);
            $standard = new NivelServicioEntrega('Standard', 'STANDARD');
            $standard->reordenar(20);
            $programado = new NivelServicioEntrega('Programado', 'PROGRAMADO');
            $programado->reordenar(30);

            $this->entityManager->persist($express);
            $this->entityManager->persist($standard);
            $this->entityManager->persist($programado);
        }

        if (0 === $this->repositorioTipoVehiculo->count([])) {
            $this->entityManager->persist(new TipoVehiculo('Patinete', 'PATINETE', 5000, 25000));
            $this->entityManager->persist(new TipoVehiculo('Moto', 'MOTO', 25000, 120000));
            $this->entityManager->persist(new TipoVehiculo('Coche', 'COCHE', 120000, 900000));
            $this->entityManager->persist(new TipoVehiculo('Furgoneta', 'FURGONETA', 500000, 4500000));
        }

        if (0 === $this->repositorioReglaDisponibilidadServicio->count([])) {
            $express = $this->repositorioNivelServicioEntrega->findOneBy(['codigo' => 'EXPRESS']);
            $standard = $this->repositorioNivelServicioEntrega->findOneBy(['codigo' => 'STANDARD']);
            $programado = $this->repositorioNivelServicioEntrega->findOneBy(['codigo' => 'PROGRAMADO']);

            if ($express instanceof NivelServicioEntrega) {
                $this->entityManager->persist(new ReglaDisponibilidadServicio($express, 5, 25000, 120000));
            }
            if ($standard instanceof NivelServicioEntrega) {
                $this->entityManager->persist(new ReglaDisponibilidadServicio($standard, 15, 60000, 900000));
            }
            if ($programado instanceof NivelServicioEntrega) {
                $this->entityManager->persist(new ReglaDisponibilidadServicio($programado, 40, 500000, 4500000));
            }
        }

        if (0 === $this->repositorioReglaTarifaCliente->count([])) {
            $particular = $this->repositorioTipoCliente->findOneBy(['codigo' => 'PARTICULAR']);
            $profesional = $this->repositorioTipoCliente->findOneBy(['codigo' => 'PROFESIONAL']);
            $express = $this->repositorioNivelServicioEntrega->findOneBy(['codigo' => 'EXPRESS']);
            $standard = $this->repositorioNivelServicioEntrega->findOneBy(['codigo' => 'STANDARD']);
            $programado = $this->repositorioNivelServicioEntrega->findOneBy(['codigo' => 'PROGRAMADO']);

            if ($particular instanceof TipoCliente && $express instanceof NivelServicioEntrega) {
                $this->entityManager->persist(new ReglaTarifaCliente($particular, $express, 0, 5, 1400));
            }
            if ($particular instanceof TipoCliente && $standard instanceof NivelServicioEntrega) {
                $this->entityManager->persist(new ReglaTarifaCliente($particular, $standard, 0, 10, 800));
            }
            if ($particular instanceof TipoCliente && $programado instanceof NivelServicioEntrega) {
                $this->entityManager->persist(new ReglaTarifaCliente($particular, $programado, 0, 10, 600));
            }
            if ($profesional instanceof TipoCliente && $standard instanceof NivelServicioEntrega) {
                $this->entityManager->persist(new ReglaTarifaCliente($profesional, $standard, 0, 10, 700));
            }
        }

        if (0 === $this->repositorioReglaTarifaTransportista->count([])) {
            $moto = $this->repositorioTipoVehiculo->findOneBy(['codigo' => 'MOTO']);
            $coche = $this->repositorioTipoVehiculo->findOneBy(['codigo' => 'COCHE']);
            $express = $this->repositorioNivelServicioEntrega->findOneBy(['codigo' => 'EXPRESS']);

            if ($moto instanceof TipoVehiculo && $express instanceof NivelServicioEntrega) {
                $this->entityManager->persist(new ReglaTarifaTransportista($moto, $express, 0, 5, 0, 25000, 0, 120000, 850, 6, 100));
            }
            if ($coche instanceof TipoVehiculo && $express instanceof NivelServicioEntrega) {
                $this->entityManager->persist(new ReglaTarifaTransportista($coche, $express, 0, 5, 0, 120000, 0, 900000, 1200, 10, 120));
            }
        }

        $this->entityManager->flush();

        $output->writeln('<info>Catalogos base cargados correctamente.</info>');

        return Command::SUCCESS;
    }
}
