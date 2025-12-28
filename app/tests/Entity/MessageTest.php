<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Message;
use App\Tests\Builders\MessageBuilder;
use PHPUnit\Framework\TestCase;

final class MessageTest extends TestCase
{
    public function testMessageIsCreatedWithProvidedValues(): void
    {
        $createdAt = new \DateTimeImmutable('2025-02-01 10:00:00');

        $message = (new MessageBuilder())
            ->withId('cccccccc-cccc-cccc-cccc-cccccccccccc')
            ->withDirection(Message::DIRECTION_OUT)
            ->withText('Reply text')
            ->withCreatedAt($createdAt)
            ->build();

        self::assertSame('cccccccc-cccc-cccc-cccc-cccccccccccc', $message->getId());
        self::assertSame(Message::DIRECTION_OUT, $message->getDirection());
        self::assertSame('Reply text', $message->getText());
        self::assertEquals($createdAt, $message->getCreatedAt());
        self::assertNotEmpty($message->getConversation()->getId());
    }
}

