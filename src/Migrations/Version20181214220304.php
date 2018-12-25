<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181214220304 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE quizz (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, question VARCHAR(255) NOT NULL, choices LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', correct_answer VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, attempts INT NOT NULL, successes INT NOT NULL, INDEX IDX_7C77973D7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quizz_answered_by (quizz_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_1F5D9A7CBA934BCD (quizz_id), INDEX IDX_1F5D9A7CA76ED395 (user_id), PRIMARY KEY(quizz_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, filename VARCHAR(255) NOT NULL, cost INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_C53D045F7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image_viewed_by (image_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_415BA1573DA5256D (image_id), INDEX IDX_415BA157A76ED395 (user_id), PRIMARY KEY(image_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `key` (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, target_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_8A90ABA97E3C61F9 (owner_id), INDEX IDX_8A90ABA9158E0B66 (target_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, instagram_token VARCHAR(255) DEFAULT NULL, username VARCHAR(255) NOT NULL, profile_pic VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE follows (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_4B638A733AD8644E (user_source), INDEX IDX_4B638A73233D34C1 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quizz ADD CONSTRAINT FK_7C77973D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE quizz_answered_by ADD CONSTRAINT FK_1F5D9A7CBA934BCD FOREIGN KEY (quizz_id) REFERENCES quizz (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quizz_answered_by ADD CONSTRAINT FK_1F5D9A7CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE image_viewed_by ADD CONSTRAINT FK_415BA1573DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_viewed_by ADD CONSTRAINT FK_415BA157A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `key` ADD CONSTRAINT FK_8A90ABA97E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `key` ADD CONSTRAINT FK_8A90ABA9158E0B66 FOREIGN KEY (target_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE follows ADD CONSTRAINT FK_4B638A733AD8644E FOREIGN KEY (user_source) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE follows ADD CONSTRAINT FK_4B638A73233D34C1 FOREIGN KEY (user_target) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE quizz_answered_by DROP FOREIGN KEY FK_1F5D9A7CBA934BCD');
        $this->addSql('ALTER TABLE image_viewed_by DROP FOREIGN KEY FK_415BA1573DA5256D');
        $this->addSql('ALTER TABLE quizz DROP FOREIGN KEY FK_7C77973D7E3C61F9');
        $this->addSql('ALTER TABLE quizz_answered_by DROP FOREIGN KEY FK_1F5D9A7CA76ED395');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F7E3C61F9');
        $this->addSql('ALTER TABLE image_viewed_by DROP FOREIGN KEY FK_415BA157A76ED395');
        $this->addSql('ALTER TABLE `key` DROP FOREIGN KEY FK_8A90ABA97E3C61F9');
        $this->addSql('ALTER TABLE `key` DROP FOREIGN KEY FK_8A90ABA9158E0B66');
        $this->addSql('ALTER TABLE follows DROP FOREIGN KEY FK_4B638A733AD8644E');
        $this->addSql('ALTER TABLE follows DROP FOREIGN KEY FK_4B638A73233D34C1');
        $this->addSql('DROP TABLE quizz');
        $this->addSql('DROP TABLE quizz_answered_by');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE image_viewed_by');
        $this->addSql('DROP TABLE `key`');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE follows');
    }
}
