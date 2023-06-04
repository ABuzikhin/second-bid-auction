<?php

declare(strict_types=1);

namespace App\Dto;

use App\Contract\BuyerInterface;
use App\Contract\MoneyInterface;

class BuyerMaxBidDto
{
    public function __construct(
        public readonly BuyerInterface $buyer,
        public readonly MoneyInterface $maxBid,
    ) {}
}