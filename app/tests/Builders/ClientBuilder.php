<?php

declare(strict_types=1);

namespace App\Tests\Builders;

use App\Entity\Client;
use App\Entity\Company;

final class ClientBuilder
{
    private string $id = '00000000-0000-0000-0000-000000000201';
    private string $externalId = 'client-123';
    private \DateTimeImmutable $createdAt;
    private CompanyBuilder $companyBuilder;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('2025-01-01 00:00:00');
        $this->companyBuilder = new CompanyBuilder();
    }

    public function withId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function withExternalId(string $externalId): self
    {
        $this->externalId = $externalId;
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

    public function build(): Client
    {
        $company = $this->companyBuilder->build();
        return new Client($this->id, $company, $this->externalId, $this->createdAt);
    }
}

