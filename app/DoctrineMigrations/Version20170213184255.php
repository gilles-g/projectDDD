<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Prooph\EventStore\Snapshot\Adapter\Doctrine\Schema\SnapshotStoreSchema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170213184255 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if (class_exists(SnapshotStoreSchema::class)) {
            SnapshotStoreSchema::create($schema, 'publisher_snapshot');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        if (class_exists(SnapshotStoreSchema::class)) {
            SnapshotStoreSchema::drop($schema, 'publisher_snapshot');
        }
    }
}
