<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\BuyerInterface;
use App\Contract\HasReservePriceInterface;
use App\Contract\MoneyInterface;
use App\Contract\WinnerInterface;
use App\Dto\BuyerMaxBidDto;
use App\Model\AuctionItem;
use App\Model\Winner;

class WinnerDetectorService implements WinnerDetectorInterface
{
    /**
     * @inheritDoc
     */
    public function detectWinner(HasReservePriceInterface $auctionItem, array $buyers): ?WinnerInterface
    {
        $reservedPrice = $auctionItem->getReservePrice();

        $maxBids = [];
        foreach ($buyers as $buyer) {
            $suitableBid = $this->getSuitableBid($buyer, $reservedPrice);

            if (!$suitableBid instanceof MoneyInterface) {
                continue;
            }

            $maxBids[] = new BuyerMaxBidDto($buyer, $suitableBid);
        }

        if ([] === $maxBids) {
            return null;
        }

        if(1 === \count($maxBids)) {
            return new Winner($maxBids[0]->buyer, $reservedPrice);
        }

        usort($maxBids, function (BuyerMaxBidDto $a, BuyerMaxBidDto $b) {
                $unifiedValueA = $a->maxBid->getValue();
                $unifiedValueB = $b->maxBid->getValue();

                if ($unifiedValueA === $unifiedValueB) {
                    return 0;
                }

                return $unifiedValueA > $unifiedValueB ? -1 : 1;
        });

        /** @var BuyerMaxBidDto $first */
        $first  = \array_shift($maxBids);
        /** @var BuyerMaxBidDto $second */
        $second = \array_shift($maxBids);

        if ($first->maxBid->getValue() === $second->maxBid->getValue()) {
            return null;
        }

        return new Winner($first->buyer, $second->maxBid);
    }

    private function getSuitableBid(BuyerInterface $buyer, MoneyInterface $reservedPrice): ?MoneyInterface
    {
        $bids = array_filter(
            $buyer->getBids(),
            fn (MoneyInterface $bid)=> $bid->getValue() >= $reservedPrice->getValue()
        );

        if ([] === $bids) {
            return null;
        }

        if (1 === \count($bids)) {
            return \array_shift($bids);
        }

        return \array_reduce(
            $bids,
            fn (?MoneyInterface $a, ?MoneyInterface $b)  => $a ? ($a->getValue() > $b->getValue() ? $a : $b) : $b
        );
    }
}