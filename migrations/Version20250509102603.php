<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250509102603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE clean (truck_cleaning_id INT NOT NULL, truck_id INT NOT NULL, INDEX IDX_F1B0AD491FEE3B9 (truck_cleaning_id), INDEX IDX_F1B0AD49C6957CCE (truck_id), PRIMARY KEY(truck_cleaning_id, truck_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, company_name VARCHAR(50) NOT NULL, company_siret VARCHAR(50) NOT NULL, company_contact VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE contains (sales_list_id INT NOT NULL, product_id INT NOT NULL, product_quantity INT NOT NULL, product_discount INT NOT NULL, INDEX IDX_8EFA6A7E33576AEB (sales_list_id), INDEX IDX_8EFA6A7E4584665A (product_id), PRIMARY KEY(sales_list_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE delivery (id INT AUTO_INCREMENT NOT NULL, sales_list_id INT NOT NULL, delivery_date DATE NOT NULL, delivery_address VARCHAR(50) NOT NULL, delivery_number VARCHAR(20) NOT NULL, delivery_status VARCHAR(20) NOT NULL, driver_remark LONGTEXT NOT NULL, qr_code LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_3781EC1033576AEB (sales_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE evaluate (sales_list_id INT NOT NULL, reviewer_id INT NOT NULL, quote_accepted TINYINT(1) NOT NULL, INDEX IDX_8E840A8833576AEB (sales_list_id), INDEX IDX_8E840A8870574616 (reviewer_id), PRIMARY KEY(sales_list_id, reviewer_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, sales_list_id INT DEFAULT NULL, total_amount DOUBLE PRECISION NOT NULL, issue_date DATE NOT NULL, due_date DATE NOT NULL, payment_status TINYINT(1) NOT NULL, acceptance_date DATE NOT NULL, UNIQUE INDEX UNIQ_9065174433576AEB (sales_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE pricing (id INT AUTO_INCREMENT NOT NULL, fixed_fee DOUBLE PRECISION NOT NULL, modification_date DATETIME NOT NULL, cost_per_km DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, warehouse_id INT NOT NULL, product_name VARCHAR(50) NOT NULL, quantity DOUBLE PRECISION NOT NULL, net_price DOUBLE PRECISION NOT NULL, gross_price DOUBLE PRECISION NOT NULL, unit_weight DOUBLE PRECISION NOT NULL, category VARCHAR(20) NOT NULL, stock_quantity INT NOT NULL, INDEX IDX_D34A04AD5080ECDE (warehouse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product_supplier (product_id INT NOT NULL, supplier_id INT NOT NULL, INDEX IDX_509A06E94584665A (product_id), INDEX IDX_509A06E92ADD6D8C (supplier_id), PRIMARY KEY(product_id, supplier_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE restock (supplier_id INT NOT NULL, truck_id INT NOT NULL, product_id INT NOT NULL, supplier_product_quantity INT NOT NULL, order_number VARCHAR(20) NOT NULL, order_date DATE NOT NULL, order_status VARCHAR(50) NOT NULL, INDEX IDX_33B621E82ADD6D8C (supplier_id), INDEX IDX_33B621E8C6957CCE (truck_id), INDEX IDX_33B621E84584665A (product_id), PRIMARY KEY(supplier_id, truck_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE sales_list (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(20) NOT NULL, products_price DOUBLE PRECISION NOT NULL, global_discount INT NOT NULL, issue_date DATETIME NOT NULL, expiration_date DATETIME NOT NULL, order_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE supplier (id INT AUTO_INCREMENT NOT NULL, supplier_name VARCHAR(50) NOT NULL, supplier_address VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE truck (id INT AUTO_INCREMENT NOT NULL, delivery_id INT DEFAULT NULL, warehouse_id INT NOT NULL, driver_id INT DEFAULT NULL, registration_number VARCHAR(50) NOT NULL, truck_type VARCHAR(50) NOT NULL, is_available TINYINT(1) NOT NULL, delivery_count INT NOT NULL, transport_distance DOUBLE PRECISION NOT NULL, transport_fee DOUBLE PRECISION NOT NULL, INDEX IDX_CDCCF30A12136921 (delivery_id), INDEX IDX_CDCCF30A5080ECDE (warehouse_id), INDEX IDX_CDCCF30AC3423909 (driver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE truck_cleaning (id INT AUTO_INCREMENT NOT NULL, cleaning_start_date DATE NOT NULL, cleaning_end_date DATE NOT NULL, observations LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, last_name VARCHAR(50) NOT NULL, first_name VARCHAR(50) NOT NULL, role VARCHAR(20) NOT NULL, INDEX IDX_8D93D649979B1AD6 (company_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE warehouse (id INT AUTO_INCREMENT NOT NULL, warehouse_address VARCHAR(255) NOT NULL, storage_capacity INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE clean ADD CONSTRAINT FK_F1B0AD491FEE3B9 FOREIGN KEY (truck_cleaning_id) REFERENCES truck_cleaning (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE clean ADD CONSTRAINT FK_F1B0AD49C6957CCE FOREIGN KEY (truck_id) REFERENCES truck (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contains ADD CONSTRAINT FK_8EFA6A7E33576AEB FOREIGN KEY (sales_list_id) REFERENCES sales_list (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contains ADD CONSTRAINT FK_8EFA6A7E4584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery ADD CONSTRAINT FK_3781EC1033576AEB FOREIGN KEY (sales_list_id) REFERENCES sales_list (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evaluate ADD CONSTRAINT FK_8E840A8833576AEB FOREIGN KEY (sales_list_id) REFERENCES sales_list (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evaluate ADD CONSTRAINT FK_8E840A8870574616 FOREIGN KEY (reviewer_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD CONSTRAINT FK_9065174433576AEB FOREIGN KEY (sales_list_id) REFERENCES sales_list (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product ADD CONSTRAINT FK_D34A04AD5080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_supplier ADD CONSTRAINT FK_509A06E94584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_supplier ADD CONSTRAINT FK_509A06E92ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE restock ADD CONSTRAINT FK_33B621E82ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE restock ADD CONSTRAINT FK_33B621E8C6957CCE FOREIGN KEY (truck_id) REFERENCES truck (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE restock ADD CONSTRAINT FK_33B621E84584665A FOREIGN KEY (product_id) REFERENCES product (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE truck ADD CONSTRAINT FK_CDCCF30A12136921 FOREIGN KEY (delivery_id) REFERENCES delivery (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE truck ADD CONSTRAINT FK_CDCCF30A5080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE truck ADD CONSTRAINT FK_CDCCF30AC3423909 FOREIGN KEY (driver_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE clean DROP FOREIGN KEY FK_F1B0AD491FEE3B9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE clean DROP FOREIGN KEY FK_F1B0AD49C6957CCE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contains DROP FOREIGN KEY FK_8EFA6A7E33576AEB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contains DROP FOREIGN KEY FK_8EFA6A7E4584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE delivery DROP FOREIGN KEY FK_3781EC1033576AEB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evaluate DROP FOREIGN KEY FK_8E840A8833576AEB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evaluate DROP FOREIGN KEY FK_8E840A8870574616
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP FOREIGN KEY FK_9065174433576AEB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD5080ECDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_supplier DROP FOREIGN KEY FK_509A06E94584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE product_supplier DROP FOREIGN KEY FK_509A06E92ADD6D8C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE restock DROP FOREIGN KEY FK_33B621E82ADD6D8C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE restock DROP FOREIGN KEY FK_33B621E8C6957CCE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE restock DROP FOREIGN KEY FK_33B621E84584665A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE truck DROP FOREIGN KEY FK_CDCCF30A12136921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE truck DROP FOREIGN KEY FK_CDCCF30A5080ECDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE truck DROP FOREIGN KEY FK_CDCCF30AC3423909
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649979B1AD6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE clean
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE company
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE contains
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE delivery
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE evaluate
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE invoice
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE pricing
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product_supplier
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE restock
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE sales_list
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE supplier
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE truck
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE truck_cleaning
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `user`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE warehouse
        SQL);
    }
}
