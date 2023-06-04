<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\BuyerInterface;
use App\Contract\HasReservePriceInterface;
use App\Contract\WinnerInterface;
use App\Model\AuctionItem;

interface WinnerDetectorInterface
{
    /**
     * @param HasReservePriceInterface $auctionItem
     * @param array<BuyerInterface>    $buyers
     *
     * @return WinnerInterface|null
     */
    public function detectWinner(HasReservePriceInterface $auctionItem, array $buyers): ?WinnerInterface;
}