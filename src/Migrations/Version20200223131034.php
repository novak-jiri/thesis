<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200223131034 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE b2s (id INT AUTO_INCREMENT NOT NULL, sequence_id_id INT NOT NULL, block_id_id INT NOT NULL, sort INT NOT NULL, INDEX IDX_906EB2AB4C8374E (sequence_id_id), INDEX IDX_906EB2ABB85558B1 (block_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE block (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, acronym VARCHAR(255) NOT NULL, residue VARCHAR(255) NOT NULL, mass DOUBLE PRECISION DEFAULT NULL, losses VARCHAR(255) DEFAULT NULL, smiles VARCHAR(255) DEFAULT NULL, source SMALLINT DEFAULT NULL, indetifier VARCHAR(255) DEFAULT NULL, family VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE modification (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, formula VARCHAR(255) NOT NULL, mass DOUBLE PRECISION DEFAULT NULL, n_terminal TINYINT(1) DEFAULT \'0\' NOT NULL, c_terminal TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sequence (id INT AUTO_INCREMENT NOT NULL, n_modification_id_id INT DEFAULT NULL, c_modification_id_id INT DEFAULT NULL, b_modification_id_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, formula VARCHAR(255) NOT NULL, mass DOUBLE PRECISION DEFAULT NULL, sequence VARCHAR(255) DEFAULT NULL, smiles VARCHAR(500) DEFAULT NULL, source SMALLINT DEFAULT NULL, identifier VARCHAR(255) DEFAULT NULL, decays VARCHAR(255) DEFAULT NULL, INDEX IDX_5286D72BF6B63604 (n_modification_id_id), INDEX IDX_5286D72B40E730A9 (c_modification_id_id), INDEX IDX_5286D72B25800BEF (b_modification_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nick VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, api_token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649290B2F37 (nick), UNIQUE INDEX UNIQ_8D93D6497BA2F5EB (api_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE b2s ADD CONSTRAINT FK_906EB2AB4C8374E FOREIGN KEY (sequence_id_id) REFERENCES sequence (id)');
        $this->addSql('ALTER TABLE b2s ADD CONSTRAINT FK_906EB2ABB85558B1 FOREIGN KEY (block_id_id) REFERENCES block (id)');
        $this->addSql('ALTER TABLE sequence ADD CONSTRAINT FK_5286D72BF6B63604 FOREIGN KEY (n_modification_id_id) REFERENCES modification (id)');
        $this->addSql('ALTER TABLE sequence ADD CONSTRAINT FK_5286D72B40E730A9 FOREIGN KEY (c_modification_id_id) REFERENCES modification (id)');
        $this->addSql('ALTER TABLE sequence ADD CONSTRAINT FK_5286D72B25800BEF FOREIGN KEY (b_modification_id_id) REFERENCES modification (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE b2s DROP FOREIGN KEY FK_906EB2ABB85558B1');
        $this->addSql('ALTER TABLE sequence DROP FOREIGN KEY FK_5286D72BF6B63604');
        $this->addSql('ALTER TABLE sequence DROP FOREIGN KEY FK_5286D72B40E730A9');
        $this->addSql('ALTER TABLE sequence DROP FOREIGN KEY FK_5286D72B25800BEF');
        $this->addSql('ALTER TABLE b2s DROP FOREIGN KEY FK_906EB2AB4C8374E');
        $this->addSql('DROP TABLE b2s');
        $this->addSql('DROP TABLE block');
        $this->addSql('DROP TABLE modification');
        $this->addSql('DROP TABLE sequence');
        $this->addSql('DROP TABLE user');
    }
}
