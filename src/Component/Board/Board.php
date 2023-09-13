<?php

namespace Battleship\Component\Board;

use Battleship\Component\Ship\Ship;
use Iterator;

final class Board implements Iterator
{
    /** @var Field[][] $fields  */
    private array $fields;

    private int $position = 0;

    /** @var Ship[] */
    private array $ships;
    private int $size;

    const HORIZONTAL_ORIENTATION = 0;
    const VERTICAL_ORIENTATION = 1;

    public function __construct(int $size)
    {
        $this->position = 0;
        $this->size = $size;
        $this->initEmptyBoard($size);
    }

    public function getField(int $row, int $column): Field
    {
        return $this->fields[$row][$column];
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function isValidColumn(mixed $column): bool
    {
        return ($column >= 0) && $column < $this->size;
    }

    public function isValidRow(mixed $row): bool
    {
        return ($row >= 0) && $row < $this->size;

    }

    public function placeRandomShip(Ship $ship): void
    {
        $row = rand(0, $this->getSize() - 1);
        $column = rand(0, $this->getSize() - 1);
        $orientation = rand(Board::HORIZONTAL_ORIENTATION, Board::VERTICAL_ORIENTATION);

        try {
            $this->placeShip($row, $column, $ship, $orientation);
        }
        catch (CannotPlaceShipException $e) {
            // It should be fixed, but I don't want to overcomplicate the solution - for 3 ships it's always fine.
            $this->placeRandomShip($ship);
        }
    }

    public function getShips(): array
    {
        return $this->ships;
    }

    public function isFinished(): bool
    {
        $remainingShipsSize = 0;
        foreach ($this->getShips() as $ship) {
            $remainingShipsSize += $ship->getRemainingSize();
        }

        return $remainingShipsSize <= 0;
    }

    public function current(): array
    {
        return $this->fields[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->fields[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * @throws CannotPlaceShipException
     */
    public function placeShip(int $row, int $column, Ship $ship, int $orientation): void
    {
        if (! $this->canPlaceShip($row, $column, $ship->getInitialSize(), $orientation)) {
            throw new CannotPlaceShipException();
        }

        if ($orientation === Board::HORIZONTAL_ORIENTATION) {
            for ($i = 0; $i < $ship->getInitialSize(); $i++) {
                $this->getField($row, $column + $i)->addShip($ship);
            }
        }
        else if ($orientation === Board::VERTICAL_ORIENTATION) {
            for ($i = 0; $i < $ship->getInitialSize(); $i++) {
                $this->getField($row + $i, $column)->addShip($ship);
            }
        }
        $this->ships[] = $ship;
    }

    public function canPlaceShip(int $row, int $column, int $size, int $orientation): bool {
        if ($orientation === Board::HORIZONTAL_ORIENTATION) {
            return $this->canPlaceShipHorizontally($row, $column, $size);
        }
        else if ($orientation === Board::VERTICAL_ORIENTATION) {
            return $this->canPlaceShipVertically($row, $column, $size);
        }
        return false;
    }

    public function canPlaceShipHorizontally(int $row, int $column, int $size): bool
    {
        for ($i = 0; $i < $size; $i++) {
            if (! $this->isValidColumn($column + $i) || ! $this->isValidRow($row)  || $this->getField($row, $column + $i)->isOccupied()) {
                return false;
            }
        }
        return true;
    }

    public function canPlaceShipVertically(int $row, int $column, int $size): bool
    {
        for ($i = 0; $i < $size; $i++) {
            if (! $this->isValidRow($row + $i) || ! $this->isValidColumn($column) || $this->getField($row + $i, $column)->isOccupied()) {
                return false;
            }
        }
        return true;
    }

    private function initEmptyBoard(int $size): void
    {
        for ($i = 0; $i < $size; $i++) {
            for ($y = 0; $y < $size; $y++) {
                $this->fields[$i][$y] = new Field();
            }
        }
    }
}