<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250228174909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE badges (id INT AUTO_INCREMENT NOT NULL, intitule VARCHAR(255) NOT NULL, img VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, couleur VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE checkbox_status (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, devoirs_id INT DEFAULT NULL, INDEX IDX_32D33657A76ED395 (user_id), INDEX IDX_32D3365776D052E1 (devoirs_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classes (id INT AUTO_INCREMENT NOT NULL, intitule VARCHAR(255) NOT NULL, promo VARCHAR(255) NOT NULL, tp VARCHAR(255) NOT NULL, td VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE devoirs (id INT AUTO_INCREMENT NOT NULL, id_users_id INT DEFAULT NULL, id_matieres_id INT DEFAULT NULL, id_categories_id INT DEFAULT NULL, id_format_rendu_id INT DEFAULT NULL, intitule VARCHAR(255) NOT NULL, contenu VARCHAR(255) NOT NULL, date DATE NOT NULL, heure TIME NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_3C781006376858A8 (id_users_id), INDEX IDX_3C7810069A27C5C6 (id_matieres_id), INDEX IDX_3C7810061C3AC5D2 (id_categories_id), INDEX IDX_3C78100687102AB1 (id_format_rendu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE format_rendu (id INT AUTO_INCREMENT NOT NULL, intitule VARCHAR(255) NOT NULL, lien VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_badges (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, id_badges_id INT DEFAULT NULL, INDEX IDX_1DA448A7A76ED395 (user_id), INDEX IDX_1DA448A7D3B81DF7 (id_badges_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_devoir_vote (id INT AUTO_INCREMENT NOT NULL, devoirs_id INT DEFAULT NULL, user_id INT DEFAULT NULL, vote INT NOT NULL, verif TINYINT(1) NOT NULL, INDEX IDX_6FEFD2176D052E1 (devoirs_id), INDEX IDX_6FEFD21A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE checkbox_status ADD CONSTRAINT FK_32D33657A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE checkbox_status ADD CONSTRAINT FK_32D3365776D052E1 FOREIGN KEY (devoirs_id) REFERENCES devoirs (id)');
        $this->addSql('ALTER TABLE devoirs ADD CONSTRAINT FK_3C781006376858A8 FOREIGN KEY (id_users_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE devoirs ADD CONSTRAINT FK_3C7810069A27C5C6 FOREIGN KEY (id_matieres_id) REFERENCES matieres (id)');
        $this->addSql('ALTER TABLE devoirs ADD CONSTRAINT FK_3C7810061C3AC5D2 FOREIGN KEY (id_categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE devoirs ADD CONSTRAINT FK_3C78100687102AB1 FOREIGN KEY (id_format_rendu_id) REFERENCES format_rendu (id)');
        $this->addSql('ALTER TABLE user_badges ADD CONSTRAINT FK_1DA448A7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_badges ADD CONSTRAINT FK_1DA448A7D3B81DF7 FOREIGN KEY (id_badges_id) REFERENCES badges (id)');
        $this->addSql('ALTER TABLE user_devoir_vote ADD CONSTRAINT FK_6FEFD2176D052E1 FOREIGN KEY (devoirs_id) REFERENCES devoirs (id)');
        $this->addSql('ALTER TABLE user_devoir_vote ADD CONSTRAINT FK_6FEFD21A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE matieres ADD couleur VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD id_classes_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493BA8DBF3 FOREIGN KEY (id_classes_id) REFERENCES classes (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6493BA8DBF3 ON user (id_classes_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493BA8DBF3');
        $this->addSql('ALTER TABLE checkbox_status DROP FOREIGN KEY FK_32D33657A76ED395');
        $this->addSql('ALTER TABLE checkbox_status DROP FOREIGN KEY FK_32D3365776D052E1');
        $this->addSql('ALTER TABLE devoirs DROP FOREIGN KEY FK_3C781006376858A8');
        $this->addSql('ALTER TABLE devoirs DROP FOREIGN KEY FK_3C7810069A27C5C6');
        $this->addSql('ALTER TABLE devoirs DROP FOREIGN KEY FK_3C7810061C3AC5D2');
        $this->addSql('ALTER TABLE devoirs DROP FOREIGN KEY FK_3C78100687102AB1');
        $this->addSql('ALTER TABLE user_badges DROP FOREIGN KEY FK_1DA448A7A76ED395');
        $this->addSql('ALTER TABLE user_badges DROP FOREIGN KEY FK_1DA448A7D3B81DF7');
        $this->addSql('ALTER TABLE user_devoir_vote DROP FOREIGN KEY FK_6FEFD2176D052E1');
        $this->addSql('ALTER TABLE user_devoir_vote DROP FOREIGN KEY FK_6FEFD21A76ED395');
        $this->addSql('DROP TABLE badges');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE checkbox_status');
        $this->addSql('DROP TABLE classes');
        $this->addSql('DROP TABLE devoirs');
        $this->addSql('DROP TABLE format_rendu');
        $this->addSql('DROP TABLE user_badges');
        $this->addSql('DROP TABLE user_devoir_vote');
        $this->addSql('ALTER TABLE matieres DROP couleur');
        $this->addSql('DROP INDEX IDX_8D93D6493BA8DBF3 ON user');
        $this->addSql('ALTER TABLE user DROP id_classes_id');
    }
}
