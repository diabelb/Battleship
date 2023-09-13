<?php

namespace Component\Board;

use Battleship\Component\Board\Field;
use Battleship\Component\Ship\Ship;
use Battleship\Component\Ship\ShipAlreadySankException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

final class FieldTest extends TestCase
{
    public function testICanCreateEmptyField() {
        // Given
        $field = new Field();

        // When

        // Then
        $this->assertFalse($field->isOccupied());
    }

    /**
     * @throws Exception
     */
    public function testICanAddShipToField() {
        // Given
        $ship = $this->createStub(Ship::class);
        $field = new Field();

        // When
        $field->addShip($ship);

        // Then
        $this->assertTrue($field->isOccupied());
    }

    /**
     * @throws ShipAlreadySankException
     */
    public function testICanHitEmptyField() {
        // Given
        $field = new Field();

        // When
        $field->hit();

        // Then
        $this->assertFalse($field->isHitShip());
        $this->assertTrue($field->isMissed());
    }


    /**
     * @throws ShipAlreadySankException
     * @throws Exception
     */
    public function testICanHitFieldWithShip() {
        // Given
        $ship = $this->createStub(Ship::class);
        $field = new Field();
        $field->addShip($ship);

        // When
        $field->hit();

        // Then
        $this->assertTrue($field->isHitShip());
        $this->assertFalse($field->isMissed());
    }

    /**
     * @throws Exception
     * @throws ShipAlreadySankException
     */
    public function testHittingFieldWithShipShouldHitShip() {
        // Given
        $ship = $this->createMock(Ship::class);
        $field = new Field();
        $field->addShip($ship);

        // Then
        $ship->expects($this->once())->method('hit');

        // When
        $field->hit();
    }
}
