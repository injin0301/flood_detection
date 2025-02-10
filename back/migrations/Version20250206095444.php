<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206095444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE capteur (id SERIAL NOT NULL, piece_id INT DEFAULT NULL, humidite DOUBLE PRECISION NOT NULL, temperature DOUBLE PRECISION NOT NULL, niveau_eau DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5B4A1695C40FCFA8 ON capteur (piece_id)');
        $this->addSql('CREATE TABLE piece (id SERIAL NOT NULL, utilisateur_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_44CA0B23FB88E14F ON piece (utilisateur_id)');
        $this->addSql('CREATE TABLE utilisateur (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) NOT NULL, tel INT DEFAULT NULL, city VARCHAR(255) NOT NULL, zip_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON utilisateur (email)');
        $this->addSql('ALTER TABLE capteur ADD CONSTRAINT FK_5B4A1695C40FCFA8 FOREIGN KEY (piece_id) REFERENCES piece (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE piece ADD CONSTRAINT FK_44CA0B23FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE capteur DROP CONSTRAINT FK_5B4A1695C40FCFA8');
        $this->addSql('ALTER TABLE piece DROP CONSTRAINT FK_44CA0B23FB88E14F');
        $this->addSql('DROP TABLE capteur');
        $this->addSql('DROP TABLE piece');
        $this->addSql('DROP TABLE utilisateur');
    }
}
