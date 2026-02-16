<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260216092004 extends AbstractMigration
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
        $this->addSql('ALTER TABLE interaction ADD lead_id INT NOT NULL, ADD user_id INT NOT NULL, CHANGE subject subject VARCHAR(200) DEFAULT NULL, CHANGE content content LONGTEXT DEFAULT NULL, CHANGE outcome outcome VARCHAR(30) DEFAULT NULL');
        $this->addSql('ALTER TABLE interaction ADD CONSTRAINT FK_378DFDA755458D FOREIGN KEY (lead_id) REFERENCES `lead` (id)');
        $this->addSql('ALTER TABLE interaction ADD CONSTRAINT FK_378DFDA7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_378DFDA755458D ON interaction (lead_id)');
        $this->addSql('CREATE INDEX IDX_378DFDA7A76ED395 ON interaction (user_id)');
        $this->addSql('ALTER TABLE `lead` CHANGE full_name full_name VARCHAR(150) NOT NULL, CHANGE phone phone VARCHAR(20) DEFAULT NULL, CHANGE whatsapp whatsapp VARCHAR(20) DEFAULT NULL, CHANGE company company VARCHAR(150) DEFAULT NULL, CHANGE job_title job_title VARCHAR(100) DEFAULT NULL, CHANGE city city VARCHAR(100) DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE converted_at converted_at DATETIME DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_289161CBE7927C74 ON `lead` (email)');
        $this->addSql('ALTER TABLE project CHANGE start_date start_date DATE DEFAULT NULL, CHANGE end_date end_date DATE DEFAULT NULL, CHANGE budget budget NUMERIC(12, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE tag CHANGE color color VARCHAR(7) DEFAULT NULL');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY `FK_527EDB25747817FD`');
        $this->addSql('DROP INDEX IDX_527EDB25747817FD ON task');
        $this->addSql('ALTER TABLE task CHANGE due_date due_date DATETIME DEFAULT NULL, CHANGE completed_at completed_at DATETIME DEFAULT NULL, CHANGE leads_id lead_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2555458D FOREIGN KEY (lead_id) REFERENCES `lead` (id)');
        $this->addSql('CREATE INDEX IDX_527EDB2555458D ON task (lead_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campaign CHANGE start_date start_date DATE DEFAULT \'NULL\', CHANGE end_date end_date DATE DEFAULT \'NULL\', CHANGE budget budget NUMERIC(10, 2) DEFAULT \'NULL\', CHANGE utm_source utm_source VARCHAR(100) DEFAULT \'NULL\', CHANGE utm_medium utm_medium VARCHAR(100) DEFAULT \'NULL\', CHANGE utm_campaign utm_campaign VARCHAR(100) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE entity CHANGE logo logo VARCHAR(255) DEFAULT \'NULL\', CHANGE color color VARCHAR(7) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE interaction DROP FOREIGN KEY FK_378DFDA755458D');
        $this->addSql('ALTER TABLE interaction DROP FOREIGN KEY FK_378DFDA7A76ED395');
        $this->addSql('DROP INDEX IDX_378DFDA755458D ON interaction');
        $this->addSql('DROP INDEX IDX_378DFDA7A76ED395 ON interaction');
        $this->addSql('ALTER TABLE interaction DROP lead_id, DROP user_id, CHANGE subject subject VARCHAR(200) DEFAULT \'NULL\', CHANGE content content VARCHAR(200) DEFAULT \'NULL\', CHANGE outcome outcome VARCHAR(30) DEFAULT \'NULL\'');
        $this->addSql('DROP INDEX UNIQ_289161CBE7927C74 ON `lead`');
        $this->addSql('ALTER TABLE `lead` CHANGE full_name full_name VARCHAR(100) NOT NULL, CHANGE phone phone VARCHAR(20) DEFAULT \'NULL\', CHANGE whatsapp whatsapp VARCHAR(20) DEFAULT \'NULL\', CHANGE company company VARCHAR(150) DEFAULT \'NULL\', CHANGE job_title job_title VARCHAR(100) DEFAULT \'NULL\', CHANGE city city VARCHAR(100) DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\', CHANGE converted_at converted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE project CHANGE start_date start_date DATE DEFAULT \'NULL\', CHANGE end_date end_date DATE DEFAULT \'NULL\', CHANGE budget budget NUMERIC(12, 2) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE tag CHANGE color color VARCHAR(7) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB2555458D');
        $this->addSql('DROP INDEX IDX_527EDB2555458D ON task');
        $this->addSql('ALTER TABLE task CHANGE due_date due_date DATETIME DEFAULT \'NULL\', CHANGE completed_at completed_at DATETIME DEFAULT \'NULL\', CHANGE lead_id leads_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT `FK_527EDB25747817FD` FOREIGN KEY (leads_id) REFERENCES `lead` (id)');
        $this->addSql('CREATE INDEX IDX_527EDB25747817FD ON task (leads_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
