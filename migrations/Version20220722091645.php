<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220722091645 extends AbstractMigration
{
    /**
     * Сохранение всех точек без дубликатов
     * INSERT INTO points_list (location, location_latitude, location_longitude) (SELECT location, lat, lng FROM (SELECT DISTINCT ON(location[0], location[1]) location, location[0] AS lat, location[1] AS lng FROM corruption) AS SRC)
     * 
     * Сопоставление основных данных и списка точек (внешний ключ corruption.point_id)
     * WITH S1 AS (SELECT id, location FROM points_list) UPDATE corruption AS C SET point_id = S1.id FROM S1 WHERE C.location ~= S1.location
     */

    public function getDescription(): string
    {
        return 'Add point table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE points_list (id SERIAL NOT NULL, location POINT NOT NULL, location_latitude DOUBLE PRECISION NOT NULL, location_longitude DOUBLE PRECISION NOT NULL)');

        $this->addSql('ALTER TABLE corruption ADD COLUMN point_id INTEGER');
        $this->addSql('CREATE INDEX points_list_location_idx ON points_list USING GIST(location)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE corruption DROP point_id');
        $this->addSql('DROP INDEX points_list_location_idx');

        $this->addSql('DROP SEQUENCE points_list_id_seq CASCADE');
        $this->addSql('DROP TABLE points_list');
    }
}
