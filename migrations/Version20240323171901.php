<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240323171901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE permissions (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(127) NOT NULL, description VARCHAR(255) NOT NULL, type VARCHAR(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_2DEDCC6F5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_assignments (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, role_id INT UNSIGNED NOT NULL, INDEX IDX_2DD0854A76ED395 (user_id), INDEX IDX_2DD0854D60322AC (role_id), UNIQUE INDEX video_unique_idx (user_id, role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_permissions (id INT UNSIGNED AUTO_INCREMENT NOT NULL, permission_id INT UNSIGNED NOT NULL, role_id INT UNSIGNED NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_1FBA94E6FED90CCA (permission_id), INDEX IDX_1FBA94E6D60322AC (role_id), UNIQUE INDEX video_unique_idx (role_id, permission_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, fullname VARCHAR(255) DEFAULT NULL, archetype VARCHAR(64) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_B63E2EC75E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE role_assignments ADD CONSTRAINT FK_2DD0854A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE role_assignments ADD CONSTRAINT FK_2DD0854D60322AC FOREIGN KEY (role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE role_permissions ADD CONSTRAINT FK_1FBA94E6FED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id)');
        $this->addSql('ALTER TABLE role_permissions ADD CONSTRAINT FK_1FBA94E6D60322AC FOREIGN KEY (role_id) REFERENCES roles (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role_assignments DROP FOREIGN KEY FK_2DD0854A76ED395');
        $this->addSql('ALTER TABLE role_assignments DROP FOREIGN KEY FK_2DD0854D60322AC');
        $this->addSql('ALTER TABLE role_permissions DROP FOREIGN KEY FK_1FBA94E6FED90CCA');
        $this->addSql('ALTER TABLE role_permissions DROP FOREIGN KEY FK_1FBA94E6D60322AC');
        $this->addSql('DROP TABLE permissions');
        $this->addSql('DROP TABLE role_assignments');
        $this->addSql('DROP TABLE role_permissions');
        $this->addSql('DROP TABLE roles');
    }
}
