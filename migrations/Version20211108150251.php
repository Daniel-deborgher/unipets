<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211108150251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sujet ADD authors_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sujet ADD CONSTRAINT FK_2E13599D6DE2013A FOREIGN KEY (authors_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_2E13599D6DE2013A ON sujet (authors_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sujet DROP FOREIGN KEY FK_2E13599D6DE2013A');
        $this->addSql('DROP INDEX IDX_2E13599D6DE2013A ON sujet');
        $this->addSql('ALTER TABLE sujet DROP authors_id');
    }
}
