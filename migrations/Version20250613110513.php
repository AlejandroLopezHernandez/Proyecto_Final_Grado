<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250613110513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE bebida (id INT AUTO_INCREMENT NOT NULL, estilo_id INT DEFAULT NULL, fabricante_id INT DEFAULT NULL, nombre VARCHAR(255) DEFAULT NULL, grado_alcoholico DOUBLE PRECISION DEFAULT NULL, tipo_bebida VARCHAR(255) DEFAULT NULL, formato VARCHAR(255) DEFAULT NULL, coste DOUBLE PRECISION DEFAULT NULL, pvp DOUBLE PRECISION DEFAULT NULL, stock INT DEFAULT NULL, descripcion LONGTEXT DEFAULT NULL, lupulos VARCHAR(255) DEFAULT NULL, INDEX IDX_4821C78543798DA7 (estilo_id), INDEX IDX_4821C785C0A0FDFA (fabricante_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE bebida_proveedor (bebida_id INT NOT NULL, proveedor_id INT NOT NULL, INDEX IDX_C5EE3CC1496D4DC4 (bebida_id), INDEX IDX_C5EE3CC1CB305D73 (proveedor_id), PRIMARY KEY(bebida_id, proveedor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE comanda (id INT AUTO_INCREMENT NOT NULL, productos JSON DEFAULT NULL, fecha DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE comida (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) DEFAULT NULL, pvp DOUBLE PRECISION DEFAULT NULL, descripcion LONGTEXT DEFAULT NULL, receta LONGTEXT DEFAULT NULL, categoria JSON NOT NULL, stock INT DEFAULT NULL, dieta VARCHAR(255) DEFAULT NULL, opciones JSON DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE estilo (id INT AUTO_INCREMENT NOT NULL, estilo_padre_id INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, descripcion LONGTEXT DEFAULT NULL, maridaje VARCHAR(255) DEFAULT NULL, fermentacion VARCHAR(255) DEFAULT NULL, color VARCHAR(255) DEFAULT NULL, sabor VARCHAR(255) DEFAULT NULL, aroma VARCHAR(255) DEFAULT NULL, carbonatacion VARCHAR(255) DEFAULT NULL, origen VARCHAR(255) DEFAULT NULL, INDEX IDX_E0973A532C05C80E (estilo_padre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE fabricante (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) DEFAULT NULL, pais VARCHAR(255) DEFAULT NULL, descripcion LONGTEXT DEFAULT NULL, imagen VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE mesa (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) DEFAULT NULL, posicion_x INT DEFAULT NULL, posicion_y INT DEFAULT NULL, activa TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE producto (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) DEFAULT NULL, coste DOUBLE PRECISION DEFAULT NULL, medida VARCHAR(255) DEFAULT NULL, stock INT DEFAULT NULL, descripcion VARCHAR(255) DEFAULT NULL, categoria VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE producto_proveedor (producto_id INT NOT NULL, proveedor_id INT NOT NULL, INDEX IDX_E051DDE47645698E (producto_id), INDEX IDX_E051DDE4CB305D73 (proveedor_id), PRIMARY KEY(producto_id, proveedor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE producto_comida (producto_id INT NOT NULL, comida_id INT NOT NULL, INDEX IDX_3C89B3997645698E (producto_id), INDEX IDX_3C89B399399E35A6 (comida_id), PRIMARY KEY(producto_id, comida_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE proveedor (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, telefono VARCHAR(255) DEFAULT NULL, descripcion VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reserva (id INT AUTO_INCREMENT NOT NULL, nombre_cliente VARCHAR(255) DEFAULT NULL, email_cliente VARCHAR(255) DEFAULT NULL, telefono_cliente VARCHAR(255) DEFAULT NULL, numero_comensales INT DEFAULT NULL, fecha_hora_reserva DATETIME DEFAULT NULL, numero_mesa INT DEFAULT NULL, info_adicional VARCHAR(255) DEFAULT NULL, estado VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(180) NOT NULL, foto VARCHAR(255) DEFAULT NULL, estado TINYINT(1) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_NOMBRE (nombre), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bebida ADD CONSTRAINT FK_4821C78543798DA7 FOREIGN KEY (estilo_id) REFERENCES estilo (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bebida ADD CONSTRAINT FK_4821C785C0A0FDFA FOREIGN KEY (fabricante_id) REFERENCES fabricante (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bebida_proveedor ADD CONSTRAINT FK_C5EE3CC1496D4DC4 FOREIGN KEY (bebida_id) REFERENCES bebida (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bebida_proveedor ADD CONSTRAINT FK_C5EE3CC1CB305D73 FOREIGN KEY (proveedor_id) REFERENCES proveedor (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE estilo ADD CONSTRAINT FK_E0973A532C05C80E FOREIGN KEY (estilo_padre_id) REFERENCES estilo (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto_proveedor ADD CONSTRAINT FK_E051DDE47645698E FOREIGN KEY (producto_id) REFERENCES producto (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto_proveedor ADD CONSTRAINT FK_E051DDE4CB305D73 FOREIGN KEY (proveedor_id) REFERENCES proveedor (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto_comida ADD CONSTRAINT FK_3C89B3997645698E FOREIGN KEY (producto_id) REFERENCES producto (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto_comida ADD CONSTRAINT FK_3C89B399399E35A6 FOREIGN KEY (comida_id) REFERENCES comida (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE bebida DROP FOREIGN KEY FK_4821C78543798DA7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bebida DROP FOREIGN KEY FK_4821C785C0A0FDFA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bebida_proveedor DROP FOREIGN KEY FK_C5EE3CC1496D4DC4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bebida_proveedor DROP FOREIGN KEY FK_C5EE3CC1CB305D73
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE estilo DROP FOREIGN KEY FK_E0973A532C05C80E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto_proveedor DROP FOREIGN KEY FK_E051DDE47645698E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto_proveedor DROP FOREIGN KEY FK_E051DDE4CB305D73
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto_comida DROP FOREIGN KEY FK_3C89B3997645698E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto_comida DROP FOREIGN KEY FK_3C89B399399E35A6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE bebida
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE bebida_proveedor
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE comanda
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE comida
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE estilo
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE fabricante
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE mesa
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE producto
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE producto_proveedor
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE producto_comida
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE proveedor
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reserva
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE usuario
        SQL);
    }
}
