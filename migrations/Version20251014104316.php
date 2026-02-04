<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251014104316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE no (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE anecdote ALTER votes_sum DROP DEFAULT');
        $this->addSql('ALTER TABLE anecdote ALTER votes_sum SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE no');
        $this->addSql('ALTER TABLE anecdote ALTER votes_sum SET DEFAULT 0');
        $this->addSql('ALTER TABLE anecdote ALTER votes_sum DROP NOT NULL');
    }
}
