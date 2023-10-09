<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231009021347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', id_parent BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, url_rewrite VARCHAR(255) NOT NULL, permalink VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_64C19C1F286BC32 (permalink), INDEX IDX_64C19C11BB9D5A2 (id_parent), INDEX permalink_idx (permalink), PRIMARY KEY(_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page (_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', id_user BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', id_main_category BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', id_page INT AUTO_INCREMENT, title VARCHAR(50) NOT NULL, resume VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, date_update DATETIME DEFAULT NULL, status INT UNSIGNED DEFAULT 1 NOT NULL, tracking_view INT DEFAULT 0 NOT NULL, url_rewrite VARCHAR(255) NOT NULL, permalink VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_140AB620B8985B8B (url_rewrite), UNIQUE INDEX UNIQ_140AB620F286BC32 (permalink), INDEX status_idx (status), INDEX id_page_status_idx (id_page, status), INDEX id_main_category_idx (id_main_category), INDEX id_user_idx (id_user), PRIMARY KEY(_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page_category (id_page BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', id_category BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_86D31EE19F2AAA22 (id_page), INDEX IDX_86D31EE15697F554 (id_category), PRIMARY KEY(id_page, id_category)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', id_parent BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(255) NOT NULL, fullname VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D6491BB9D5A2 (id_parent), INDEX email_idx (email), PRIMARY KEY(_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C11BB9D5A2 FOREIGN KEY (id_parent) REFERENCES category (_id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB6206B3CA4B FOREIGN KEY (id_user) REFERENCES user (_id)');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB6206146D9D1 FOREIGN KEY (id_main_category) REFERENCES category (_id)');
        $this->addSql('ALTER TABLE page_category ADD CONSTRAINT FK_86D31EE19F2AAA22 FOREIGN KEY (id_page) REFERENCES page (_id)');
        $this->addSql('ALTER TABLE page_category ADD CONSTRAINT FK_86D31EE15697F554 FOREIGN KEY (id_category) REFERENCES category (_id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6491BB9D5A2 FOREIGN KEY (id_parent) REFERENCES user (_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C11BB9D5A2');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB6206B3CA4B');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB6206146D9D1');
        $this->addSql('ALTER TABLE page_category DROP FOREIGN KEY FK_86D31EE19F2AAA22');
        $this->addSql('ALTER TABLE page_category DROP FOREIGN KEY FK_86D31EE15697F554');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6491BB9D5A2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE page_category');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
