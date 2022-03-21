<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211202145558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formation CHANGE nbre_heures nbres_heures INT NOT NULL');
        $this->addSql('ALTER TABLE inscription ADD formation_id INT DEFAULT NULL, ADD employe_id INT DEFAULT NULL, CHANGE statut statut VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D65200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D61B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('CREATE INDEX IDX_5E90F6D65200282E ON inscription (formation_id)');
        $this->addSql('CREATE INDEX IDX_5E90F6D61B65292 ON inscription (employe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formation CHANGE nbres_heures nbre_heures INT NOT NULL');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D65200282E');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_5E90F6D61B65292');
        $this->addSql('DROP INDEX IDX_5E90F6D65200282E ON inscription');
        $this->addSql('DROP INDEX IDX_5E90F6D61B65292 ON inscription');
        $this->addSql('ALTER TABLE inscription DROP formation_id, DROP employe_id, CHANGE statut statut VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
