<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231106081916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE problematiques DROP etat');
        $this->addSql('ALTER TABLE suivi_problematique ADD membre_validateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE suivi_problematique ADD CONSTRAINT FK_E88423FAC2D1011D FOREIGN KEY (membre_validateur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E88423FAC2D1011D ON suivi_problematique (membre_validateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE problematiques ADD etat VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE suivi_problematique DROP FOREIGN KEY FK_E88423FAC2D1011D');
        $this->addSql('DROP INDEX IDX_E88423FAC2D1011D ON suivi_problematique');
        $this->addSql('ALTER TABLE suivi_problematique DROP membre_validateur_id');
    }
}
