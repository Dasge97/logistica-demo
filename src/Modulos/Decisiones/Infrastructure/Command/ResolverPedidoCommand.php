<?php

namespace App\Modulos\Decisiones\Infrastructure\Command;

use App\Modulos\Decisiones\Application\ResolutorOpcionesEntrega;
use App\Modulos\Pedidos\Infrastructure\Persistence\Doctrine\RepositorioPedido;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(name: 'app:pedidos:resolver', description: 'Resuelve las opciones de entrega disponibles para un pedido.')]
final class ResolverPedidoCommand extends Command
{
    public function __construct(
        private readonly RepositorioPedido $repositorioPedido,
        private readonly ResolutorOpcionesEntrega $resolutorOpcionesEntrega,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('pedidoId', InputArgument::REQUIRED, 'Identificador del pedido');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pedido = $this->repositorioPedido->find(Uuid::fromString((string) $input->getArgument('pedidoId')));
        if (null === $pedido) {
            $output->writeln('<error>Pedido no encontrado.</error>');

            return Command::FAILURE;
        }

        $resultado = $this->resolutorOpcionesEntrega->resolverParaPedido($pedido);
        $output->writeln(sprintf('<info>Pedido %s</info>', $pedido->getReferencia()));

        foreach ($resultado->opciones as $opcion) {
            $output->writeln(sprintf('- %s | Precio cliente: %d centimos', $opcion->nivelServicioEntrega->getNombre(), $opcion->precioClienteCentimos));

            foreach ($opcion->vehiculos as $vehiculo) {
                $output->writeln(sprintf('  * %s -> coste %d centimos', $vehiculo->tipoVehiculo->getNombre(), $vehiculo->costeCentimos));
            }

            if (null !== $opcion->vehiculoOptimo) {
                $output->writeln(sprintf('  > Vehiculo optimo: %s', $opcion->vehiculoOptimo->tipoVehiculo->getNombre()));
            }
        }

        foreach ($resultado->descartes as $descarte) {
            $output->writeln(sprintf('- %s descartado: %s', $descarte->nivelServicioEntrega->getNombre(), $descarte->motivo));
        }

        return Command::SUCCESS;
    }
}
