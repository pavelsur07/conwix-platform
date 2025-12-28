<?php

declare(strict_types=1);

namespace App\Tests\Builders;

use App\Entity\Account;

final class AccountBuilder
{
    private string $id = '00000000-0000-0000-0000-000000000001';
    private string $email = 'test@example.com';
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('2025-01-01 00:00:00');
    }

    public function withId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function withEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function withCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function build(): Account
    {
        return new Account($this->id, $this->email, $this->createdAt);
    }
}
