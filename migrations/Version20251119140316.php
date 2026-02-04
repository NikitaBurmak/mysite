<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251119140316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE anecdote DROP CONSTRAINT fk_anecdote_topic');
        $this->addSql('DROP SEQUENCE no_id_seq CASCADE');

        $this->addSql('CREATE TABLE topics (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');

        $this->addSql('DROP TABLE no');
        $this->addSql('DROP TABLE anectodes_topics');

        $this->addSql('ALTER TABLE anecdote ADD user_id INT NOT NULL');

        $this->addSql('ALTER TABLE anecdote ADD CONSTRAINT FK_A5051EEC1F55203D FOREIGN KEY (topic_id) REFERENCES topics (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE anecdote ADD CONSTRAINT FK_A5051EECA76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A5051EECA76ED395 ON anecdote (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE anecdote DROP CONSTRAINT FK_A5051EEC1F55203D');
        $this->addSql('ALTER TABLE anecdote DROP CONSTRAINT FK_A5051EECA76ED395');
        $this->addSql('DROP INDEX IDX_A5051EECA76ED395');
        $this->addSql('ALTER TABLE anecdote DROP user_id');

        $this->addSql('DROP TABLE topics'); // теперь корректное имя
        $this->addSql('CREATE TABLE topics (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');

        $this->addSql('CREATE TABLE no (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE anectodes_topics (id_anecdote INT NOT NULL, id_topic INT NOT NULL, PRIMARY KEY(id_anecdote, id_topic))');

        $this->addSql('ALTER TABLE anecdote ADD CONSTRAINT fk_anecdote_topic FOREIGN KEY (topic_id) REFERENCES topics (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
