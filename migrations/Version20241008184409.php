<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241008184409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE guest_reservation (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, sport_company_id INT NOT NULL, date_time DATETIME NOT NULL, client_first_name VARCHAR(255) NOT NULL, client_last_name VARCHAR(255) NOT NULL, client_email VARCHAR(255) NOT NULL, client_phone VARCHAR(255) DEFAULT NULL, INDEX IDX_C5FDF2D7ED5CA9E6 (service_id), INDEX IDX_C5FDF2D7A4B385CB (sport_company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE guest_reservation ADD CONSTRAINT FK_C5FDF2D7ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE guest_reservation ADD CONSTRAINT FK_C5FDF2D7A4B385CB FOREIGN KEY (sport_company_id) REFERENCES sport_company (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE guest_reservation DROP FOREIGN KEY FK_C5FDF2D7ED5CA9E6');
        $this->addSql('ALTER TABLE guest_reservation DROP FOREIGN KEY FK_C5FDF2D7A4B385CB');
        $this->addSql('DROP TABLE guest_reservation');
    }
}
