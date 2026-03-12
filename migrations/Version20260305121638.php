<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260305121638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE anecdote_topic (anecdote_id INT NOT NULL, topic_id INT NOT NULL, PRIMARY KEY(anecdote_id, topic_id))');
        $this->addSql('CREATE INDEX IDX_4143AF2462922701 ON anecdote_topic (anecdote_id)');
        $this->addSql('CREATE INDEX IDX_4143AF241F55203D ON anecdote_topic (topic_id)');
        $this->addSql('ALTER TABLE anecdote_topic ADD CONSTRAINT FK_4143AF2462922701 FOREIGN KEY (anecdote_id) REFERENCES anecdote (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE anecdote_topic ADD CONSTRAINT FK_4143AF241F55203D FOREIGN KEY (topic_id) REFERENCES topics (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE anecdote DROP CONSTRAINT fk_a5051eec1f55203d');
        $this->addSql('DROP INDEX idx_a5051eec1f55203d');
        $this->addSql('ALTER TABLE anecdote DROP topic_id');
        $this->addSql('ALTER TABLE anecdote ALTER user_id DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE anecdote_topic DROP CONSTRAINT FK_4143AF2462922701');
        $this->addSql('ALTER TABLE anecdote_topic DROP CONSTRAINT FK_4143AF241F55203D');
        $this->addSql('DROP TABLE anecdote_topic');
        $this->addSql('ALTER TABLE anecdote ADD topic_id INT NOT NULL');
        $this->addSql('ALTER TABLE anecdote ALTER user_id SET NOT NULL');
        $this->addSql('ALTER TABLE anecdote ADD CONSTRAINT fk_a5051eec1f55203d FOREIGN KEY (topic_id) REFERENCES topics (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_a5051eec1f55203d ON anecdote (topic_id)');
    }
}
