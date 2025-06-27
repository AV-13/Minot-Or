<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250627122732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE quotation (id INT AUTO_INCREMENT NOT NULL, sales_list_id INT DEFAULT NULL, pricing_id INT NOT NULL, total_amount DOUBLE PRECISION NOT NULL, issue_date DATE NOT NULL, due_date DATE NOT NULL, payment_status TINYINT(1) NOT NULL, acceptance_date DATE NOT NULL, UNIQUE INDEX UNIQ_474A8DB933576AEB (sales_list_id), INDEX IDX_474A8DB98864AF73 (pricing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quotation ADD CONSTRAINT FK_474A8DB933576AEB FOREIGN KEY (sales_list_id) REFERENCES sales_list (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quotation ADD CONSTRAINT FK_474A8DB98864AF73 FOREIGN KEY (pricing_id) REFERENCES pricing (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP FOREIGN KEY FK_9065174433576AEB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP FOREIGN KEY FK_906517448864AF73
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE invoice
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, sales_list_id INT DEFAULT NULL, pricing_id INT NOT NULL, total_amount DOUBLE PRECISION NOT NULL, issue_date DATE NOT NULL, due_date DATE NOT NULL, payment_status TINYINT(1) NOT NULL, acceptance_date DATE NOT NULL, UNIQUE INDEX UNIQ_9065174433576AEB (sales_list_id), INDEX IDX_906517448864AF73 (pricing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD CONSTRAINT FK_9065174433576AEB FOREIGN KEY (sales_list_id) REFERENCES sales_list (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD CONSTRAINT FK_906517448864AF73 FOREIGN KEY (pricing_id) REFERENCES pricing (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quotation DROP FOREIGN KEY FK_474A8DB933576AEB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quotation DROP FOREIGN KEY FK_474A8DB98864AF73
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quotation
        SQL);
    }
}
