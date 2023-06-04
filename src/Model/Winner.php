<?php

declare(strict_types=1);

namespace App\Model;

use App\Contract\BuyerInterface;
use App\Contract\MoneyInterface;
use App\Contract\WinnerInterface;

class Winner implements WinnerInterface
{
    public function __construct(
        private readonly BuyerInterface $buyer,
        private readonly MoneyInterface $bid,
    ) {}

    public function getBuyer(): BuyerInterface
    {
        return $this->buyer;
    }

    public function getBid(): MoneyInterface
    {
        return $this->bid;
    }
}