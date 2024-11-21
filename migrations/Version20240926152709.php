<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240926152709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, standard_user_id INT NOT NULL, sport_company_id INT NOT NULL, service_id INT NOT NULL, date_time DATETIME NOT NULL, status VARCHAR(20) NOT NULL, INDEX IDX_42C849558CCB3DC3 (standard_user_id), INDEX IDX_42C84955A4B385CB (sport_company_id), INDEX IDX_42C84955ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL, sport_company_id INT NOT NULL, day_of_week VARCHAR(10) NOT NULL, opening_time TIME NOT NULL, closing_time TIME NOT NULL, INDEX IDX_5A3811FBA4B385CB (sport_company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, sport_company_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_E19D9AD2A4B385CB (sport_company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sport_company (id INT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, is_subscribed TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE standard_user (id INT NOT NULL, address VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, phone_number VARCHAR(20) NOT NULL, type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849558CCB3DC3 FOREIGN KEY (standard_user_id) REFERENCES standard_user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A4B385CB FOREIGN KEY (sport_company_id) REFERENCES sport_company (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FBA4B385CB FOREIGN KEY (sport_company_id) REFERENCES sport_company (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2A4B385CB FOREIGN KEY (sport_company_id) REFERENCES sport_company (id)');
        $this->addSql('ALTER TABLE sport_company ADD CONSTRAINT FK_EE0A45E5BF396750 FOREIGN KEY (id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE standard_user ADD CONSTRAINT FK_92BA3CF4BF396750 FOREIGN KEY (id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849558CCB3DC3');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A4B385CB');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955ED5CA9E6');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FBA4B385CB');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2A4B385CB');
        $this->addSql('ALTER TABLE sport_company DROP FOREIGN KEY FK_EE0A45E5BF396750');
        $this->addSql('ALTER TABLE standard_user DROP FOREIGN KEY FK_92BA3CF4BF396750');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE sport_company');
        $this->addSql('DROP TABLE standard_user');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
