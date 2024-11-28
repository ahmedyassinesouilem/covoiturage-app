<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241128173551 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, passager_id INT DEFAULT NULL, covoiturage_id INT DEFAULT NULL, etat VARCHAR(255) NOT NULL, INDEX IDX_42C8495571A51189 (passager_id), INDEX IDX_42C8495562671590 (covoiturage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495571A51189 FOREIGN KEY (passager_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495562671590 FOREIGN KEY (covoiturage_id) REFERENCES covoiturage_cond (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495571A51189');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495562671590');
        $this->addSql('DROP TABLE reservation');
    }
}
