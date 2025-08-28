<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250827123958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clean (truck_cleaning_id INT NOT NULL, truck_id INT NOT NULL, INDEX IDX_F1B0AD491FEE3B9 (truck_cleaning_id), INDEX IDX_F1B0AD49C6957CCE (truck_id), PRIMARY KEY(truck_cleaning_id, truck_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, company_name VARCHAR(50) NOT NULL, company_siret VARCHAR(50) NOT NULL, company_contact VARCHAR(50) NOT NULL, unsold TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contains (sales_list_id INT NOT NULL, product_id INT NOT NULL, product_quantity INT NOT NULL, product_discount INT NOT NULL, INDEX IDX_8EFA6A7E33576AEB (sales_list_id), INDEX IDX_8EFA6A7E4584665A (product_id), PRIMARY KEY(sales_list_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE delivery (id INT AUTO_INCREMENT NOT NULL, sales_list_id INT NOT NULL, delivery_date DATE NOT NULL, delivery_address VARCHAR(255) NOT NULL, delivery_number VARCHAR(50) NOT NULL, delivery_status ENUM(\'in_preparation\',\'in_progress\',\'delivered\') NOT NULL COMMENT \'(DC2Type:delivery_status_enum)\', driver_remark LONGTEXT DEFAULT NULL, qr_code VARCHAR(64) NOT NULL, UNIQUE INDEX UNIQ_3781EC107D8B1FB5 (qr_code), UNIQUE INDEX UNIQ_3781EC1033576AEB (sales_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evaluate (sales_list_id INT NOT NULL, reviewer_id INT NOT NULL, quote_accepted TINYINT(1) NOT NULL, INDEX IDX_8E840A8833576AEB (sales_list_id), INDEX IDX_8E840A8870574616 (reviewer_id), PRIMARY KEY(sales_list_id, reviewer_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pricing (id INT AUTO_INCREMENT NOT NULL, fixed_fee DOUBLE PRECISION NOT NULL, modification_date DATETIME NOT NULL, cost_per_km DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, warehouse_id INT NOT NULL, product_name VARCHAR(50) NOT NULL, quantity DOUBLE PRECISION NOT NULL, net_price DOUBLE PRECISION NOT NULL, gross_price DOUBLE PRECISION NOT NULL, unit_weight DOUBLE PRECISION NOT NULL, description LONGTEXT DEFAULT NULL, category ENUM(\'flour\',\'oil\',\'egg\',\'yeast\',\'salt\',\'sugar\',\'butter\',\'milk\',\'seed\',\'chocolate\',\'bread\') NOT NULL COMMENT \'(DC2Type:product_category_enum)\', stock_quantity INT NOT NULL, INDEX IDX_D34A04AD5080ECDE (warehouse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_supplier (product_id INT NOT NULL, supplier_id INT NOT NULL, INDEX IDX_509A06E94584665A (product_id), INDEX IDX_509A06E92ADD6D8C (supplier_id), PRIMARY KEY(product_id, supplier_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quotation (id INT AUTO_INCREMENT NOT NULL, sales_list_id INT DEFAULT NULL, pricing_id INT NOT NULL, total_amount DOUBLE PRECISION NOT NULL, issue_date DATE NOT NULL, due_date DATE NOT NULL, payment_status TINYINT(1) NOT NULL, acceptance_date DATE NOT NULL, UNIQUE INDEX UNIQ_474A8DB933576AEB (sales_list_id), INDEX IDX_474A8DB98864AF73 (pricing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restock (supplier_id INT NOT NULL, truck_id INT NOT NULL, product_id INT NOT NULL, supplier_product_quantity INT NOT NULL, order_number VARCHAR(20) NOT NULL, order_date DATE NOT NULL, order_status VARCHAR(50) NOT NULL, INDEX IDX_33B621E82ADD6D8C (supplier_id), INDEX IDX_33B621E8C6957CCE (truck_id), INDEX IDX_33B621E84584665A (product_id), PRIMARY KEY(supplier_id, truck_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sales_list (id INT AUTO_INCREMENT NOT NULL, status ENUM(\'pending\',\'preparing_products\',\'awaiting_delivery\') NOT NULL COMMENT \'(DC2Type:sales_status_enum)\', products_price DOUBLE PRECISION NOT NULL, global_discount INT NOT NULL, issue_date DATETIME NOT NULL, expiration_date DATETIME NOT NULL, order_date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supplier (id INT AUTO_INCREMENT NOT NULL, supplier_name VARCHAR(255) NOT NULL, supplier_address VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supplier_product (supplier_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_522F70B22ADD6D8C (supplier_id), INDEX IDX_522F70B24584665A (product_id), PRIMARY KEY(supplier_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE truck (id INT AUTO_INCREMENT NOT NULL, delivery_id INT DEFAULT NULL, warehouse_id INT NOT NULL, driver_id INT DEFAULT NULL, registration_number VARCHAR(50) NOT NULL, truck_type ENUM(\'monocuve\', \'porteur_palettes\', \'autre\') NOT NULL COMMENT \'(DC2Type:truck_category_enum)\', is_available TINYINT(1) NOT NULL, delivery_count INT NOT NULL, transport_distance DOUBLE PRECISION NOT NULL, transport_fee DOUBLE PRECISION NOT NULL, INDEX IDX_CDCCF30A12136921 (delivery_id), INDEX IDX_CDCCF30A5080ECDE (warehouse_id), INDEX IDX_CDCCF30AC3423909 (driver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE truck_cleaning (id INT AUTO_INCREMENT NOT NULL, cleaning_start_date DATE NOT NULL, cleaning_end_date DATE NOT NULL, observations LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, last_name VARCHAR(50) NOT NULL, first_name VARCHAR(50) NOT NULL, role ENUM(\'WaitingForValidation\',\'Baker\',\'Sales\',\'Driver\',\'OrderPreparer\',\'Maintenance\',\'Procurement\') NOT NULL COMMENT \'(DC2Type:user_role_enum)\', INDEX IDX_8D93D649979B1AD6 (company_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse (id INT AUTO_INCREMENT NOT NULL, warehouse_address VARCHAR(255) NOT NULL, storage_capacity INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE clean ADD CONSTRAINT FK_F1B0AD491FEE3B9 FOREIGN KEY (truck_cleaning_id) REFERENCES truck_cleaning (id)');
        $this->addSql('ALTER TABLE clean ADD CONSTRAINT FK_F1B0AD49C6957CCE FOREIGN KEY (truck_id) REFERENCES truck (id)');
        $this->addSql('ALTER TABLE contains ADD CONSTRAINT FK_8EFA6A7E33576AEB FOREIGN KEY (sales_list_id) REFERENCES sales_list (id)');
        $this->addSql('ALTER TABLE contains ADD CONSTRAINT FK_8EFA6A7E4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE delivery ADD CONSTRAINT FK_3781EC1033576AEB FOREIGN KEY (sales_list_id) REFERENCES sales_list (id)');
        $this->addSql('ALTER TABLE evaluate ADD CONSTRAINT FK_8E840A8833576AEB FOREIGN KEY (sales_list_id) REFERENCES sales_list (id)');
        $this->addSql('ALTER TABLE evaluate ADD CONSTRAINT FK_8E840A8870574616 FOREIGN KEY (reviewer_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD5080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('ALTER TABLE product_supplier ADD CONSTRAINT FK_509A06E94584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_supplier ADD CONSTRAINT FK_509A06E92ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE quotation ADD CONSTRAINT FK_474A8DB933576AEB FOREIGN KEY (sales_list_id) REFERENCES sales_list (id)');
        $this->addSql('ALTER TABLE quotation ADD CONSTRAINT FK_474A8DB98864AF73 FOREIGN KEY (pricing_id) REFERENCES pricing (id)');
        $this->addSql('ALTER TABLE restock ADD CONSTRAINT FK_33B621E82ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE restock ADD CONSTRAINT FK_33B621E8C6957CCE FOREIGN KEY (truck_id) REFERENCES truck (id)');
        $this->addSql('ALTER TABLE restock ADD CONSTRAINT FK_33B621E84584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE supplier_product ADD CONSTRAINT FK_522F70B22ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE supplier_product ADD CONSTRAINT FK_522F70B24584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE truck ADD CONSTRAINT FK_CDCCF30A12136921 FOREIGN KEY (delivery_id) REFERENCES delivery (id)');
        $this->addSql('ALTER TABLE truck ADD CONSTRAINT FK_CDCCF30A5080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('ALTER TABLE truck ADD CONSTRAINT FK_CDCCF30AC3423909 FOREIGN KEY (driver_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clean DROP FOREIGN KEY FK_F1B0AD491FEE3B9');
        $this->addSql('ALTER TABLE clean DROP FOREIGN KEY FK_F1B0AD49C6957CCE');
        $this->addSql('ALTER TABLE contains DROP FOREIGN KEY FK_8EFA6A7E33576AEB');
        $this->addSql('ALTER TABLE contains DROP FOREIGN KEY FK_8EFA6A7E4584665A');
        $this->addSql('ALTER TABLE delivery DROP FOREIGN KEY FK_3781EC1033576AEB');
        $this->addSql('ALTER TABLE evaluate DROP FOREIGN KEY FK_8E840A8833576AEB');
        $this->addSql('ALTER TABLE evaluate DROP FOREIGN KEY FK_8E840A8870574616');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD5080ECDE');
        $this->addSql('ALTER TABLE product_supplier DROP FOREIGN KEY FK_509A06E94584665A');
        $this->addSql('ALTER TABLE product_supplier DROP FOREIGN KEY FK_509A06E92ADD6D8C');
        $this->addSql('ALTER TABLE quotation DROP FOREIGN KEY FK_474A8DB933576AEB');
        $this->addSql('ALTER TABLE quotation DROP FOREIGN KEY FK_474A8DB98864AF73');
        $this->addSql('ALTER TABLE restock DROP FOREIGN KEY FK_33B621E82ADD6D8C');
        $this->addSql('ALTER TABLE restock DROP FOREIGN KEY FK_33B621E8C6957CCE');
        $this->addSql('ALTER TABLE restock DROP FOREIGN KEY FK_33B621E84584665A');
        $this->addSql('ALTER TABLE supplier_product DROP FOREIGN KEY FK_522F70B22ADD6D8C');
        $this->addSql('ALTER TABLE supplier_product DROP FOREIGN KEY FK_522F70B24584665A');
        $this->addSql('ALTER TABLE truck DROP FOREIGN KEY FK_CDCCF30A12136921');
        $this->addSql('ALTER TABLE truck DROP FOREIGN KEY FK_CDCCF30A5080ECDE');
        $this->addSql('ALTER TABLE truck DROP FOREIGN KEY FK_CDCCF30AC3423909');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649979B1AD6');
        $this->addSql('DROP TABLE clean');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE contains');
        $this->addSql('DROP TABLE delivery');
        $this->addSql('DROP TABLE evaluate');
        $this->addSql('DROP TABLE pricing');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_supplier');
        $this->addSql('DROP TABLE quotation');
        $this->addSql('DROP TABLE restock');
        $this->addSql('DROP TABLE sales_list');
        $this->addSql('DROP TABLE supplier');
        $this->addSql('DROP TABLE supplier_product');
        $this->addSql('DROP TABLE truck');
        $this->addSql('DROP TABLE truck_cleaning');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE warehouse');
    }
}
