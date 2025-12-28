<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'messages')]
class Message
{
    public const DIRECTION_IN = 'in';
    public const DIRECTION_OUT = 'out';

    #[ORM\Id]
    #[ORM\Column(type: 'guid', unique: true)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Conversation::class)]
    #[ORM\JoinColumn(name: 'conversation_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Conversation $conversation;

    #[ORM\Column(type: 'string', length: 3)]
    private string $direction;

    #[ORM\Column(type: 'text')]
    private string $text;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        Conversation $conversation,
        string $direction,
        string $text,
        \DateTimeImmutable $createdAt
    ) {
        $this->id = $id;
        $this->conversation = $conversation;
        $this->direction = $direction;
        $this->text = $text;
        $this->createdAt = $createdAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getConversation(): Conversation
    {
        return $this->conversation;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}

