<?php

declare(strict_types=1);

namespace App\Contract;

use App\Model\Buyer;

interface WinnerInterface
{
    public function getBuyer(): BuyerInterface;

    public function getBid(): MoneyInterface;
}