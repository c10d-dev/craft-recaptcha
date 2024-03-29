<?php
/**
 * craft-recaptcha plugin for Craft CMS
 *
 * Integrate reCAPTCHA validation into your forms.
 *
 * @link      https://c10d.dev
 * @copyright Copyright (c) Cédric Givord
 */

namespace c10d\craftrecaptcha\migrations;

use c10d\craftrecaptcha\CraftRecaptcha;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * @author    Cédric Givord
 * @package   CraftRecaptcha
 * @since     1.1.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public string $driver;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

   /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables(): bool
    {
        $tablesCreated = false;

        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%craftrecaptcha_logs}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%craftrecaptcha_logs}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                    'success' => $this->boolean()->notNull()->defaultValue(false),
                    'requestUrl' => $this->mediumText(),
                    'requestBody' => $this->longText(),
                    'captchaJson' => $this->mediumText(),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * @return void
     */
    protected function createIndexes(): void
    {
        $this->createIndex(
            $this->db->getIndexName(
                '{{%craftrecaptcha_logs}}',
                'success',
                false
            ),
            '{{%craftrecaptcha_logs}}',
            'success',
            false
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                $this->createIndex(
                    $this->db->getIndexName(
                        '{{%craftrecaptcha_logs}}',
                        'requestUrl',
                        false
                    ),
                    '{{%craftrecaptcha_logs}}',
                    'requestUrl(40)',
                    false
                );
                break;
            case DbConfig::DRIVER_PGSQL:
                $this->createIndex(
                    $this->db->getIndexName(
                        '{{%craftrecaptcha_logs}}',
                        'requestUrl',
                        false
                    ),
                    '{{%craftrecaptcha_logs}}',
                    'requestUrl',
                    false
                );
                break;
        }
    }

    /**
     * @return void
     */
    protected function addForeignKeys(): void
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%craftrecaptcha_logs}}', 'siteId'),
            '{{%craftrecaptcha_logs}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @return void
     */
    protected function insertDefaultData(): void
    {
    }

    /**
     * @return void
     */
    protected function removeTables(): void
    {
        $this->dropTableIfExists('{{%craftrecaptcha_logs}}');
    }
}
