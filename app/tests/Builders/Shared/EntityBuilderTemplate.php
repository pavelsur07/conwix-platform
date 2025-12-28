<?php
/**
 * Это шаблон, не реальный builder.
 * Копируешь файл → переименовываешь → заменяешь <Entity>.
 */

declare(strict_types=1);

namespace App\Tests\Builders\Shared;

use;

<Entity >; // заменить на реальную сущность

/**
 * <Entity>Builder
 *
 * Используется ТОЛЬКО в тестах.
 *
 * Правила:
 * - build() всегда возвращает валидную сущность
 * - значения по умолчанию должны быть реалистичными
 * - любые изменения — через withX() методы
 */
final class <Entity > Builder extends AbstractBuilder
{
    /**
     * Значения по умолчанию (валидные!)
     */
    private
    string $name = 'Default name';
    private bool $active = true;

    /**
     * Пример связанной сущности
     */
    private ?object $related = null;

    public static function create(): self
{
    return new self();
}

    /**
     * Fluent API — точечные изменения
     */
    public function withName(string $name): self
{
    $this->name = $name;

    return $this;
}

    public function inactive(): self
{
    $this->active = false;

    return $this;
}

    public function withRelated(object $related): self
{
    $this->related = $related;

    return $this;
}

    /**
     * Единственное место создания сущности
     */
    public function build(): <Entity >
    {
        $entity = new <Entity>(
    $this->name,
            $this->active
        );

        if ($this->related !== null) {
            $entity->setRelated($this->related);
        }

        return $entity;
    }
}
