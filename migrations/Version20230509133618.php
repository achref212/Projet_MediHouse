<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230509133618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rendez_vous ADD time TIME DEFAULT NULL, CHANGE date date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE profilepicture profilepicture VARCHAR(255) NOT NULL, CHANGE activate activate TINYINT(1) NOT NULL, CHANGE reset_token reset_token VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rendez_vous DROP time, CHANGE date date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE profilepicture profilepicture VARCHAR(255) DEFAULT NULL, CHANGE activate activate TINYINT(1) DEFAULT NULL, CHANGE reset_token reset_token VARCHAR(255) DEFAULT NULL');
    }
}
