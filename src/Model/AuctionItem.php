<?php

declare(strict_types=1);

namespace App\Model;

use App\Contract\HasReservePriceInterface;
use App\Contract\MoneyInterface;

class AuctionItem implements HasReservePriceInterface
{
    public function __construct(
        private readonly ItemPrice $reservePrice
    ) {}

    public function getReservePrice(): MoneyInterface
    {
        return $this->reservePrice;
    }
}