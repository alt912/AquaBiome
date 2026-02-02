<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260117104047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alerte CHANGE unite unite VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE aquarium CHANGE dernier_changement_eau dernier_changement_eau DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY `FK_5F1B6E70FB88E14F`');
        $this->addSql('DROP INDEX IDX_5F1B6E70FB88E14F ON mesure');
        $this->addSql('ALTER TABLE mesure ADD temperature DOUBLE PRECISION NOT NULL, ADD ph DOUBLE PRECISION NOT NULL, ADD chlore DOUBLE PRECISION DEFAULT NULL, ADD gh INT DEFAULT NULL, ADD kh INT DEFAULT NULL, ADD nitrites DOUBLE PRECISION DEFAULT NULL, ADD ammonium DOUBLE PRECISION DEFAULT NULL, DROP utilisateur_id, CHANGE valeur valeur DOUBLE PRECISION DEFAULT NULL, CHANGE alerte_id alerte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP nom, DROP prenom');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alerte CHANGE unite unite VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE aquarium CHANGE dernier_changement_eau dernier_changement_eau DATETIME NOT NULL');
        $this->addSql('ALTER TABLE mesure ADD utilisateur_id INT NOT NULL, DROP temperature, DROP ph, DROP chlore, DROP gh, DROP kh, DROP nitrites, DROP ammonium, CHANGE valeur valeur DOUBLE PRECISION NOT NULL, CHANGE alerte_id alerte_id INT NOT NULL');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT `FK_5F1B6E70FB88E14F` FOREIGN KEY (utilisateur_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5F1B6E70FB88E14F ON mesure (utilisateur_id)');
        $this->addSql('ALTER TABLE `user` ADD nom TEXT DEFAULT NULL, ADD prenom TEXT NOT NULL');
    }
}
