<?php

declare(strict_types=1);

namespace Migrations;

use App\Core\Repository\Role\RoleService;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240323171931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        RoleService::create_default_roles($this->connection);
    }

    public function down(Schema $schema): void
    {
        RoleService::remove_default_roles($this->connection);
    }
}
