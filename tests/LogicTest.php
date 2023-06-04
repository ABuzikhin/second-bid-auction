<?php

declare(strict_types=1);

namespace Tests;

use App\Model\AuctionItem;
use App\Model\Bid;
use App\Model\Buyer;
use App\Model\ItemPrice;
use App\Service\WinnerDetectorService;

class LogicTest
{
    public function testMainLogic(): void
    {
        $this->describe('Test the main logic of the algorithm.');

        $auctionItem = new AuctionItem(new ItemPrice(100));

        $buyerA = $this->makeBuyer('A', [110, 130]);
        $buyerB = $this->makeBuyer('B');
        $buyerC = $this->makeBuyer('C', [125]);
        $buyerD = $this->makeBuyer('D', [105, 115, 90]);
        $buyerE = $this->makeBuyer('E', [132, 135, 140]);

        $service = new WinnerDetectorService();

        $result = $service->detectWinner($auctionItem, [$buyerE, $buyerD, $buyerC, $buyerB, $buyerA]);

        $this->assertNotNull($result);
        $this->assertEquals('E', $result->getBuyer()->getName(), 'Buyer name does not match.');
        $this->assertEquals(130, $result->getBid()->getValue(), 'Bid value does not match.');
    }

    public function testNoBidsCase(): void
    {
        $this->describe('Test extra case - No Bids.');

        $auctionItem = new AuctionItem(new ItemPrice(100));

        $buyerA = $this->makeBuyer('A');
        $buyerB = $this->makeBuyer('B');
        $buyerC = $this->makeBuyer('C');

        $service = new WinnerDetectorService();

        $result = $service->detectWinner($auctionItem, [$buyerC, $buyerB, $buyerA]);

        $this->assertNull($result, 'WinnerDetector should not detect the winner.');
    }

    public function testNoBuyers(): void
    {
        $this->describe('Test extra case - No Buyers');

        $auctionItem = new AuctionItem(new ItemPrice(100));

        $service = new WinnerDetectorService();
        $result  = $service->detectWinner($auctionItem, []);

        $this->assertNull($result, 'WinnerDetector should not detect the winner.');
    }

    public function testMoreThenOneSecondaryPrice(): void
    {
        $this->describe('Test extra case - two buyers suggest equal secondary bid');

        $auctionItem = new AuctionItem(new ItemPrice(100));

        $buyerA = $this->makeBuyer('A', [135, 130]);
        $buyerB = $this->makeBuyer('B');
        $buyerC = $this->makeBuyer('C', [135]);
        $buyerE = $this->makeBuyer('E', [140]);

        $service = new WinnerDetectorService();

        $result = $service->detectWinner($auctionItem, [$buyerE, $buyerC, $buyerB, $buyerA]);

        $this->assertNotNull($result);
        $this->assertEquals('E', $result->getBuyer()->getName(), 'Buyer name does not match.');
        $this->assertEquals(135, $result->getBid()->getValue(), 'Bid value does not match.');
    }

    public function testSingleBuyerCase(): void
    {
        $this->describe('Test extra case - single buyer case.');

        $auctionItem = new AuctionItem(new ItemPrice(100));

        $buyerA  = $this->makeBuyer('A', [135, 130]);
        $service = new WinnerDetectorService();

        $result = $service->detectWinner($auctionItem, [$buyerA]);

        $this->assertNotNull($result);
        $this->assertEquals('A', $result->getBuyer()->getName(), 'Buyer name does not match.');
        $this->assertEquals(100, $result->getBid()->getValue(), 'Bid value does not match.');
    }

    public function testAllOtherBidsBelowReservedPrice(): void
    {
        $this->describe('Test extra case - all other buyers provide bid lower then reserved price.');

        $auctionItem = new AuctionItem(new ItemPrice(100));

        $buyerA = $this->makeBuyer('A', [90, 89]);
        $buyerB = $this->makeBuyer('B', [101, 50]);
        $buyerC = $this->makeBuyer('C', [70, 60]);

        $service = new WinnerDetectorService();

        $result = $service->detectWinner($auctionItem, [$buyerA, $buyerB, $buyerC]);

        $this->assertEquals('B', $result->getBuyer()->getName(), 'Buyer name does not match.');
        $this->assertEquals(100, $result->getBid()->getValue(), 'Bid value does not match.');
    }

    public function testEqualsWinningBids(): void
    {
        $this->describe('Test extra case - two or more buyers suggest equals highest bid.');

        $auctionItem = new AuctionItem(new ItemPrice(100));

        $buyerA = $this->makeBuyer('A', [90, 101]);
        $buyerB = $this->makeBuyer('B', [101, 50]);
        $buyerC = $this->makeBuyer('C', [70, 60]);
        $buyerD = $this->makeBuyer('B', [98, 99, 101]);

        $service = new WinnerDetectorService();

        $result = $service->detectWinner($auctionItem, [$buyerD, $buyerA, $buyerB, $buyerC]);

        $this->assertNull($result);
    }


    protected function makeBuyer(string $name, array $bids = []): Buyer
    {
        if ([] === $bids) {
            return new Buyer($name);
        }

        $array = [];
        foreach ($bids as $bid) {
            $array[] = new Bid($bid);
        }

        return new Buyer($name, $array);
    }

    private function assertEquals(
        string|int $actual,
        string|int $expected,
        string $error = 'This values are not equals.'
    ): void {
        if ($actual === $expected) {
            return;
        }

        echo 'ERROR: '.$error.PHP_EOL;
    }

    private function assertNull(mixed $actual, string $error = 'This value should be null'): void
    {
        if (null === $actual) {
            return;
        }

        echo 'ERROR: '.$error.PHP_EOL;
    }

    private function assertNotNull(mixed $actual, string $error = 'This value should not be null'): void
    {
        if (null !== $actual) {
            return;
        }

        echo 'ERROR: '.$error.PHP_EOL;
    }

    private function describe(string $message): void
    {
        echo $message . PHP_EOL;
    }
}