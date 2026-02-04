<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251021114715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anecdote ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE anecdote DROP votes_sum');
        $this->addSql('ALTER TABLE anecdote ADD CONSTRAINT FK_A5051EECA76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A5051EECA76ED395 ON anecdote (user_id)');
        $this->addSql('DROP INDEX vote_user_id_anecdote_id_key');
        $this->addSql('ALTER TABLE vote ALTER user_id DROP NOT NULL');
        $this->addSql('ALTER TABLE vote ALTER anecdote_id DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE vote ALTER user_id SET NOT NULL');
        $this->addSql('ALTER TABLE vote ALTER anecdote_id SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX vote_user_id_anecdote_id_key ON vote (user_id, anecdote_id)');
        $this->addSql('ALTER TABLE anecdote DROP CONSTRAINT FK_A5051EECA76ED395');
        $this->addSql('DROP INDEX IDX_A5051EECA76ED395');
        $this->addSql('ALTER TABLE anecdote ADD votes_sum INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE anecdote DROP user_id');
    }
}
