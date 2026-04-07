<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260407193102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajustar unidades de tipos de vehiculo a gramos y centimetros cubicos';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tipos_vehiculo ADD volumen_maximo_cm3 INT DEFAULT 0 NOT NULL');
        $this->addSql('UPDATE tipos_vehiculo SET volumen_maximo_cm3 = ROUND(volumen_maximo_m3)::INT');
        $this->addSql('ALTER TABLE tipos_vehiculo DROP volumen_maximo_m3');
        $this->addSql('ALTER TABLE tipos_vehiculo RENAME COLUMN peso_maximo_kg TO peso_maximo_gramos');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tipos_vehiculo ADD peso_maximo_kg INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE tipos_vehiculo ADD volumen_maximo_m3 DOUBLE PRECISION DEFAULT 0 NOT NULL');
        $this->addSql('UPDATE tipos_vehiculo SET peso_maximo_kg = peso_maximo_gramos, volumen_maximo_m3 = volumen_maximo_cm3');
        $this->addSql('ALTER TABLE tipos_vehiculo DROP volumen_maximo_cm3');
        $this->addSql('ALTER TABLE tipos_vehiculo DROP peso_maximo_gramos');
    }
}
