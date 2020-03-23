<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200312181539 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE beacons (page_view_id INT UNSIGNED AUTO_INCREMENT NOT NULL, beacon TEXT NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(page_view_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE boomerang_builds (id INT UNSIGNED AUTO_INCREMENT NOT NULL, build_params TEXT NOT NULL COLLATE utf8mb4_unicode_ci, build_result TEXT NOT NULL COLLATE utf8mb4_unicode_ci, boomerang_version VARCHAR(128) NOT NULL COLLATE utf8mb4_unicode_ci, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE device_types (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci, code VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE feedback (id INT UNSIGNED AUTO_INCREMENT NOT NULL, message TEXT NOT NULL COLLATE utf8mb4_unicode_ci, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE last_blocking_resources (page_view_id INT UNSIGNED NOT NULL, time SMALLINT UNSIGNED NOT NULL, url TEXT NOT NULL COLLATE utf8mb4_unicode_ci, first_paint SMALLINT UNSIGNED NOT NULL, PRIMARY KEY(page_view_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE navigation_timings (page_view_id INT UNSIGNED AUTO_INCREMENT NOT NULL, dns_duration SMALLINT UNSIGNED NOT NULL, connect_duration SMALLINT UNSIGNED NOT NULL, ttfb SMALLINT NOT NULL, download_time SMALLINT NOT NULL, first_byte SMALLINT UNSIGNED NOT NULL, redirect_duration SMALLINT UNSIGNED NOT NULL, last_byte_duration SMALLINT UNSIGNED NOT NULL, first_paint SMALLINT UNSIGNED NOT NULL, first_contentful_paint SMALLINT UNSIGNED NOT NULL, redirects_count SMALLINT NOT NULL, total_img_size INT NOT NULL, total_js_compressed_size INT NOT NULL, total_js_uncomressed_size INT NOT NULL, total_css_compressed_size INT NOT NULL, total_css_uncomressed_size INT NOT NULL, number_js_files SMALLINT NOT NULL, number_css_files SMALLINT NOT NULL, number_img_files SMALLINT NOT NULL, url_id INT UNSIGNED NOT NULL, user_agent_id INT UNSIGNED NOT NULL, device_type_id INT NOT NULL, os_id INT NOT NULL, process_id CHAR(8) NOT NULL COLLATE utf8mb4_unicode_ci, guid CHAR(128) NOT NULL COLLATE utf8mb4_unicode_ci, stay_on_page_time SMALLINT UNSIGNED NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, load_event_end SMALLINT UNSIGNED NOT NULL, INDEX created_at (created_at), INDEX url_id_2 (url_id, created_at), INDEX device_type_id (device_type_id), INDEX url_id (url_id), INDEX page_view_id (page_view_id, user_agent_id), INDEX user_agent_id (user_agent_id), INDEX os_id (os_id), INDEX guid (guid), PRIMARY KEY(page_view_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE navigation_timings_query_params (page_view_id INT UNSIGNED NOT NULL, query_params TEXT NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(page_view_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE navigation_timings_urls (id INT UNSIGNED AUTO_INCREMENT NOT NULL, url TEXT NOT NULL COLLATE utf8mb4_unicode_ci, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX created_at (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE navigation_timings_user_agents (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_agent TEXT NOT NULL COLLATE utf8mb4_unicode_ci, device_type TEXT NOT NULL COLLATE utf8mb4_unicode_ci, device_type_id INT NOT NULL, device_model TEXT NOT NULL COLLATE utf8mb4_unicode_ci, device_manufacturer TEXT NOT NULL COLLATE utf8mb4_unicode_ci, browser_name TEXT NOT NULL COLLATE utf8mb4_unicode_ci, browser_version TEXT NOT NULL COLLATE utf8mb4_unicode_ci, os_name TEXT NOT NULL COLLATE utf8mb4_unicode_ci, os_version TEXT NOT NULL COLLATE utf8mb4_unicode_ci, os_id INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX created_at (created_at), INDEX device_type_id (device_type_id), INDEX os_id (os_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE operating_systems (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci, code VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE page_type_config (id INT AUTO_INCREMENT NOT NULL, page_type_name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, condition_value TEXT NOT NULL COLLATE utf8mb4_unicode_ci, condition_term TEXT NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE releases (id INT UNSIGNED AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, description TEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, INDEX date (date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE site_settings (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, value LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, roles LONGTEXT NOT NULL COLLATE utf8mb4_bin, password VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, fname VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, lname VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, restore_password VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE visits_overview (visit_id INT UNSIGNED AUTO_INCREMENT NOT NULL, guid CHAR(128) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, page_views_count INT UNSIGNED NOT NULL, first_page_view_id INT UNSIGNED NOT NULL, last_page_view_id INT UNSIGNED DEFAULT NULL, visit_duration INT UNSIGNED DEFAULT NULL, after_last_visit_duration INT UNSIGNED NOT NULL, first_url_id INT UNSIGNED NOT NULL, last_url_id INT UNSIGNED NOT NULL, completed TINYINT(1) NOT NULL, INDEX last_page_view_id (last_page_view_id), INDEX first_page_view_id (first_page_view_id), INDEX completed (completed), INDEX guid (guid), PRIMARY KEY(visit_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE beacons');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE boomerang_builds');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE device_types');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE feedback');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE last_blocking_resources');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE navigation_timings');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE navigation_timings_query_params');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE navigation_timings_urls');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE navigation_timings_user_agents');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE operating_systems');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE page_type_config');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE releases');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE site_settings');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE visits_overview');
    }
}
