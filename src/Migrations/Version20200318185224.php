<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200318185224 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE timers ADD title VARCHAR(25) NOT NULL, ADD description VARCHAR(255) NOT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE timer_type timer_type INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E606E6AF2FBBD0A6 ON timers (timer_type)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_E606E6AF2FBBD0A6 ON timers');
        $this->addSql('ALTER TABLE timers DROP title, DROP description, CHANGE timer_type timer_type INT NOT NULL, CHANGE user_id user_id INT NOT NULL');
    }
}
