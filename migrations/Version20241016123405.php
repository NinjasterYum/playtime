<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241016123405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE guest_reservation ADD terrain_id INT NOT NULL');
        $this->addSql('ALTER TABLE guest_reservation ADD CONSTRAINT FK_C5FDF2D78A2D8B41 FOREIGN KEY (terrain_id) REFERENCES terrain (id)');
        $this->addSql('CREATE INDEX IDX_C5FDF2D78A2D8B41 ON guest_reservation (terrain_id)');
        $this->addSql('ALTER TABLE reservation ADD terrain_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849558A2D8B41 FOREIGN KEY (terrain_id) REFERENCES terrain (id)');
        $this->addSql('CREATE INDEX IDX_42C849558A2D8B41 ON reservation (terrain_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE guest_reservation DROP FOREIGN KEY FK_C5FDF2D78A2D8B41');
        $this->addSql('DROP INDEX IDX_C5FDF2D78A2D8B41 ON guest_reservation');
        $this->addSql('ALTER TABLE guest_reservation DROP terrain_id');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849558A2D8B41');
        $this->addSql('DROP INDEX IDX_42C849558A2D8B41 ON reservation');
        $this->addSql('ALTER TABLE reservation DROP terrain_id');
    }
}
