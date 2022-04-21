<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220419195253 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Статус обработки файла';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE yafile ADD status SMALLINT DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE yafile DROP status');
    }
}
