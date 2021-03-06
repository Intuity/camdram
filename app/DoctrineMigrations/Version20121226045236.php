<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121226045236 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

        $this->addSql("DROP INDEX token ON acts_authtokens");
        $this->addSql("ALTER TABLE acts_users CHANGE pass pass VARCHAR(32) DEFAULT NULL, CHANGE resetcode resetcode VARCHAR(32) DEFAULT NULL");
        $this->addSql("DROP INDEX UNIQ_487DD160989D9B62 ON acts_entities");
        $this->addSql("CREATE UNIQUE INDEX slugs ON acts_entities (entity_type, slug)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("DROP INDEX slugs ON acts_entities");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_487DD160989D9B62 ON acts_entities (slug)");
        $this->addSql("CREATE INDEX token ON acts_authtokens (token)");
        $this->addSql("ALTER TABLE acts_users CHANGE pass pass VARCHAR(32) NOT NULL, CHANGE resetcode resetcode VARCHAR(32) NOT NULL");
    }
}
