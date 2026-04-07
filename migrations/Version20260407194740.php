<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260407194740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Anadir seleccion de servicio y vehiculo al pedido confirmado';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pedidos ADD precio_cliente_centimos INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pedidos ADD coste_logistico_centimos INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pedidos ADD margen_centimos INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pedidos ADD servicio_elegido_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE pedidos ADD vehiculo_elegido_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAA20472405 FOREIGN KEY (servicio_elegido_id) REFERENCES niveles_servicio_entrega (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAAB72D0FE0 FOREIGN KEY (vehiculo_elegido_id) REFERENCES tipos_vehiculo (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_6716CCAA20472405 ON pedidos (servicio_elegido_id)');
        $this->addSql('CREATE INDEX IDX_6716CCAAB72D0FE0 ON pedidos (vehiculo_elegido_id)');
        $this->addSql('ALTER TABLE tipos_vehiculo ALTER volumen_maximo_cm3 DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pedidos DROP CONSTRAINT FK_6716CCAA20472405');
        $this->addSql('ALTER TABLE pedidos DROP CONSTRAINT FK_6716CCAAB72D0FE0');
        $this->addSql('DROP INDEX IDX_6716CCAA20472405');
        $this->addSql('DROP INDEX IDX_6716CCAAB72D0FE0');
        $this->addSql('ALTER TABLE pedidos DROP precio_cliente_centimos');
        $this->addSql('ALTER TABLE pedidos DROP coste_logistico_centimos');
        $this->addSql('ALTER TABLE pedidos DROP margen_centimos');
        $this->addSql('ALTER TABLE pedidos DROP servicio_elegido_id');
        $this->addSql('ALTER TABLE pedidos DROP vehiculo_elegido_id');
        $this->addSql('ALTER TABLE tipos_vehiculo ALTER volumen_maximo_cm3 SET DEFAULT 0');
    }
}
