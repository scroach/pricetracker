<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181226234227 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE price_history (id INT AUTO_INCREMENT NOT NULL, request_id INT DEFAULT NULL, value DOUBLE PRECISION NOT NULL, timestamp DATETIME NOT NULL, INDEX IDX_4C9CB817427EB8A5 (request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE request_log (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, request_headers LONGTEXT NOT NULL, request_body LONGTEXT NOT NULL, response_headers LONGTEXT NOT NULL, response_body LONGTEXT NOT NULL, timestamp DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE price_history ADD CONSTRAINT FK_4C9CB817427EB8A5 FOREIGN KEY (request_id) REFERENCES request_log (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE price_history DROP FOREIGN KEY FK_4C9CB817427EB8A5');
        $this->addSql('DROP TABLE price_history');
        $this->addSql('DROP TABLE request_log');
    }
}
