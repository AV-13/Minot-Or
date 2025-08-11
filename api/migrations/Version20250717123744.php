<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250717123744 extends AbstractMigration
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
            CREATE TABLE supplier_product (supplier_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_522F70B22ADD6D8C (supplier_id), INDEX IDX_522F70B24584665A (product_id), PRIMARY KEY(supplier_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quotation ADD CONSTRAINT FK_474A8DB933576AEB FOREIGN KEY (sales_list_id) REFERENCES sales_list (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quotation ADD CONSTRAINT FK_474A8DB98864AF73 FOREIGN KEY (pricing_id) REFERENCES pricing (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE supplier_product ADD CONSTRAINT FK_522F70B22ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE supplier_product ADD CONSTRAINT FK_522F70B24584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE
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
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery CHANGE delivery_address delivery_address VARCHAR(255) NOT NULL, CHANGE delivery_number delivery_number VARCHAR(50) NOT NULL, CHANGE delivery_status delivery_status ENUM('in_preparation','in_progress','delivered') NOT NULL COMMENT '(DC2Type:delivery_status_enum)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD description LONGTEXT DEFAULT NULL, CHANGE category category ENUM('flour','oil','egg','yeast','salt','sugar','butter','milk','seed','chocolate','bread') NOT NULL COMMENT '(DC2Type:product_category_enum)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sales_list CHANGE status status ENUM('pending','preparing_products','awaiting_delivery') NOT NULL COMMENT '(DC2Type:sales_status_enum)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE supplier CHANGE supplier_name supplier_name VARCHAR(255) NOT NULL, CHANGE supplier_address supplier_address VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE truck CHANGE truck_type truck_type ENUM('monocuve', 'porteur_palettes', 'autre') NOT NULL COMMENT '(DC2Type:truck_category_enum)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE role role ENUM('WaitingForValidation','Baker','Sales','Driver','OrderPreparer','Maintenance','Procurement') NOT NULL COMMENT '(DC2Type:user_role_enum)'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, sales_list_id INT DEFAULT NULL, pricing_id INT NOT NULL, total_amount DOUBLE PRECISION NOT NULL, issue_date DATE NOT NULL, due_date DATE NOT NULL, payment_status TINYINT(1) NOT NULL, acceptance_date DATE NOT NULL, INDEX IDX_906517448864AF73 (pricing_id), UNIQUE INDEX UNIQ_9065174433576AEB (sales_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
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
            ALTER TABLE supplier_product DROP FOREIGN KEY FK_522F70B22ADD6D8C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE supplier_product DROP FOREIGN KEY FK_522F70B24584665A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quotation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE supplier_product
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE supplier CHANGE supplier_name supplier_name VARCHAR(50) NOT NULL, CHANGE supplier_address supplier_address VARCHAR(50) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sales_list CHANGE status status VARCHAR(20) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` CHANGE role role VARCHAR(20) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP description, CHANGE category category VARCHAR(20) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery CHANGE delivery_address delivery_address VARCHAR(50) NOT NULL, CHANGE delivery_number delivery_number VARCHAR(20) NOT NULL, CHANGE delivery_status delivery_status VARCHAR(20) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE truck CHANGE truck_type truck_type VARCHAR(50) NOT NULL
        SQL);
    }
}
