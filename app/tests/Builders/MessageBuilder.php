<?php

declare(strict_types=1);

namespace App\Tests\Builders;

use App\Entity\Message;

final class MessageBuilder
{
    private string $id = '00000000-0000-0000-0000-000000000401';
    private string $direction = Message::DIRECTION_IN;
    private string $text = 'Hello';
    private \DateTimeImmutable $createdAt;
    private ConversationBuilder $conversationBuilder;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('2025-01-01 00:00:00');
        $this->conversationBuilder = new ConversationBuilder();
    }

    public function withId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function withDirection(string $direction): self
    {
        $this->direction = $direction;
        return $this;
    }

    public function withText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function withCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function withConversationBuilder(ConversationBuilder $conversationBuilder): self
    {
        $this->conversationBuilder = $conversationBuilder;
        return $this;
    }

    public function build(): Message
    {
        $conversation = $this->conversationBuilder->build();
        return new Message($this->id, $conversation, $this->direction, $this->text, $this->createdAt);
    }
}

