<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250430155610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE producto_comida (id INT AUTO_INCREMENT NOT NULL, comida_id INT DEFAULT NULL, producto_id INT DEFAULT NULL, INDEX IDX_3C89B399399E35A6 (comida_id), INDEX IDX_3C89B3997645698E (producto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto_comida ADD CONSTRAINT FK_3C89B399399E35A6 FOREIGN KEY (comida_id) REFERENCES comida (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto_comida ADD CONSTRAINT FK_3C89B3997645698E FOREIGN KEY (producto_id) REFERENCES producto (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comida_producto DROP FOREIGN KEY FK_13A72634399E35A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comida_producto DROP FOREIGN KEY FK_13A726347645698E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE comida_producto
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE comida_producto (comida_id INT NOT NULL, producto_id INT NOT NULL, INDEX IDX_13A726347645698E (producto_id), INDEX IDX_13A72634399E35A6 (comida_id), PRIMARY KEY(comida_id, producto_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comida_producto ADD CONSTRAINT FK_13A72634399E35A6 FOREIGN KEY (comida_id) REFERENCES comida (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comida_producto ADD CONSTRAINT FK_13A726347645698E FOREIGN KEY (producto_id) REFERENCES producto (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto_comida DROP FOREIGN KEY FK_3C89B399399E35A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto_comida DROP FOREIGN KEY FK_3C89B3997645698E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE producto_comida
        SQL);
    }
}
