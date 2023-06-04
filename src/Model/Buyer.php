<?php

declare(strict_types=1);

namespace App\Model;

use App\Contract\BuyerInterface;

class Buyer implements BuyerInterface
{
    /** @var array<Bid> */
    private array $bids;

    public function __construct(
        private readonly string $name,
        array $bids = [],
    )
    {
        $this->bids = $bids;
    }

    public function getBids(): array
    {
        return $this->bids;
    }

    public function getName(): string
    {
        return $this->name;
    }
}