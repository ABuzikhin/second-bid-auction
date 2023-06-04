<?php

declare(strict_types=1);

namespace App\Contract;

use App\Model\Bid;

interface BuyerInterface
{
    public function getName(): string;

    /**
     * @return array<Bid>
     */
    public function getBids(): array;
}