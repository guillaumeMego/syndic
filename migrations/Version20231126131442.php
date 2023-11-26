<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231126131442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE problematiques (id INT AUTO_INCREMENT NOT NULL, auteur_id INT DEFAULT NULL, problematique VARCHAR(127) NOT NULL, description VARCHAR(255) NOT NULL, image_name VARCHAR(255) DEFAULT NULL, commentaire LONGTEXT DEFAULT NULL, date_ajout DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', date_modif DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_5CAC852E60BB6FE6 (auteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE suivi_problematique (id INT AUTO_INCREMENT NOT NULL, membre_validateur_id INT DEFAULT NULL, problematique_id INT NOT NULL, etat VARCHAR(50) NOT NULL, date_modif DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_E88423FAC2D1011D (membre_validateur_id), UNIQUE INDEX UNIQ_E88423FA18BBFAFB (problematique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, prenom VARCHAR(100) NOT NULL, nom VARCHAR(100) NOT NULL, email VARCHAR(320) NOT NULL, is_verified TINYINT(1) NOT NULL, batiment VARCHAR(10) NOT NULL, etage INT NOT NULL, numero_appartement INT NOT NULL, telephone VARCHAR(10) NOT NULL, image_name VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, date_ajout DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', date_modif DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', password_change_required TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE problematiques ADD CONSTRAINT FK_5CAC852E60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE suivi_problematique ADD CONSTRAINT FK_E88423FAC2D1011D FOREIGN KEY (membre_validateur_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE suivi_problematique ADD CONSTRAINT FK_E88423FA18BBFAFB FOREIGN KEY (problematique_id) REFERENCES problematiques (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE problematiques DROP FOREIGN KEY FK_5CAC852E60BB6FE6');
        $this->addSql('ALTER TABLE suivi_problematique DROP FOREIGN KEY FK_E88423FAC2D1011D');
        $this->addSql('ALTER TABLE suivi_problematique DROP FOREIGN KEY FK_E88423FA18BBFAFB');
        $this->addSql('DROP TABLE problematiques');
        $this->addSql('DROP TABLE suivi_problematique');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
