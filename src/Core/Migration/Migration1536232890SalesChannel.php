<?php declare(strict_types=1);

namespace Shopware\Core\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1536232890SalesChannel extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1536232890;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
            CREATE TABLE `sales_channel` (
              `id` BINARY(16) NOT NULL,
              `type_id` BINARY(16) NOT NULL,
              `configuration` JSON NULL,
              `access_key` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `language_id` BINARY(16) NOT NULL,
              `currency_id` BINARY(16) NOT NULL,
              `payment_method_id` BINARY(16) NOT NULL,
              `shipping_method_id` BINARY(16) NOT NULL,
              `country_id` BINARY(16) NOT NULL,
              `active` TINYINT(1) NOT NULL DEFAULT '1',
              `tax_calculation_type` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vertical',
              `created_at` DATETIME(3) NOT NULL,
              `updated_at` DATETIME(3) NULL,
              PRIMARY KEY (`id`),
              UNIQUE `uniq.access_key` (`access_key`),
              CONSTRAINT `json.sales_channel.configuration` CHECK (JSON_VALID(`configuration`)),
              CONSTRAINT `fk.sales_channel.country_id` FOREIGN KEY (`country_id`)
                REFERENCES `country` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
              CONSTRAINT `fk.sales_channel.currency_id` FOREIGN KEY (`currency_id`)
                REFERENCES `currency` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
              CONSTRAINT `fk.sales_channel.language_id` FOREIGN KEY (`language_id`)
                REFERENCES `language` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
              CONSTRAINT `fk.sales_channel.payment_method_id` FOREIGN KEY (`payment_method_id`)
                REFERENCES `payment_method` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
              CONSTRAINT `fk.sales_channel.shipping_method_id` FOREIGN KEY (`shipping_method_id`)
                REFERENCES `shipping_method` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
              CONSTRAINT `fk.sales_channel.type_id` FOREIGN KEY (`type_id`)
                REFERENCES `sales_channel_type` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        $connection->executeQuery($sql);

        $connection->executeQuery('
            CREATE TABLE `sales_channel_translation` (
              `sales_channel_id` BINARY(16) NOT NULL,
              `language_id` BINARY(16) NOT NULL,
              `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL,
              `attributes` JSON NULL,
              `created_at` DATETIME(3) NOT NULL,
              `updated_at` DATETIME(3) NULL,
              PRIMARY KEY (`sales_channel_id`, `language_id`),
              CONSTRAINT `json.sales_channel_translation.attributes` CHECK (JSON_VALID(`attributes`)),
              CONSTRAINT `fk.sales_channel_translation.language_id` FOREIGN KEY (`language_id`)
                REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk.sales_channel_translation.sales_channel_id` FOREIGN KEY (`sales_channel_id`)
                REFERENCES `sales_channel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
