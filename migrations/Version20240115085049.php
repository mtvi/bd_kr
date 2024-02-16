<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240115085049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, category_name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gpu (id INT AUTO_INCREMENT NOT NULL, manufacturer_id INT NOT NULL, memory_id INT DEFAULT NULL, pciversion_id INT DEFAULT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, release_date DATE NOT NULL, core_clock INT NOT NULL, boost_clock INT NOT NULL, vram INT NOT NULL, tdp INT NOT NULL, INDEX IDX_BD89F8F2A23B42D (manufacturer_id), INDEX IDX_BD89F8F2CCC80CB3 (memory_id), INDEX IDX_BD89F8F269E36330 (pciversion_id), INDEX IDX_BD89F8F212469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manufacturers (id INT AUTO_INCREMENT NOT NULL, manufacturer_name VARCHAR(50) NOT NULL, website VARCHAR(50) NOT NULL, country VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE memory_types (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, speed INT DEFAULT NULL, bus_width INT DEFAULT NULL, band_width INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_details (id INT AUTO_INCREMENT NOT NULL, order_id_id INT DEFAULT NULL, product_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_845CA2C1FCDAEAAA (order_id_id), INDEX IDX_845CA2C14584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, customer_name VARCHAR(255) NOT NULL, orader_date DATETIME NOT NULL, total_price NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pci (id INT AUTO_INCREMENT NOT NULL, version VARCHAR(20) NOT NULL, band_width INT NOT NULL, lanes_number INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, gpu_id INT DEFAULT NULL, vendor_id INT DEFAULT NULL, image VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, cooling_type VARCHAR(255) NOT NULL, INDEX IDX_B3BA5A5A98003202 (gpu_id), INDEX IDX_B3BA5A5AF603EE73 (vendor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reviews (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, review_date DATETIME NOT NULL, review_text LONGTEXT NOT NULL, rating INT NOT NULL, reviewer VARCHAR(50) NOT NULL, INDEX IDX_6970EB0F4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vendors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, website VARCHAR(50) NOT NULL, country VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gpu ADD CONSTRAINT FK_BD89F8F2A23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturers (id)');
        $this->addSql('ALTER TABLE gpu ADD CONSTRAINT FK_BD89F8F2CCC80CB3 FOREIGN KEY (memory_id) REFERENCES memory_types (id)');
        $this->addSql('ALTER TABLE gpu ADD CONSTRAINT FK_BD89F8F269E36330 FOREIGN KEY (pciversion_id) REFERENCES pci (id)');
        $this->addSql('ALTER TABLE gpu ADD CONSTRAINT FK_BD89F8F212469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C1FCDAEAAA FOREIGN KEY (order_id_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C14584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A98003202 FOREIGN KEY (gpu_id) REFERENCES gpu (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AF603EE73 FOREIGN KEY (vendor_id) REFERENCES vendors (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F4584665A FOREIGN KEY (product_id) REFERENCES products (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gpu DROP FOREIGN KEY FK_BD89F8F2A23B42D');
        $this->addSql('ALTER TABLE gpu DROP FOREIGN KEY FK_BD89F8F2CCC80CB3');
        $this->addSql('ALTER TABLE gpu DROP FOREIGN KEY FK_BD89F8F269E36330');
        $this->addSql('ALTER TABLE gpu DROP FOREIGN KEY FK_BD89F8F212469DE2');
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C1FCDAEAAA');
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C14584665A');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A98003202');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5AF603EE73');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F4584665A');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE gpu');
        $this->addSql('DROP TABLE manufacturers');
        $this->addSql('DROP TABLE memory_types');
        $this->addSql('DROP TABLE order_details');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE pci');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE reviews');
        $this->addSql('DROP TABLE vendors');
    }
}
