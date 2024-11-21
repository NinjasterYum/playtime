<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241007074236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company_image (id INT AUTO_INCREMENT NOT NULL, sport_company_id INT NOT NULL, filename VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_82CCA63AA4B385CB (sport_company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company_image ADD CONSTRAINT FK_82CCA63AA4B385CB FOREIGN KEY (sport_company_id) REFERENCES sport_company (id)');
        $this->addSql('ALTER TABLE sport_company ADD postal_code VARCHAR(255) NOT NULL, ADD city VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company_image DROP FOREIGN KEY FK_82CCA63AA4B385CB');
        $this->addSql('DROP TABLE company_image');
        $this->addSql('ALTER TABLE sport_company DROP postal_code, DROP city');
    }
}
