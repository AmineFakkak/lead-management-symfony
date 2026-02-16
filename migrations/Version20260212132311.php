<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260212132311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campaign CHANGE start_date start_date DATE DEFAULT NULL, CHANGE end_date end_date DATE DEFAULT NULL, CHANGE budget budget NUMERIC(10, 2) DEFAULT NULL, CHANGE utm_source utm_source VARCHAR(100) DEFAULT NULL, CHANGE utm_medium utm_medium VARCHAR(100) DEFAULT NULL, CHANGE utm_campaign utm_campaign VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE entity CHANGE logo logo VARCHAR(255) DEFAULT NULL, CHANGE color color VARCHAR(7) DEFAULT NULL');
        $this->addSql('ALTER TABLE interaction CHANGE subject subject VARCHAR(200) DEFAULT NULL, CHANGE content content VARCHAR(200) DEFAULT NULL, CHANGE outcome outcome VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE `lead` CHANGE phone phone VARCHAR(20) DEFAULT NULL, CHANGE whatsapp whatsapp VARCHAR(20) DEFAULT NULL, CHANGE company company VARCHAR(150) DEFAULT NULL, CHANGE job_title job_title VARCHAR(100) DEFAULT NULL, CHANGE city city VARCHAR(100) DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE converted_at converted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE project CHANGE start_date start_date DATE DEFAULT NULL, CHANGE end_date end_date DATE DEFAULT NULL, CHANGE budget budget NUMERIC(12, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE tag CHANGE color color VARCHAR(7) DEFAULT NULL');
        $this->addSql('ALTER TABLE task CHANGE due_date due_date DATETIME DEFAULT NULL, CHANGE completed_at completed_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campaign CHANGE start_date start_date DATE DEFAULT \'NULL\', CHANGE end_date end_date DATE DEFAULT \'NULL\', CHANGE budget budget NUMERIC(10, 2) DEFAULT \'NULL\', CHANGE utm_source utm_source VARCHAR(100) DEFAULT \'NULL\', CHANGE utm_medium utm_medium VARCHAR(100) DEFAULT \'NULL\', CHANGE utm_campaign utm_campaign VARCHAR(100) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE entity CHANGE logo logo VARCHAR(255) DEFAULT \'NULL\', CHANGE color color VARCHAR(7) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE interaction CHANGE subject subject VARCHAR(200) DEFAULT \'NULL\', CHANGE content content VARCHAR(200) DEFAULT \'NULL\', CHANGE outcome outcome VARCHAR(30) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE `lead` CHANGE phone phone VARCHAR(20) DEFAULT \'NULL\', CHANGE whatsapp whatsapp VARCHAR(20) DEFAULT \'NULL\', CHANGE company company VARCHAR(150) DEFAULT \'NULL\', CHANGE job_title job_title VARCHAR(100) DEFAULT \'NULL\', CHANGE city city VARCHAR(100) DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\', CHANGE converted_at converted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE project CHANGE start_date start_date DATE DEFAULT \'NULL\', CHANGE end_date end_date DATE DEFAULT \'NULL\', CHANGE budget budget NUMERIC(12, 2) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE tag CHANGE color color VARCHAR(7) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE task CHANGE due_date due_date DATETIME DEFAULT \'NULL\', CHANGE completed_at completed_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
