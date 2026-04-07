<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260407192416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crear reglas de disponibilidad operativa por nivel de servicio';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reglas_disponibilidad_servicio (id UUID NOT NULL, distancia_maxima_km DOUBLE PRECISION NOT NULL, peso_maximo_gramos INT NOT NULL, volumen_maximo_cm3 INT NOT NULL, activa BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, nivel_servicio_entrega_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_80D1F3BEB0814AE9 ON reglas_disponibilidad_servicio (nivel_servicio_entrega_id)');
        $this->addSql('ALTER TABLE reglas_disponibilidad_servicio ADD CONSTRAINT FK_80D1F3BEB0814AE9 FOREIGN KEY (nivel_servicio_entrega_id) REFERENCES niveles_servicio_entrega (id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reglas_disponibilidad_servicio DROP CONSTRAINT FK_80D1F3BEB0814AE9');
        $this->addSql('DROP TABLE reglas_disponibilidad_servicio');
    }
}
