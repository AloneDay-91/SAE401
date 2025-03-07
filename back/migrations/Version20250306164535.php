<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306164535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devoirs ADD id_classes_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE devoirs ADD CONSTRAINT FK_3C7810063BA8DBF3 FOREIGN KEY (id_classes_id) REFERENCES classes (id)');
        $this->addSql('CREATE INDEX IDX_3C7810063BA8DBF3 ON devoirs (id_classes_id)');
        $this->addSql('ALTER TABLE user CHANGE id_classes_id id_classes_id INT NOT NULL, CHANGE roleapp roleapp VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devoirs DROP FOREIGN KEY FK_3C7810063BA8DBF3');
        $this->addSql('DROP INDEX IDX_3C7810063BA8DBF3 ON devoirs');
        $this->addSql('ALTER TABLE devoirs DROP id_classes_id');
        $this->addSql('ALTER TABLE user CHANGE id_classes_id id_classes_id INT DEFAULT NULL, CHANGE roleapp roleapp VARCHAR(255) DEFAULT \'ELEVE\' NOT NULL');
    }
}
