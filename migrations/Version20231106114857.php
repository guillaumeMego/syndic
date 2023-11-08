<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231106114857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE suivi_problematique DROP INDEX IDX_E88423FA18BBFAFB, ADD UNIQUE INDEX UNIQ_E88423FA18BBFAFB (problematique_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE suivi_problematique DROP INDEX UNIQ_E88423FA18BBFAFB, ADD INDEX IDX_E88423FA18BBFAFB (problematique_id)');
    }
}
