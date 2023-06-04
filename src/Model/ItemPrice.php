<?php

declare(strict_types=1);

namespace App\Model;

use App\Contract\MoneyInterface;

class ItemPrice implements MoneyInterface
{
    public function __construct(
        private readonly int $value
    ) {}

    public function getValue(): int
    {
        return $this->value;
    }
}