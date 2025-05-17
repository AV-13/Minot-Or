<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250516082256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD pricing_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD CONSTRAINT FK_906517448864AF73 FOREIGN KEY (pricing_id) REFERENCES pricing (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_906517448864AF73 ON invoice (pricing_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP FOREIGN KEY FK_906517448864AF73
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_906517448864AF73 ON invoice
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP pricing_id
        SQL);
    }
}
