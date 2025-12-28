<?php

declare(strict_types=1);

namespace App\Tests\Builders;

use App\Entity\Company;

final class CompanyBuilder
{
    private string $id = '00000000-0000-0000-0000-000000000101';
    private string $name = 'Test Company';
    private \DateTimeImmutable $createdAt;
    private AccountBuilder $ownerBuilder;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('2025-01-01 00:00:00');
        $this->ownerBuilder = new AccountBuilder();
    }

    public function withId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function withName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function withCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function withOwnerBuilder(AccountBuilder $ownerBuilder): self
    {
        $this->ownerBuilder = $ownerBuilder;
        return $this;
    }

    public function build(): Company
    {
        $owner = $this->ownerBuilder->build();

        return new Company(
            $this->id,
            $owner,
            $this->name,
            $this->createdAt
        );
    }
}
