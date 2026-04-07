<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260407192142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crear tablas de pedidos y lineas de pedido con soporte de metricas';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lineas_pedido (id UUID NOT NULL, descripcion VARCHAR(180) NOT NULL, cantidad INT NOT NULL, peso_unitario_gramos INT NOT NULL, volumen_unitario_cm3 INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pedido_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_D2DE2C134854653A ON lineas_pedido (pedido_id)');
        $this->addSql('CREATE TABLE pedidos (id UUID NOT NULL, referencia VARCHAR(32) NOT NULL, estado VARCHAR(255) NOT NULL, nombre_cliente VARCHAR(140) NOT NULL, telefono_cliente VARCHAR(40) NOT NULL, distancia_km DOUBLE PRECISION NOT NULL, peso_total_gramos INT NOT NULL, volumen_total_cm3 INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tipo_cliente_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_6716CCAA4FF54C79 ON pedidos (tipo_cliente_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_pedido_referencia ON pedidos (referencia)');
        $this->addSql('ALTER TABLE lineas_pedido ADD CONSTRAINT FK_D2DE2C134854653A FOREIGN KEY (pedido_id) REFERENCES pedidos (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAA4FF54C79 FOREIGN KEY (tipo_cliente_id) REFERENCES tipos_cliente (id) ON DELETE RESTRICT NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lineas_pedido DROP CONSTRAINT FK_D2DE2C134854653A');
        $this->addSql('ALTER TABLE pedidos DROP CONSTRAINT FK_6716CCAA4FF54C79');
        $this->addSql('DROP TABLE lineas_pedido');
        $this->addSql('DROP TABLE pedidos');
    }
}
