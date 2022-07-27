<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220727210512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Индекс в основных данных над идентификаторами точек';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX corruption_point_id_idx ON corruption (point_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX corruption_point_id_idx');
    }
}
