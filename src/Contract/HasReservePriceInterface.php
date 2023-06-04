<?php

declare(strict_types=1);

namespace App\Contract;

interface HasReservePriceInterface
{
    public function getReservePrice(): MoneyInterface;
}