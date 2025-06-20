<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250620085821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery CHANGE delivery_address delivery_address VARCHAR(255) NOT NULL, CHANGE delivery_number delivery_number VARCHAR(50) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD description LONGTEXT DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP description
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery CHANGE delivery_address delivery_address VARCHAR(50) NOT NULL, CHANGE delivery_number delivery_number VARCHAR(20) NOT NULL
        SQL);
    }
}
