<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260407191419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crear catalogos base de tipos de cliente, niveles de servicio y tipos de vehiculo';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE niveles_servicio_entrega (id UUID NOT NULL, nombre VARCHAR(80) NOT NULL, codigo VARCHAR(40) NOT NULL, activo BOOLEAN DEFAULT true NOT NULL, orden_visual INT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_nivel_servicio_codigo ON niveles_servicio_entrega (codigo)');
        $this->addSql('CREATE TABLE tipos_cliente (id UUID NOT NULL, nombre VARCHAR(80) NOT NULL, codigo VARCHAR(40) NOT NULL, activo BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_tipo_cliente_codigo ON tipos_cliente (codigo)');
        $this->addSql('CREATE TABLE tipos_vehiculo (id UUID NOT NULL, nombre VARCHAR(80) NOT NULL, codigo VARCHAR(40) NOT NULL, peso_maximo_kg INT NOT NULL, volumen_maximo_m3 DOUBLE PRECISION NOT NULL, activo BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_tipo_vehiculo_codigo ON tipos_vehiculo (codigo)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE niveles_servicio_entrega');
        $this->addSql('DROP TABLE tipos_cliente');
        $this->addSql('DROP TABLE tipos_vehiculo');
    }
}
