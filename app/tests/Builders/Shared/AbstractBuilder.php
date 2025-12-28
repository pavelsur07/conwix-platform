<?php

declare(strict_types=1);

namespace App\Tests\Builders\Shared;

/**
 * Базовый класс для всех test-builders.
 *
 * Принципы:
 * - Builder создаёт ВАЛИДНЫЙ объект по умолчанию
 * - Builder НЕ знает про Doctrine / Container
 * - build() — единственная точка создания объекта
 */
abstract class AbstractBuilder
{
    /**
     * Создаёт и возвращает валидный объект.
     * Никакой работы с БД здесь быть не должно.
     */
    abstract public function build(): object;
}
