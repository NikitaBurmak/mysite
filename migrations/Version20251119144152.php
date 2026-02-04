<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251119144152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE no_id_seq CASCADE');
        $this->addSql('DROP TABLE no');
        $this->addSql('DROP TABLE anectodes_topics');
        $this->addSql('ALTER TABLE anecdote DROP CONSTRAINT fk_anecdote_topic');
        $this->addSql('ALTER TABLE anecdote ADD CONSTRAINT FK_A5051EEC1F55203D FOREIGN KEY (topic_id) REFERENCES topics (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE anecdote ADD CONSTRAINT FK_A5051EECA76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A5051EECA76ED395 ON anecdote (user_id)');
        $this->addSql('CREATE SEQUENCE topics_id_seq');
        $this->addSql('SELECT setval(\'topics_id_seq\', (SELECT MAX(id) FROM topics))');
        $this->addSql('ALTER TABLE topics ALTER id SET DEFAULT nextval(\'topics_id_seq\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE no_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE no (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE anectodes_topics (id_anecdote INT NOT NULL, id_topic INT NOT NULL, PRIMARY KEY(id_anecdote, id_topic))');
        $this->addSql('ALTER TABLE anecdote DROP CONSTRAINT FK_A5051EEC1F55203D');
        $this->addSql('ALTER TABLE anecdote DROP CONSTRAINT FK_A5051EECA76ED395');
        $this->addSql('DROP INDEX IDX_A5051EECA76ED395');
        $this->addSql('ALTER TABLE anecdote ADD CONSTRAINT fk_anecdote_topic FOREIGN KEY (topic_id) REFERENCES topics (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE topics ALTER id DROP DEFAULT');
    }
}
