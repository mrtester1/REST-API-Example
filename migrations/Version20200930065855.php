<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200930065855 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Creates an UUID extention, fills groups table with pre-defined groups';
    }

    public function up(Schema $schema) : void
    {
        /**
         * Расширение для работы с uuid
         */
        $this->addSql("CREATE EXTENSION IF NOT EXISTS \"uuid-ossp\"");

        /**
         * Таблица групп
         */
        $this->addSql("insert into groups (id, name) values ('d18b29bd-b4ef-4891-98d3-aa25ccc6e9a9','guest')");
        $this->addSql("insert into groups (id, name) values ('90617e56-1220-40ee-95e9-1d9c8cf77d1b','user')");
        $this->addSql("insert into groups (id, name) values ('8cd8e1b8-c9e3-4206-9402-29f854c398b7','manager')");

        /**
         * Таблица доступов
         */
        $this->addSql("insert into acl (id, group_id, url, method) values (1, 'd18b29bd-b4ef-4891-98d3-aa25ccc6e9a9', '/api/v1/group_list', 'GET')");
        $this->addSql("insert into acl (id, group_id, url, method) values (2, '90617e56-1220-40ee-95e9-1d9c8cf77d1b', '/api/v1/group_list', 'GET')");
        $this->addSql("insert into acl (id, group_id, url, method) values (3, '8cd8e1b8-c9e3-4206-9402-29f854c398b7', '/api/v1/group_list', 'GET')");

        $this->addSql("insert into acl (id, group_id, url, method) values (4, '90617e56-1220-40ee-95e9-1d9c8cf77d1b', '/api/v1/user_get-by-id', 'GET')");
        $this->addSql("insert into acl (id, group_id, url, method) values (5, '8cd8e1b8-c9e3-4206-9402-29f854c398b7', '/api/v1/user_get-by-id', 'GET')");

        $this->addSql("insert into acl (id, group_id, url, method) values (6, '8cd8e1b8-c9e3-4206-9402-29f854c398b7', '/api/v1/user_update', 'POST')");
        $this->addSql("insert into acl (id, group_id, url, method) values (7, '8cd8e1b8-c9e3-4206-9402-29f854c398b7', '/api/v1/user_add', 'POST')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql("truncate table groups");
        $this->addSql("truncate table acl");
    }
}
