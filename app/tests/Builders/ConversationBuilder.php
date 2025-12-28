<?php

declare(strict_types=1);

namespace App\Tests\Builders;

use App\Entity\Conversation;

final class ConversationBuilder
{
    private string $id = '00000000-0000-0000-0000-000000000301';
    private \DateTimeImmutable $createdAt;
    private CompanyBuilder $companyBuilder;
    private ClientBuilder $clientBuilder;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('2025-01-01 00:00:00');
        $this->companyBuilder = new CompanyBuilder();
        $this->clientBuilder = new ClientBuilder();
    }

    public function withId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function withCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function withCompanyBuilder(CompanyBuilder $companyBuilder): self
    {
        $this->companyBuilder = $companyBuilder;
        return $this;
    }

    public function withClientBuilder(ClientBuilder $clientBuilder): self
    {
        $this->clientBuilder = $clientBuilder;
        return $this;
    }

    public function build(): \App\Entity\Conversation
    {
        $company = $this->companyBuilder->build();

        // Client должен быть из той же компании — строим через companyBuilder
        $client = (new ClientBuilder())
            ->withCompanyBuilder($this->companyBuilder)
            ->withExternalId('client-123')
            ->build();

        return new Conversation($this->id, $company, $client, $this->createdAt);
    }
}

