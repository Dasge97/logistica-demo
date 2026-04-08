<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260407213000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ampliar tarifas transportista con rangos de distancia, peso y volumen';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reglas_tarifa_transportista ADD distancia_minima_km DOUBLE PRECISION DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE reglas_tarifa_transportista ADD distancia_maxima_km DOUBLE PRECISION DEFAULT 9999 NOT NULL');
        $this->addSql('ALTER TABLE reglas_tarifa_transportista ADD peso_minimo_gramos INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE reglas_tarifa_transportista ADD peso_maximo_gramos INT DEFAULT 2147483647 NOT NULL');
        $this->addSql('ALTER TABLE reglas_tarifa_transportista ADD volumen_minimo_cm3 INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE reglas_tarifa_transportista ADD volumen_maximo_cm3 INT DEFAULT 2147483647 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reglas_tarifa_transportista DROP distancia_minima_km');
        $this->addSql('ALTER TABLE reglas_tarifa_transportista DROP distancia_maxima_km');
        $this->addSql('ALTER TABLE reglas_tarifa_transportista DROP peso_minimo_gramos');
        $this->addSql('ALTER TABLE reglas_tarifa_transportista DROP peso_maximo_gramos');
        $this->addSql('ALTER TABLE reglas_tarifa_transportista DROP volumen_minimo_cm3');
        $this->addSql('ALTER TABLE reglas_tarifa_transportista DROP volumen_maximo_cm3');
    }
}
