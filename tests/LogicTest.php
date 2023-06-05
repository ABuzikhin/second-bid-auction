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

        $auctionItem = $this->makeAuctionItem();

        $buyerA = $this->makeBuyer(1, 'A', [110, 130]);
        $buyerB = $this->makeBuyer(2, 'B');
        $buyerC = $this->makeBuyer(3, 'C', [125]);
        $buyerD = $this->makeBuyer(4, 'D', [105, 115, 90]);
        $buyerE = $this->makeBuyer(5, 'E', [132, 135, 140]);

        $service = new WinnerDetectorService();

        $result = $service->detectWinner($auctionItem, [$buyerE, $buyerD, $buyerC, $buyerB, $buyerA]);

        $this->assertNotNull($result);
        $this->assertEquals(5, $result->getBuyer()->getId(), 'Buyer ID does not match.');
        $this->assertEquals('E', $result->getBuyer()->getName(), 'Buyer name does not match.');
        $this->assertEquals(130, $result->getBid()->getValue(), 'Bid value does not match.');
    }

    public function testMainLogicWithSimilarBuyers(): void
    {
        $this->describe('Test extra case - Some buyers have similar names.');

        $auctionItem = $this->makeAuctionItem();

        $buyerA = $this->makeBuyer(1, 'E', [110, 130]);
        $buyerB = $this->makeBuyer(2, 'E');
        $buyerC = $this->makeBuyer(3, 'C', [125]);
        $buyerD = $this->makeBuyer(4, 'D', [105, 115, 90]);
        $buyerE = $this->makeBuyer(5, 'E', [132, 135, 140]);

        $service = new WinnerDetectorService();

        $result = $service->detectWinner($auctionItem, [$buyerE, $buyerD, $buyerC, $buyerB, $buyerA]);

        $this->assertNotNull($result);
        $this->assertEquals(5, $result->getBuyer()->getId(), 'Buyer ID does not match.');
        $this->assertEquals('E', $result->getBuyer()->getName(), 'Buyer name does not match.');
        $this->assertEquals(130, $result->getBid()->getValue(), 'Bid value does not match.');
    }

    public function testNoBidsCase(): void
    {
        $this->describe('Test extra case - No Bids.');

        $auctionItem = $this->makeAuctionItem();

        $buyerA = $this->makeBuyer(1, 'A');
        $buyerB = $this->makeBuyer(2, 'B');
        $buyerC = $this->makeBuyer(3, 'C');

        $service = new WinnerDetectorService();

        $result = $service->detectWinner($auctionItem, [$buyerC, $buyerB, $buyerA]);

        $this->assertNull($result, 'WinnerDetector should not detect the winner.');
    }

    public function testNoBuyers(): void
    {
        $this->describe('Test extra case - No Buyers');

        $auctionItem = $this->makeAuctionItem();

        $service = new WinnerDetectorService();
        $result  = $service->detectWinner($auctionItem, []);

        $this->assertNull($result, 'WinnerDetector should not detect the winner.');
    }

    public function testMoreThenOneSecondaryPrice(): void
    {
        $this->describe('Test extra case - two buyers suggest equal secondary bid');

        $auctionItem = $this->makeAuctionItem();

        $buyerA = $this->makeBuyer(1, 'A', [135, 130]);
        $buyerB = $this->makeBuyer(2, 'B');
        $buyerC = $this->makeBuyer(3, 'C', [135]);
        $buyerE = $this->makeBuyer(4, 'E', [140]);

        $service = new WinnerDetectorService();

        $result = $service->detectWinner($auctionItem, [$buyerE, $buyerC, $buyerB, $buyerA]);

        $this->assertNotNull($result);
        $this->assertEquals(4, $result->getBuyer()->getId(), 'Buyer ID does not match.');
        $this->assertEquals('E', $result->getBuyer()->getName(), 'Buyer name does not match.');
        $this->assertEquals(135, $result->getBid()->getValue(), 'Bid value does not match.');
    }

    public function testSingleBuyerCase(): void
    {
        $this->describe('Test extra case - single buyer case.');

        $auctionItem = $this->makeAuctionItem();

        $buyerA  = $this->makeBuyer(8, 'A', [135, 130]);
        $service = new WinnerDetectorService();

        $result = $service->detectWinner($auctionItem, [$buyerA]);

        $this->assertNotNull($result);
        $this->assertEquals(8, $result->getBuyer()->getId(), 'Buyer ID does not match.');
        $this->assertEquals('A', $result->getBuyer()->getName(), 'Buyer name does not match.');
        $this->assertEquals(100, $result->getBid()->getValue(), 'Bid value does not match.');
    }

    public function testAllOtherBidsBelowReservedPrice(): void
    {
        $this->describe('Test extra case - all other buyers provide bid lower then reserved price.');

        $auctionItem = $this->makeAuctionItem();

        $buyerA = $this->makeBuyer(1, 'A', [90, 89]);
        $buyerB = $this->makeBuyer(2, 'B', [101, 50]);
        $buyerC = $this->makeBuyer(3, 'C', [70, 60]);

        $service = new WinnerDetectorService();

        $result = $service->detectWinner($auctionItem, [$buyerA, $buyerB, $buyerC]);

        $this->assertEquals(2, $result->getBuyer()->getId(), 'Buyer ID does not match.');
        $this->assertEquals('B', $result->getBuyer()->getName(), 'Buyer name does not match.');
        $this->assertEquals(100, $result->getBid()->getValue(), 'Bid value does not match.');
    }

    public function testEqualsWinningBids(): void
    {
        $this->describe('Test extra case - two or more buyers suggest equals highest bid.');

        $auctionItem = $this->makeAuctionItem();

        $buyerA = $this->makeBuyer(1, 'A', [90, 101]);
        $buyerB = $this->makeBuyer(2, 'B', [101, 50]);
        $buyerC = $this->makeBuyer(3, 'C', [70, 60]);
        $buyerD = $this->makeBuyer(4, 'B', [98, 99, 101]);

        $service = new WinnerDetectorService();

        $result = $service->detectWinner($auctionItem, [$buyerD, $buyerA, $buyerB, $buyerC]);

        $this->assertNull($result);
    }


    protected function makeBuyer(int $id, string $name, array $bids = []): Buyer
    {
        $buyer = new Buyer($name);

        if ([] === $bids) {
            $this->updateId($id, Buyer::class, $buyer);

            return $buyer;
        }

        $array = [];
        foreach ($bids as $bid) {
            $array[] = new Bid($bid);
        }

        $buyer = new Buyer($name, $array);
        $this->updateId($id, Buyer::class, $buyer);

        return $buyer;
    }

    protected function makeAuctionItem(int $id = 1, int $itemPrice = 100): AuctionItem
    {
        $item =  new AuctionItem(new ItemPrice($itemPrice));
        $this->updateId($id, AuctionItem::class, $item);

        return $item;
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

    private function updateId(int $id, string $className, mixed $buyer): void
    {
        $reflectionProperty = new \ReflectionProperty($className, 'id');

        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($buyer, $id);
    }
}