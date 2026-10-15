<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260506093442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covoiturage_cond DROP INDEX UNIQ_8F0CF2844A4A3511, ADD INDEX IDX_8F0CF2844A4A3511 (vehicule_id)');
        $this->addSql('ALTER TABLE covoiturage_cond CHANGE vehicule_id vehicule_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covoiturage_cond DROP INDEX IDX_8F0CF2844A4A3511, ADD UNIQUE INDEX UNIQ_8F0CF2844A4A3511 (vehicule_id)');
        $this->addSql('ALTER TABLE covoiturage_cond CHANGE vehicule_id vehicule_id INT NOT NULL');
    }
}
