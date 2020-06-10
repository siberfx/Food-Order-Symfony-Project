<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200110154228 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE setting ADD company VARCHAR(100) DEFAULT NULL, ADD address VARCHAR(255) DEFAULT NULL, ADD phone VARCHAR(20) DEFAULT NULL, ADD fax VARCHAR(20) DEFAULT NULL, ADD email VARCHAR(255) DEFAULT NULL, ADD smtpserver VARCHAR(20) DEFAULT NULL, ADD smtpemail VARCHAR(50) DEFAULT NULL, ADD smtppassword VARCHAR(20) DEFAULT NULL, ADD smtpport VARCHAR(5) DEFAULT NULL, ADD facebook VARCHAR(20) DEFAULT NULL, ADD instagram VARCHAR(20) DEFAULT NULL, ADD aboutus LONGTEXT DEFAULT NULL, ADD contact LONGTEXT DEFAULT NULL, ADD reference LONGTEXT DEFAULT NULL, ADD status VARCHAR(6) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE setting DROP company, DROP address, DROP phone, DROP fax, DROP email, DROP smtpserver, DROP smtpemail, DROP smtppassword, DROP smtpport, DROP facebook, DROP instagram, DROP aboutus, DROP contact, DROP reference, DROP status');
    }
}
