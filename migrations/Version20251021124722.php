<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251021124722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anecdote ADD topic VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE anecdote DROP votes_sum');
        $this->addSql('ALTER INDEX vote_user_id_anecdote_id_key RENAME TO user_anecdote_unique');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER INDEX user_anecdote_unique RENAME TO vote_user_id_anecdote_id_key');
        $this->addSql('ALTER TABLE anecdote ADD votes_sum INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE anecdote DROP topic');
    }
}
