<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260323101356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alerte (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, unite VARCHAR(255) DEFAULT NULL, message_alerte LONGTEXT NOT NULL, date_alerte DATETIME NOT NULL, aquarium_id INT NOT NULL, INDEX IDX_3AE753A7051F3DE (aquarium_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE aquarium (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, type_eau VARCHAR(255) NOT NULL, temperature DOUBLE PRECISION NOT NULL, volume_litre DOUBLE PRECISION NOT NULL, derniere_maj DATETIME NOT NULL, dernier_changement_eau DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mesure (id INT AUTO_INCREMENT NOT NULL, date_saisie DATETIME NOT NULL, temperature DOUBLE PRECISION NOT NULL, ph DOUBLE PRECISION NOT NULL, chlore DOUBLE PRECISION DEFAULT NULL, gh INT DEFAULT NULL, kh INT DEFAULT NULL, valeur DOUBLE PRECISION DEFAULT NULL, nitrites DOUBLE PRECISION DEFAULT NULL, ammonium DOUBLE PRECISION DEFAULT NULL, aquarium_id INT NOT NULL, alerte_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_5F1B6E707051F3DE (aquarium_id), INDEX IDX_5F1B6E702C9BA629 (alerte_id), INDEX IDX_5F1B6E70A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mesure_historique (id INT AUTO_INCREMENT NOT NULL, date_action DATETIME NOT NULL, action VARCHAR(255) NOT NULL, details LONGTEXT NOT NULL, mesure_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_1D6B7E6243AB22FA (mesure_id), INDEX IDX_1D6B7E62A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE nourriture (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, aquarium_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_7447E6137051F3DE (aquarium_id), INDEX IDX_7447E613FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE poisson_inventaire (id INT AUTO_INCREMENT NOT NULL, espece_nom VARCHAR(255) NOT NULL, nombre INT NOT NULL, remarques LONGTEXT DEFAULT NULL, ph_ideal_min DOUBLE PRECISION NOT NULL, ph_ideal_max DOUBLE PRECISION NOT NULL, aquarium_id INT NOT NULL, INDEX IDX_ADA44BC57051F3DE (aquarium_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tache (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, priorite VARCHAR(255) NOT NULL, type_action VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, deadline DATETIME NOT NULL, date_completion DATETIME DEFAULT NULL, recurrence_jours INT DEFAULT NULL, aquarium_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_938720757051F3DE (aquarium_id), INDEX IDX_93872075FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE alerte ADD CONSTRAINT FK_3AE753A7051F3DE FOREIGN KEY (aquarium_id) REFERENCES aquarium (id)');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E707051F3DE FOREIGN KEY (aquarium_id) REFERENCES aquarium (id)');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E702C9BA629 FOREIGN KEY (alerte_id) REFERENCES alerte (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE mesure ADD CONSTRAINT FK_5F1B6E70A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE mesure_historique ADD CONSTRAINT FK_1D6B7E6243AB22FA FOREIGN KEY (mesure_id) REFERENCES mesure (id)');
        $this->addSql('ALTER TABLE mesure_historique ADD CONSTRAINT FK_1D6B7E62A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE nourriture ADD CONSTRAINT FK_7447E6137051F3DE FOREIGN KEY (aquarium_id) REFERENCES aquarium (id)');
        $this->addSql('ALTER TABLE nourriture ADD CONSTRAINT FK_7447E613FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE poisson_inventaire ADD CONSTRAINT FK_ADA44BC57051F3DE FOREIGN KEY (aquarium_id) REFERENCES aquarium (id)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_938720757051F3DE FOREIGN KEY (aquarium_id) REFERENCES aquarium (id)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alerte DROP FOREIGN KEY FK_3AE753A7051F3DE');
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E707051F3DE');
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E702C9BA629');
        $this->addSql('ALTER TABLE mesure DROP FOREIGN KEY FK_5F1B6E70A76ED395');
        $this->addSql('ALTER TABLE mesure_historique DROP FOREIGN KEY FK_1D6B7E6243AB22FA');
        $this->addSql('ALTER TABLE mesure_historique DROP FOREIGN KEY FK_1D6B7E62A76ED395');
        $this->addSql('ALTER TABLE nourriture DROP FOREIGN KEY FK_7447E6137051F3DE');
        $this->addSql('ALTER TABLE nourriture DROP FOREIGN KEY FK_7447E613FB88E14F');
        $this->addSql('ALTER TABLE poisson_inventaire DROP FOREIGN KEY FK_ADA44BC57051F3DE');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_938720757051F3DE');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075FB88E14F');
        $this->addSql('DROP TABLE alerte');
        $this->addSql('DROP TABLE aquarium');
        $this->addSql('DROP TABLE mesure');
        $this->addSql('DROP TABLE mesure_historique');
        $this->addSql('DROP TABLE nourriture');
        $this->addSql('DROP TABLE poisson_inventaire');
        $this->addSql('DROP TABLE tache');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
