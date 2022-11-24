<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221121131108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE yafile ADD yuser_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE yafile ADD file_type INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE yafile ADD CONSTRAINT FK_94664A5BEC3F5939 FOREIGN KEY (yuser_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_94664A5BEC3F5939 ON yafile (yuser_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE yafile DROP CONSTRAINT FK_94664A5BEC3F5939');
        $this->addSql('DROP INDEX IDX_94664A5BEC3F5939');
        $this->addSql('ALTER TABLE yafile DROP yuser_id');
        $this->addSql('ALTER TABLE yafile DROP file_type');
    }
}
