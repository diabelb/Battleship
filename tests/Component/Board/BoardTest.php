<?php

namespace Component\Board;

use Battleship\Component\Board\Board;
use Battleship\Component\Board\CannotPlaceShipException;
use Battleship\Component\Board\Field;
use Battleship\Component\Ship\Ship;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class BoardTest extends TestCase
{
    public static function placeShipProvider(): array
    {
        return [
            [13, 5, 3, 15, Board::VERTICAL_ORIENTATION],
            [-1, 5, 3, 15, Board::VERTICAL_ORIENTATION],
            [4, 15, 3, 15, Board::VERTICAL_ORIENTATION],
            [4, -3, 3, 15, Board::VERTICAL_ORIENTATION],
            [-1, 3, 3, 15, Board::HORIZONTAL_ORIENTATION],
            [15, 3, 3, 15, Board::HORIZONTAL_ORIENTATION],
            [4, -1, 3, 15, Board::HORIZONTAL_ORIENTATION],
            [4, 13, 3, 15, Board::HORIZONTAL_ORIENTATION],
        ];
    }

    public function testCreatingEmptyBoardShouldInitAllFields() {
        // Given
        $size = 15;

        // When
        $board = new Board($size);

        // Then
        for ($i = 0; $i < $size; $i++) {
            for ($y = 0; $y < $size; $y++) {
                $this->assertInstanceOf(Field::class, $board->getField($i, $y));
            }
        }
    }

    public function testICenGerCorrectBoardSize() {
        // Given
        $size = 15;

        // When
        $board = new Board($size);

        // Then
        $this->assertEquals($size, $board->getSize());
    }

    /**
     * @throws Exception
     */
    public function testBoardIsNotFinishedByDefault() {
        // Given
        $size = 15;
        $ship1 = $this->createMock(Ship::class);
        $ship1->method('getRemainingSize')->willReturn(5);

        $ship2 = $this->createMock(Ship::class);
        $ship2->method('getRemainingSize')->willReturn(5);

        $ship3 = $this->createMock(Ship::class);
        $ship3->method('getRemainingSize')->willReturn(5);

        // When
        $board = new Board($size);
        $board->placeRandomShip($ship1);
        $board->placeRandomShip($ship2);
        $board->placeRandomShip($ship3);

        // Then
        $this->assertFalse($board->isFinished());
    }

    /**
     * @throws Exception
     */
    public function testHittingAllShipsShouldFinishBoard() {
        // Given
        $size = 15;
        $ship1 = $this->createMock(Ship::class);
        $ship1->method('getRemainingSize')->willReturn(0);

        $ship2 = $this->createMock(Ship::class);
        $ship2->method('getRemainingSize')->willReturn(0);

        $ship3 = $this->createMock(Ship::class);
        $ship3->method('getRemainingSize')->willReturn(0);

        // When
        $board = new Board($size);
        $board->placeRandomShip($ship1);
        $board->placeRandomShip($ship2);
        $board->placeRandomShip($ship3);

        // Then
        $this->assertTrue($board->isFinished());
    }

    public function testICanValidateBoardColumn() {
        // Given
        $size = 15;

        // When
        $board = new Board($size);

        // Then
        $this->assertTrue($board->isValidColumn(5));
        $this->assertTrue($board->isValidColumn(14));
        $this->assertFalse($board->isValidColumn(15));
        $this->assertFalse($board->isValidColumn(20));
        $this->assertFalse($board->isValidColumn(-1));
    }

    public function testICanValidateBoardRow() {
        // Given
        $size = 15;

        // When
        $board = new Board($size);

        // Then
        $this->assertTrue($board->isValidRow(5));
        $this->assertTrue($board->isValidRow(14));
        $this->assertFalse($board->isValidRow(15));
        $this->assertFalse($board->isValidRow(20));
        $this->assertFalse($board->isValidRow(-1));
    }

    public function testICanGetAllShips() {
        // Given
        $size = 15;
        $ship1 = $this->createMock(Ship::class);
        $ship2 = $this->createMock(Ship::class);


        // When
        $board = new Board($size);
        $board->placeRandomShip($ship1);
        $board->placeRandomShip($ship2);

        // Then
        $this->assertCount(2, $board->getShips());
    }

    /**
     * @throws CannotPlaceShipException
     * @throws Exception
     */
    public function testICanPlaceVerticalShipOnBoard() {
        // Given
        $size = 15;
        $ship1 = $this->createMock(Ship::class);
        $ship1->method('getInitialSize')->willReturn(3);

        // When
        $board = new Board($size);
        $board->placeShip(3,5, $ship1, Board::VERTICAL_ORIENTATION);

        // Then
        $this->assertFalse($board->getField(2, 5)->isOccupied());
        $this->assertTrue($board->getField(3, 5)->isOccupied());
        $this->assertTrue($board->getField(4, 5)->isOccupied());
        $this->assertTrue($board->getField(5, 5)->isOccupied());
        $this->assertFalse($board->getField(6, 5)->isOccupied());
    }

    /**
     * @throws CannotPlaceShipException
     * @throws Exception
     */
    public function testICanPlaceHorizontalShipOnBoard() {
        // Given
        $size = 15;
        $ship1 = $this->createMock(Ship::class);
        $ship1->method('getInitialSize')->willReturn(3);

        // When
        $board = new Board($size);
        $board->placeShip(3,5, $ship1, Board::HORIZONTAL_ORIENTATION);

        // Then
        $this->assertFalse($board->getField(3, 4)->isOccupied());
        $this->assertTrue($board->getField(3, 5)->isOccupied());
        $this->assertTrue($board->getField(3, 6)->isOccupied());
        $this->assertTrue($board->getField(3, 7)->isOccupied());
        $this->assertFalse($board->getField(3, 8)->isOccupied());
    }

    /**
     * @throws CannotPlaceShipException
     * @throws Exception
     */
    public function testICannotPlaceMultipleShips() {
        // Given
        $size = 15;
        $ship1 = $this->createMock(Ship::class);
        $ship1->method('getInitialSize')->willReturn(3);
        $ship2 = $this->createMock(Ship::class);
        $ship2->method('getInitialSize')->willReturn(3);

        // When
        $board = new Board($size);
        $board->placeShip(3,5, $ship1, Board::HORIZONTAL_ORIENTATION);
        $board->placeShip(6,5, $ship2, Board::HORIZONTAL_ORIENTATION);

        // Then
        $this->assertFalse($board->getField(3, 4)->isOccupied());
        $this->assertTrue($board->getField(3, 5)->isOccupied());
        $this->assertTrue($board->getField(3, 6)->isOccupied());
        $this->assertTrue($board->getField(3, 7)->isOccupied());
        $this->assertFalse($board->getField(3, 8)->isOccupied());
        $this->assertFalse($board->getField(6, 4)->isOccupied());
        $this->assertTrue($board->getField(6, 5)->isOccupied());
        $this->assertTrue($board->getField(6, 6)->isOccupied());
        $this->assertTrue($board->getField(6, 7)->isOccupied());
        $this->assertFalse($board->getField(6, 8)->isOccupied());
    }

    /**
     * @throws CannotPlaceShipException
     * @throws Exception
     */
    public function testICannotPlaceTwoShipsOnTheSamePlace() {
        // Given
        $size = 15;
        $ship1 = $this->createMock(Ship::class);
        $ship1->method('getInitialSize')->willReturn(3);
        $ship2 = $this->createMock(Ship::class);
        $ship2->method('getInitialSize')->willReturn(3);

        // Then
        $this->expectException(CannotPlaceShipException::class);

        // When
        $board = new Board($size);
        $board->placeShip(3,5, $ship1, Board::HORIZONTAL_ORIENTATION);
        $board->placeShip(3,5, $ship2, Board::HORIZONTAL_ORIENTATION);
    }

    /**
     * @throws Exception
     */
    #[DataProvider('placeShipProvider')]
    public function testICannotPlaceShipOutOfBoard($row, $column, $shipSize, $size, $orientation) {
        // Given
        $ship1 = $this->createMock(Ship::class);
        $ship1->method('getInitialSize')->willReturn($shipSize);

        // Then
        $this->expectException(CannotPlaceShipException::class);

        //When
        $board = new Board($size);
        $board->placeShip($row,$column, $ship1, $orientation);
    }
}
