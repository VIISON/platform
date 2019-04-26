<?php declare(strict_types=1);

namespace Shopware\Core\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1536233100MediaDefaultFolder extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1536233100;
    }

    public function update(Connection $connection): void
    {
        $connection->exec('
            CREATE TABLE `media_default_folder` (
              `id` BINARY(16) NOT NULL,
              `media_folder_id` BINARY(16),
              `association_fields` JSON NOT NULL,
              `entity` VARCHAR(255) NOT NULL,
              `attributes` JSON NULL,
              `created_at` DATETIME(3) NOT NULL,
              `updated_at` DATETIME(3) NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `uniq.entity` (`entity`),
              UNIQUE KEY `uniq.media_folder_id` (`media_folder_id`),
              CONSTRAINT `json.media_default_folder.attributes` CHECK (JSON_VALID(`attributes`)),
              CONSTRAINT `json.media_default_folder.association_fields` CHECK (JSON_VALID(`association_fields`)),
              CONSTRAINT `fk.media_default_folder.media_folder_id` FOREIGN KEY (`media_folder_id`) 
                REFERENCES `media_folder` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // no destructive changes
    }
}
