<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190601154803 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE customer_order_item (id INT AUTO_INCREMENT NOT NULL, customer_order_id INT NOT NULL, item_id INT NOT NULL, amount INT NOT NULL, created DATETIME NOT NULL, INDEX IDX_AF231B8BA15A2E17 (customer_order_id), INDEX IDX_AF231B8B126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, firstName VARCHAR(255) NOT NULL, lastName VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(96) NOT NULL COMMENT \'Use password hash with BCRYPT\', created DATETIME NOT NULL, roles JSON NOT NULL COMMENT \'Security roles\', UNIQUE INDEX UNIQ_81398E09F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_order (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, created DATETIME NOT NULL, modified DATETIME DEFAULT NULL, status SMALLINT NOT NULL, INDEX IDX_3B1CE6A39395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, price BIGINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer_order_item ADD CONSTRAINT FK_AF231B8BA15A2E17 FOREIGN KEY (customer_order_id) REFERENCES customer_order (id)');
        $this->addSql('ALTER TABLE customer_order_item ADD CONSTRAINT FK_AF231B8B126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE customer_order ADD CONSTRAINT FK_3B1CE6A39395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_order DROP FOREIGN KEY FK_3B1CE6A39395C3F3');
        $this->addSql('ALTER TABLE customer_order_item DROP FOREIGN KEY FK_AF231B8BA15A2E17');
        $this->addSql('ALTER TABLE customer_order_item DROP FOREIGN KEY FK_AF231B8B126F525E');
        $this->addSql('DROP TABLE customer_order_item');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE customer_order');
        $this->addSql('DROP TABLE item');
    }
}
