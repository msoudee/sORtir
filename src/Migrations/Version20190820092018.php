<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190820092018 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE etats (no_etat INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(30) NOT NULL, PRIMARY KEY(no_etat)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscriptions (sorties_no_sortie INT NOT NULL, participants_no_participant INT NOT NULL, date_inscription DATETIME NOT NULL, INDEX inscriptions_participants_fk (participants_no_participant), PRIMARY KEY(sorties_no_sortie, participants_no_participant)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lieux (no_lieu INT AUTO_INCREMENT NOT NULL, nom_lieu VARCHAR(30) NOT NULL, rue VARCHAR(30) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, villes_no_ville INT NOT NULL, INDEX lieux_villes_fk (villes_no_ville), PRIMARY KEY(no_lieu)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participants (no_participant INT AUTO_INCREMENT NOT NULL, pseudo VARCHAR(30) NOT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) NOT NULL, telephone VARCHAR(15) DEFAULT NULL, mail VARCHAR(20) NOT NULL, mot_de_passe VARCHAR(20) NOT NULL, administrateur TINYINT(1) NOT NULL, actif TINYINT(1) NOT NULL, sites_no_site INT NOT NULL, UNIQUE INDEX participants_pseudo_uk (pseudo), PRIMARY KEY(no_participant)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sites (no_site INT AUTO_INCREMENT NOT NULL, nom_site VARCHAR(30) NOT NULL, PRIMARY KEY(no_site)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sorties (no_sortie INT AUTO_INCREMENT NOT NULL, nom VARCHAR(30) NOT NULL, datedebut DATETIME NOT NULL, duree INT DEFAULT NULL, datecloture DATETIME NOT NULL, nbinscriptionsmax INT NOT NULL, descriptioninfos VARCHAR(500) DEFAULT NULL, etatsortie INT DEFAULT NULL, urlPhoto VARCHAR(250) DEFAULT NULL, organisateur INT NOT NULL, lieux_no_lieu INT NOT NULL, etats_no_etat INT NOT NULL, INDEX sorties_lieux_fk (lieux_no_lieu), INDEX sorties_etats_fk (etats_no_etat), INDEX sorties_participants_fk (organisateur), PRIMARY KEY(no_sortie)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE villes (no_ville INT AUTO_INCREMENT NOT NULL, nom_ville VARCHAR(30) NOT NULL, code_postal VARCHAR(10) NOT NULL, PRIMARY KEY(no_ville)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE etats');
        $this->addSql('DROP TABLE inscriptions');
        $this->addSql('DROP TABLE lieux');
        $this->addSql('DROP TABLE participants');
        $this->addSql('DROP TABLE sites');
        $this->addSql('DROP TABLE sorties');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE villes');
    }
}
