<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260407192822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crear reglas de tarifas cliente y tarifas transportista';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reglas_tarifa_cliente (id UUID NOT NULL, distancia_desde_km DOUBLE PRECISION NOT NULL, distancia_hasta_km DOUBLE PRECISION NOT NULL, precio_cliente_centimos INT NOT NULL, activa BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tipo_cliente_id UUID NOT NULL, nivel_servicio_entrega_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_3A264EA74FF54C79 ON reglas_tarifa_cliente (tipo_cliente_id)');
        $this->addSql('CREATE INDEX IDX_3A264EA7B0814AE9 ON reglas_tarifa_cliente (nivel_servicio_entrega_id)');
        $this->addSql('CREATE TABLE reglas_tarifa_transportista (id UUID NOT NULL, precio_base_centimos INT NOT NULL, distancia_incluida_km DOUBLE PRECISION NOT NULL, precio_km_extra_centimos INT NOT NULL, activa BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, tipo_vehiculo_id UUID NOT NULL, nivel_servicio_entrega_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_C6B1EF4010D3FB8D ON reglas_tarifa_transportista (tipo_vehiculo_id)');
        $this->addSql('CREATE INDEX IDX_C6B1EF40B0814AE9 ON reglas_tarifa_transportista (nivel_servicio_entrega_id)');
        $this->addSql('ALTER TABLE reglas_tarifa_cliente ADD CONSTRAINT FK_3A264EA74FF54C79 FOREIGN KEY (tipo_cliente_id) REFERENCES tipos_cliente (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE reglas_tarifa_cliente ADD CONSTRAINT FK_3A264EA7B0814AE9 FOREIGN KEY (nivel_servicio_entrega_id) REFERENCES niveles_servicio_entrega (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE reglas_tarifa_transportista ADD CONSTRAINT FK_C6B1EF4010D3FB8D FOREIGN KEY (tipo_vehiculo_id) REFERENCES tipos_vehiculo (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE reglas_tarifa_transportista ADD CONSTRAINT FK_C6B1EF40B0814AE9 FOREIGN KEY (nivel_servicio_entrega_id) REFERENCES niveles_servicio_entrega (id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reglas_tarifa_cliente DROP CONSTRAINT FK_3A264EA74FF54C79');
        $this->addSql('ALTER TABLE reglas_tarifa_cliente DROP CONSTRAINT FK_3A264EA7B0814AE9');
        $this->addSql('ALTER TABLE reglas_tarifa_transportista DROP CONSTRAINT FK_C6B1EF4010D3FB8D');
        $this->addSql('ALTER TABLE reglas_tarifa_transportista DROP CONSTRAINT FK_C6B1EF40B0814AE9');
        $this->addSql('DROP TABLE reglas_tarifa_cliente');
        $this->addSql('DROP TABLE reglas_tarifa_transportista');
    }
}
