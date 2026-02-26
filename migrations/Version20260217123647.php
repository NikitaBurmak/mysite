<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260217123647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE anecdote (id SERIAL NOT NULL, topic_id INT NOT NULL, user_id INT NOT NULL, text TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A5051EEC1F55203D ON anecdote (topic_id)');
        $this->addSql('CREATE INDEX IDX_A5051EECA76ED395 ON anecdote (user_id)');
        $this->addSql('CREATE TABLE app_user (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9E7927C74 ON app_user (email)');
        $this->addSql('CREATE TABLE topics (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE vote (id SERIAL NOT NULL, user_id INT NOT NULL, anecdote_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5A108564A76ED395 ON vote (user_id)');
        $this->addSql('CREATE INDEX IDX_5A10856462922701 ON vote (anecdote_id)');
        $this->addSql('CREATE UNIQUE INDEX user_anecdote_unique ON vote (user_id, anecdote_id)');
        $this->addSql('ALTER TABLE anecdote ADD CONSTRAINT FK_A5051EEC1F55203D FOREIGN KEY (topic_id) REFERENCES topics (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE anecdote ADD CONSTRAINT FK_A5051EECA76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564A76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A10856462922701 FOREIGN KEY (anecdote_id) REFERENCES anecdote (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE anecdote DROP CONSTRAINT FK_A5051EEC1F55203D');
        $this->addSql('ALTER TABLE anecdote DROP CONSTRAINT FK_A5051EECA76ED395');
        $this->addSql('ALTER TABLE vote DROP CONSTRAINT FK_5A108564A76ED395');
        $this->addSql('ALTER TABLE vote DROP CONSTRAINT FK_5A10856462922701');
        $this->addSql('DROP TABLE anecdote');
        $this->addSql('DROP TABLE app_user');
        $this->addSql('DROP TABLE topics');
        $this->addSql('DROP TABLE vote');
    }
}
