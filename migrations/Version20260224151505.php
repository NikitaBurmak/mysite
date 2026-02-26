<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260224151505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
        INSERT INTO \"app_user\" (email, roles, password)
        VALUES (
            'admin@gmail.com',
            '[\"ROLE_ADMIN\"]',
            '\$2y\$13\$p1b0OpMagzSCizc/gMbxde9t8HlKeFS8cyBBdF.g./5z/otlt2klO'
        )
    ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("
        DELETE FROM \"app_user\"
        WHERE email='admin@gmail.com'
    ");
    }
}
