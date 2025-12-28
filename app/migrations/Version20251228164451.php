<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251228164451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clients (id UUID NOT NULL, external_id VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, company_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_C82E74979B1AD6 ON clients (company_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_clients_company_external ON clients (company_id, external_id)');
        $this->addSql('CREATE TABLE conversations (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, company_id UUID NOT NULL, client_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_C2521BF1979B1AD6 ON conversations (company_id)');
        $this->addSql('CREATE INDEX IDX_C2521BF119EB6921 ON conversations (client_id)');
        $this->addSql('CREATE TABLE messages (id UUID NOT NULL, direction VARCHAR(3) NOT NULL, text TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, conversation_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_DB021E969AC0396 ON messages (conversation_id)');
        $this->addSql('ALTER TABLE clients ADD CONSTRAINT FK_C82E74979B1AD6 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF1979B1AD6 FOREIGN KEY (company_id) REFERENCES companies (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE conversations ADD CONSTRAINT FK_C2521BF119EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E969AC0396 FOREIGN KEY (conversation_id) REFERENCES conversations (id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clients DROP CONSTRAINT FK_C82E74979B1AD6');
        $this->addSql('ALTER TABLE conversations DROP CONSTRAINT FK_C2521BF1979B1AD6');
        $this->addSql('ALTER TABLE conversations DROP CONSTRAINT FK_C2521BF119EB6921');
        $this->addSql('ALTER TABLE messages DROP CONSTRAINT FK_DB021E969AC0396');
        $this->addSql('DROP TABLE clients');
        $this->addSql('DROP TABLE conversations');
        $this->addSql('DROP TABLE messages');
    }
}
