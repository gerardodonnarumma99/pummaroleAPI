<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200318191132 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE timers DROP INDEX UNIQ_E606E6AF2FBBD0A6, ADD INDEX timer_type (timer_type)');
        $this->addSql('ALTER TABLE timers ADD title VARCHAR(25) NOT NULL, ADD description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE timer_type CHANGE duration duration INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE timer_type CHANGE duration duration INT DEFAULT NULL');
        $this->addSql('ALTER TABLE timers DROP INDEX timer_type, ADD UNIQUE INDEX UNIQ_E606E6AF2FBBD0A6 (timer_type)');
        $this->addSql('ALTER TABLE timers DROP title, DROP description');
    }
}
