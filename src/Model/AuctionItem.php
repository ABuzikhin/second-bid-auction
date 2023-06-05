<?php

declare(strict_types=1);

namespace App\Model;

use App\Contract\HasReservePriceInterface;
use App\Contract\MoneyInterface;

class AuctionItem implements HasReservePriceInterface
{
    private int $id;

    public function __construct(
        private readonly ItemPrice $reservePrice
    ) {}

    public function getReservePrice(): MoneyInterface
    {
        return $this->reservePrice;
    }

    public function getId(): int
    {
        return $this->id;
    }
}