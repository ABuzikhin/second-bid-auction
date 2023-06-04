<?php

declare(strict_types=1);

namespace App\Contract;

interface MoneyInterface
{
    public function getValue(): int;
}