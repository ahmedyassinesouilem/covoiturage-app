<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241204093701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, conducteur_id INT DEFAULT NULL, passager_id INT DEFAULT NULL, objet VARCHAR(1000) NOT NULL, INDEX IDX_CE606404F16F4AC6 (conducteur_id), INDEX IDX_CE60640471A51189 (passager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404F16F4AC6 FOREIGN KEY (conducteur_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE60640471A51189 FOREIGN KEY (passager_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user ADD alerte INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404F16F4AC6');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE60640471A51189');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('ALTER TABLE `user` DROP alerte');
    }
}
