<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220721161730 extends AbstractMigration
{
    private string $indexName = 'corruption_location_idx';

    public function getDescription(): string
    {
        return 'Index on points field (location)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX '. $this->indexName .' ON corruption USING GIST(location)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX '. $this->indexName);
    }
}
