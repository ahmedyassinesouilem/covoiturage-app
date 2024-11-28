<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241127192827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covoiturage_cond ADD isdemande TINYINT(1) NOT NULL, ADD is_valid TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD covoiturage_cond_id INT DEFAULT NULL, CHANGE date_naissance date_naissance DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AF41B9F7 FOREIGN KEY (covoiturage_cond_id) REFERENCES covoiturage_cond (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649AF41B9F7 ON user (covoiturage_cond_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE covoiturage_cond DROP isdemande, DROP is_valid');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649AF41B9F7');
        $this->addSql('DROP INDEX IDX_8D93D649AF41B9F7 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP covoiturage_cond_id, CHANGE date_naissance date_naissance DATE NOT NULL');
    }
}
