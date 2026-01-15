<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260115110954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY `FK_5F1B6E70FB88E14F`');
        $this->addSql('DROP INDEX IDX_5F1B6E70FB88E14F ON mesure');
        $this->addSql('ALTER TABLE mesure DROP utilisateur_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mesure ADD utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT `FK_5F1B6E70FB88E14F` FOREIGN KEY (utilisateur_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5F1B6E70FB88E14F ON mesure (utilisateur_id)');
    }
}
