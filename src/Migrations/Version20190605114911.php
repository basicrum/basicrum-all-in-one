<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190605114911 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE releases (id INT UNSIGNED AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, description TEXT DEFAULT NULL, INDEX date (date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visits_overview (visit_id INT UNSIGNED AUTO_INCREMENT NOT NULL, guid CHAR(128) DEFAULT NULL, page_views_count INT UNSIGNED NOT NULL, first_page_view_id INT UNSIGNED NOT NULL, last_page_view_id INT UNSIGNED DEFAULT NULL, visit_duration INT UNSIGNED DEFAULT NULL, after_last_visit_duration INT UNSIGNED NOT NULL, first_url_id INT UNSIGNED NOT NULL, last_url_id INT UNSIGNED NOT NULL, completed TINYINT(1) NOT NULL, INDEX completed (completed), INDEX first_page_view_id (first_page_view_id), INDEX last_page_view_id (last_page_view_id), INDEX guid (guid), PRIMARY KEY(visit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feedback (id INT UNSIGNED AUTO_INCREMENT NOT NULL, message TEXT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operating_systems (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(64) NOT NULL, code VARCHAR(64) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE navigation_timings_user_agents (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_agent TEXT NOT NULL, device_type TEXT NOT NULL, device_type_id INT NOT NULL, device_model TEXT NOT NULL, device_manufacturer TEXT NOT NULL, browser_name TEXT NOT NULL, browser_version TEXT NOT NULL, os_name TEXT NOT NULL, os_version TEXT NOT NULL, os_id INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX created_at (created_at), INDEX os_id (os_id), INDEX device_type_id (device_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resource_timings_urls (id INT UNSIGNED AUTO_INCREMENT NOT NULL, url TEXT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX created_at (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE navigation_timings_query_params (page_view_id INT UNSIGNED AUTO_INCREMENT NOT NULL, query_params TEXT NOT NULL, PRIMARY KEY(page_view_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device_types (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(64) NOT NULL, code VARCHAR(64) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE navigation_timings (page_view_id INT UNSIGNED AUTO_INCREMENT NOT NULL, dns_duration SMALLINT UNSIGNED NOT NULL, connect_duration SMALLINT UNSIGNED NOT NULL, first_byte SMALLINT UNSIGNED NOT NULL, redirect_duration SMALLINT UNSIGNED NOT NULL, last_byte_duration SMALLINT UNSIGNED NOT NULL, first_paint SMALLINT UNSIGNED NOT NULL, first_contentful_paint SMALLINT UNSIGNED NOT NULL, redirects_count SMALLINT NOT NULL, url_id INT UNSIGNED NOT NULL, user_agent_id INT UNSIGNED NOT NULL, device_type_id INT NOT NULL, os_id INT NOT NULL, process_id CHAR(8) NOT NULL, guid CHAR(128) NOT NULL, stay_on_page_time SMALLINT UNSIGNED NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, load_event_end SMALLINT UNSIGNED NOT NULL, INDEX os_id (os_id), INDEX url_id (url_id), INDEX url_id_2 (url_id, created_at), INDEX user_agent_id (user_agent_id), INDEX device_type_id (device_type_id), INDEX created_at (created_at), INDEX guid (guid), INDEX page_view_id (page_view_id, user_agent_id), PRIMARY KEY(page_view_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resource_timings (page_view_id INT UNSIGNED AUTO_INCREMENT NOT NULL, resource_timings TEXT NOT NULL, PRIMARY KEY(page_view_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE boomerang_builds (id INT UNSIGNED AUTO_INCREMENT NOT NULL, build_params TEXT NOT NULL, build_result TEXT NOT NULL, boomerang_version VARCHAR(128) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE navigation_timings_urls (id INT UNSIGNED AUTO_INCREMENT NOT NULL, url TEXT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX created_at (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page_type_config (id INT AUTO_INCREMENT NOT NULL, page_type_name VARCHAR(255) NOT NULL, condition_value TEXT NOT NULL, condition_term TEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE releases');
        $this->addSql('DROP TABLE visits_overview');
        $this->addSql('DROP TABLE feedback');
        $this->addSql('DROP TABLE operating_systems');
        $this->addSql('DROP TABLE navigation_timings_user_agents');
        $this->addSql('DROP TABLE resource_timings_urls');
        $this->addSql('DROP TABLE navigation_timings_query_params');
        $this->addSql('DROP TABLE device_types');
        $this->addSql('DROP TABLE navigation_timings');
        $this->addSql('DROP TABLE resource_timings');
        $this->addSql('DROP TABLE boomerang_builds');
        $this->addSql('DROP TABLE navigation_timings_urls');
        $this->addSql('DROP TABLE page_type_config');
    }
}
