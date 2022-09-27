<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220927152804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX corruption_location_idx');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE INDEX corruption_location_idx ON corruption (location)');
    }
}
