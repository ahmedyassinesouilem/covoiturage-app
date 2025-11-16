<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241204054823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE covoiturage_cond (id INT AUTO_INCREMENT NOT NULL, conducteur_id INT DEFAULT NULL, vehicule_id INT NOT NULL, villede_depart VARCHAR(255) NOT NULL, ville_arriver VARCHAR(255) NOT NULL, placede_rencontre VARCHAR(255) NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_staurer TINYINT(1) NOT NULL, nbplace INT NOT NULL, INDEX IDX_8F0CF284F16F4AC6 (conducteur_id), UNIQUE INDEX UNIQ_8F0CF2844A4A3511 (vehicule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, passager_id INT DEFAULT NULL, covoiturage_id INT DEFAULT NULL, etat VARCHAR(255) NOT NULL, INDEX IDX_42C8495571A51189 (passager_id), INDEX IDX_42C8495562671590 (covoiturage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, covoiturage_cond_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, date_naissance DATE DEFAULT NULL, user_type VARCHAR(255) NOT NULL, numtel INT DEFAULT NULL, num_permis INT DEFAULT NULL, adress VARCHAR(255) DEFAULT NULL, INDEX IDX_8D93D649AF41B9F7 (covoiturage_cond_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicule (id INT AUTO_INCREMENT NOT NULL, conducteur_id INT NOT NULL, type VARCHAR(255) NOT NULL, nbpplace INT NOT NULL, model VARCHAR(255) DEFAULT NULL, INDEX IDX_292FFF1DF16F4AC6 (conducteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE covoiturage_cond ADD CONSTRAINT FK_8F0CF284F16F4AC6 FOREIGN KEY (conducteur_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE covoiturage_cond ADD CONSTRAINT FK_8F0CF2844A4A3511 FOREIGN KEY (vehicule_id) REFERENCES vehicule (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495571A51189 FOREIGN KEY (passager_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495562671590 FOREIGN KEY (covoiturage_id) REFERENCES covoiturage_cond (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649AF41B9F7 FOREIGN KEY (covoiturage_cond_id) REFERENCES covoiturage_cond (id)');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1DF16F4AC6 FOREIGN KEY (conducteur_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covoiturage_cond DROP FOREIGN KEY FK_8F0CF284F16F4AC6');
        $this->addSql('ALTER TABLE covoiturage_cond DROP FOREIGN KEY FK_8F0CF2844A4A3511');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495571A51189');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495562671590');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649AF41B9F7');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1DF16F4AC6');
        $this->addSql('DROP TABLE covoiturage_cond');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE vehicule');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
