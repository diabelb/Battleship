<?php

namespace Component\Ship;

use Battleship\Component\Ship\Ship;
use Battleship\Component\Ship\ShipAlreadySankException;
use PHPUnit\Framework\TestCase;

final class ShipTest extends TestCase
{
    const SHIP_NAME = 'Battleship';
    const SHIP_SIZE = 5;

    public function testICanCreateNeShipWithGivenNameAndSize(): void {
        // Given
        $ship = new Ship(self::SHIP_NAME, self::SHIP_SIZE);

        // When

        // Then
        $this->assertEquals(self::SHIP_NAME, $ship->getName());
        $this->assertEquals(self::SHIP_SIZE, $ship->getInitialSize());
    }

    public function testHittingShipWillReduceRemainingSize(): void {
        // Given
        $ship = new Ship(self::SHIP_NAME, self::SHIP_SIZE);

        // When
        $ship->hit();

        // then
        $this->assertEquals(self::SHIP_SIZE - 1, $ship->getRemainingSize());
    }

    public function testRemainingSizeCannotBeLoverThan0(): void {
        $ship = new Ship(self::SHIP_NAME, self::SHIP_SIZE);

        $this->expectException(ShipAlreadySankException::class);

        $ship->hit();
        $ship->hit();
        $ship->hit();
        $ship->hit();
        $ship->hit();
        $ship->hit();


    }
}
