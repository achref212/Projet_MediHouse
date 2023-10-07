<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230509115901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B6F7494E79F37AE5 ON question (id_user_id)');
        $this->addSql('ALTER TABLE reclamation CHANGE date_reclamation date_reclamation DATE NOT NULL, CHANGE etat etat VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD date_naissance DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E79F37AE5');
        $this->addSql('DROP INDEX IDX_B6F7494E79F37AE5 ON question');
        $this->addSql('ALTER TABLE reclamation CHANGE date_reclamation date_reclamation DATE DEFAULT NULL, CHANGE etat etat VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP date_naissance');
    }
}
