<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250224135921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hex_text_protect (id SERIAL NOT NULL, utilisateur_id INT DEFAULT NULL, pass_phrase VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E09DF4D0FB88E14F ON hex_text_protect (utilisateur_id)');
        $this->addSql('ALTER TABLE hex_text_protect ADD CONSTRAINT FK_E09DF4D0FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE hex_text_protect DROP CONSTRAINT FK_E09DF4D0FB88E14F');
        $this->addSql('DROP TABLE hex_text_protect');
    }
}
