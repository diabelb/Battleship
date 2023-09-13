<?php

namespace Battleship\Component\Ship;

class Ship
{
    private string $name;
    private int $size;

    private int $remainingSize;

    public function __construct(string $name, int $size)
    {
        $this->name = $name;
        $this->size = $size;
        $this->remainingSize = $size;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @throws ShipAlreadySankException
     */
    public function hit(): void
    {
        if ($this->remainingSize > 0) {
            $this->remainingSize--;
        }
        else {
            throw new ShipAlreadySankException();
        }
    }

    public function getRemainingSize(): int
    {
        return $this->remainingSize;
    }

    public function getInitialSize(): int
    {
        return $this->size;
    }
}