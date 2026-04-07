<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260407195219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crear snapshots inmutables de tarificacion por pedido';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE snapshots_tarificacion_pedido (id UUID NOT NULL, nombre_servicio VARCHAR(80) NOT NULL, nombre_vehiculo VARCHAR(80) NOT NULL, distancia_km DOUBLE PRECISION NOT NULL, peso_total_gramos INT NOT NULL, volumen_total_cm3 INT NOT NULL, precio_cliente_centimos INT NOT NULL, coste_logistico_centimos INT NOT NULL, margen_centimos INT NOT NULL, explicacion_json JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, pedido_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_snapshot_pedido ON snapshots_tarificacion_pedido (pedido_id)');
        $this->addSql('ALTER TABLE snapshots_tarificacion_pedido ADD CONSTRAINT FK_976945F24854653A FOREIGN KEY (pedido_id) REFERENCES pedidos (id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE snapshots_tarificacion_pedido DROP CONSTRAINT FK_976945F24854653A');
        $this->addSql('DROP TABLE snapshots_tarificacion_pedido');
    }
}
