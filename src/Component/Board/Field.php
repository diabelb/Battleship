<?php

namespace Battleship\Component\Board;

use Battleship\Component\Ship\Ship;
use Battleship\Component\Ship\ShipAlreadySankException;

final class Field
{
    private bool $isOccupied;
    private bool $isHitShip;

    private bool $isMissed;

    private ?Ship $ship = null;

    public function __construct()
    {
        $this->isOccupied = false;
        $this->isHitShip = false;
        $this->isMissed = false;
    }

    public function addShip(Ship $ship): void
    {
        $this->isOccupied = true;
        $this->ship = $ship;
    }

    /**
     * @throws ShipAlreadySankException
     */
    public function hit(): void
    {
        if ($this->isOccupied) {
            if (!$this->isHitShip) {
                $this->isHitShip = true;
                $this->isMissed = false;
                $this->ship->hit();
            }
        }
        else {
            $this->isMissed = true;
        }
    }

    public function isOccupied(): bool
    {
        return $this->isOccupied;
    }

    public function isHitShip(): bool
    {
        return $this->isHitShip;
    }

    public function isMissed(): bool
    {
        return $this->isMissed;
    }
}