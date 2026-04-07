<?php

namespace App\Modulos\Pedidos\Infrastructure\Command;

use App\Modulos\Decisiones\Application\ResolutorOpcionesEntrega;
use App\Modulos\Decisiones\Application\ServicioSnapshotPedido;
use App\Modulos\Pedidos\Application\CalculadoraMetricasPedido;
use App\Modulos\Pedidos\Domain\Entity\LineaPedido;
use App\Modulos\Pedidos\Domain\Entity\Pedido;
use App\Modulos\Pedidos\Infrastructure\Persistence\Doctrine\RepositorioPedido;
use App\Modulos\Catalogos\Infrastructure\Persistence\Doctrine\RepositorioTipoCliente;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:pedidos:cargar-demo', description: 'Carga pedidos demo para validar la v1 visualmente.')]
final class CargarPedidosDemoCommand extends Command
{
    public function __construct(
        private readonly RepositorioPedido $repositorioPedido,
        private readonly RepositorioTipoCliente $repositorioTipoCliente,
        private readonly EntityManagerInterface $entityManager,
        private readonly CalculadoraMetricasPedido $calculadoraMetricasPedido,
        private readonly ResolutorOpcionesEntrega $resolutorOpcionesEntrega,
        private readonly ServicioSnapshotPedido $servicioSnapshotPedido,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $particular = $this->repositorioTipoCliente->findOneBy(['codigo' => 'PARTICULAR']);
        $profesional = $this->repositorioTipoCliente->findOneBy(['codigo' => 'PROFESIONAL']);

        if (null === $particular || null === $profesional) {
            $output->writeln('<error>Primero debes cargar los catalogos base.</error>');

            return Command::FAILURE;
        }

        $escenarios = [
            [
                'referencia' => 'PED-DEMO-EXP-001',
                'cliente' => 'Cliente Express',
                'telefono' => '600100100',
                'tipoCliente' => $particular,
                'distanciaKm' => 3,
                'lineas' => [
                    ['descripcion' => 'Caja ligera', 'cantidad' => 2, 'peso' => 2000, 'volumen' => 8000],
                ],
                'confirmar' => true,
            ],
            [
                'referencia' => 'PED-DEMO-STD-001',
                'cliente' => 'Cliente Standard',
                'telefono' => '600200200',
                'tipoCliente' => $profesional,
                'distanciaKm' => 7,
                'lineas' => [
                    ['descripcion' => 'Material mediano', 'cantidad' => 1, 'peso' => 12000, 'volumen' => 40000],
                ],
                'confirmar' => false,
            ],
            [
                'referencia' => 'PED-DEMO-PESO-001',
                'cliente' => 'Cliente Pesado',
                'telefono' => '600300300',
                'tipoCliente' => $particular,
                'distanciaKm' => 8,
                'lineas' => [
                    ['descripcion' => 'Carga pesada', 'cantidad' => 1, 'peso' => 40000, 'volumen' => 120000],
                ],
                'confirmar' => false,
            ],
        ];

        foreach ($escenarios as $escenario) {
            if ($this->repositorioPedido->findOneBy(['referencia' => $escenario['referencia']]) instanceof Pedido) {
                continue;
            }

            $pedido = new Pedido($escenario['referencia'], $escenario['cliente'], $escenario['telefono'], $escenario['tipoCliente']);
            $pedido->setDistanciaKm($escenario['distanciaKm']);

            foreach ($escenario['lineas'] as $linea) {
                $lineaPedido = new LineaPedido($pedido, $linea['descripcion'], $linea['cantidad'], $linea['peso'], $linea['volumen']);
                $pedido->agregarLinea($lineaPedido);
                $this->entityManager->persist($lineaPedido);
            }

            $this->calculadoraMetricasPedido->recalcular($pedido);
            $this->entityManager->persist($pedido);

            if (true === $escenario['confirmar']) {
                $resultado = $this->resolutorOpcionesEntrega->resolverParaPedido($pedido);
                $opcion = $resultado->opciones[0] ?? null;
                if (null !== $opcion && null !== $opcion->vehiculoOptimo && null !== $opcion->getMargenCentimos()) {
                    $pedido->asignarDecisionEntrega(
                        $opcion->nivelServicioEntrega,
                        $opcion->vehiculoOptimo->tipoVehiculo,
                        $opcion->precioClienteCentimos,
                        $opcion->vehiculoOptimo->costeCentimos,
                        $opcion->getMargenCentimos(),
                    );
                    $pedido->confirmar();
                    $this->servicioSnapshotPedido->crearSnapshot($pedido, $resultado, $opcion);
                }
            }
        }

        $this->entityManager->flush();
        $output->writeln('<info>Pedidos demo cargados correctamente.</info>');

        return Command::SUCCESS;
    }
}
