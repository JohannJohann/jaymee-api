<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190907100511 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE flag (id INT AUTO_INCREMENT NOT NULL, flagged_by_id INT NOT NULL, flagged_content_id INT NOT NULL, flagged_user_id INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_D1F4EB9A60B67306 (flagged_by_id), INDEX IDX_D1F4EB9A7BB32B82 (flagged_content_id), INDEX IDX_D1F4EB9A673A7B08 (flagged_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_blocks (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_ABBF8E453AD8644E (user_source), INDEX IDX_ABBF8E45233D34C1 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE flag ADD CONSTRAINT FK_D1F4EB9A60B67306 FOREIGN KEY (flagged_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE flag ADD CONSTRAINT FK_D1F4EB9A7BB32B82 FOREIGN KEY (flagged_content_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE flag ADD CONSTRAINT FK_D1F4EB9A673A7B08 FOREIGN KEY (flagged_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_blocks ADD CONSTRAINT FK_ABBF8E453AD8644E FOREIGN KEY (user_source) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_blocks ADD CONSTRAINT FK_ABBF8E45233D34C1 FOREIGN KEY (user_target) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE flag');
        $this->addSql('DROP TABLE user_blocks');
    }
}
