<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260114095302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE aquarium ADD utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE aquarium ADD CONSTRAINT FK_2BBA6EB2FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2BBA6EB2FB88E14F ON aquarium (utilisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE aquarium DROP FOREIGN KEY FK_2BBA6EB2FB88E14F');
        $this->addSql('DROP INDEX IDX_2BBA6EB2FB88E14F ON aquarium');
        $this->addSql('ALTER TABLE aquarium DROP utilisateur_id');
    }
}
