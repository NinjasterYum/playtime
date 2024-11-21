<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241008180300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE terrain (id INT AUTO_INCREMENT NOT NULL, sport_company_id INT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, is_indoor TINYINT(1) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_C87653B1A4B385CB (sport_company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE terrain ADD CONSTRAINT FK_C87653B1A4B385CB FOREIGN KEY (sport_company_id) REFERENCES sport_company (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE terrain DROP FOREIGN KEY FK_C87653B1A4B385CB');
        $this->addSql('DROP TABLE terrain');
    }
}
