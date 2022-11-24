<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221123201234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Отношение адреса - файл';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F8193CB796C FOREIGN KEY (file_id) REFERENCES yafile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D4E6F8193CB796C ON address (file_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE address DROP CONSTRAINT FK_D4E6F8193CB796C');
        $this->addSql('DROP INDEX IDX_D4E6F8193CB796C');
    }
}
