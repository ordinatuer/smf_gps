<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220425210910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Увеличение размера нескольких полей для Yafile';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE corruption ALTER address_house TYPE VARCHAR(128)');
        $this->addSql('ALTER TABLE corruption ALTER address_entrance TYPE VARCHAR(128)');
        $this->addSql('ALTER TABLE corruption ALTER address_floor TYPE VARCHAR(128)');
        $this->addSql('ALTER TABLE corruption ALTER address_office TYPE VARCHAR(128)');
        $this->addSql('ALTER TABLE corruption ALTER address_doorcode TYPE VARCHAR(128)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE corruption ALTER address_house TYPE VARCHAR(32)');
        $this->addSql('ALTER TABLE corruption ALTER address_entrance TYPE VARCHAR(32)');
        $this->addSql('ALTER TABLE corruption ALTER address_floor TYPE VARCHAR(32)');
        $this->addSql('ALTER TABLE corruption ALTER address_office TYPE VARCHAR(32)');
        $this->addSql('ALTER TABLE corruption ALTER address_doorcode TYPE VARCHAR(32)');
    }
}
