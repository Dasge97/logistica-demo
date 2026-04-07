<?php

namespace App\Core\Usuario\Infrastructure\Command;

use App\Core\Usuario\Domain\Entity\Usuario;
use App\Core\Usuario\Domain\Enum\RolUsuario;
use App\Core\Usuario\Infrastructure\Persistence\Doctrine\RepositorioUsuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:usuario:crear-admin', description: 'Crea el usuario administrador inicial del sistema.')]
final class CrearUsuarioAdminCommand extends Command
{
    public function __construct(
        private readonly RepositorioUsuario $repositorioUsuario,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'Email del administrador', 'admin@logistica.local')
            ->addArgument('nombre', InputArgument::OPTIONAL, 'Nombre completo del administrador', 'Administrador')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password del administrador', 'admin1234');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (string) $input->getArgument('email');
        $nombre = (string) $input->getArgument('nombre');
        $password = (string) $input->getArgument('password');

        if ($this->repositorioUsuario->buscarPorEmail($email) instanceof Usuario) {
            $output->writeln('<comment>Ya existe un usuario con ese email.</comment>');

            return Command::SUCCESS;
        }

        $usuario = new Usuario($email, $nombre, [RolUsuario::ADMIN->value]);
        $usuario->establecerPassword($this->passwordHasher->hashPassword($usuario, $password));

        $this->entityManager->persist($usuario);
        $this->entityManager->flush();

        $output->writeln('<info>Usuario administrador creado correctamente.</info>');

        return Command::SUCCESS;
    }
}
