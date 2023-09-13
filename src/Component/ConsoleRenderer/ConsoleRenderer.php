<?php

namespace Battleship\Component\ConsoleRenderer;

use Battleship\Component\Board\Board;
use Battleship\Component\Board\Field;
use Battleship\Component\LetterHelper\AlphabetHelper;
use Battleship\Component\Ship\Ship;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Style\OutputStyle;

final class ConsoleRenderer
{
    private Board $board;
    private OutputStyle $io;

    private Table $table;

    public function __construct(Board $board, OutputStyle $io)
    {
        $this->board = $board;
        $this->io = $io;
        $this->table = $this->io->createTable();
    }

    public function render(): void
    {
        $this->createShipsList();
        $this->createTableHeader();
        $this->createRows();

        // Change row fist element style
        $this->table->setColumnStyle(0, (new TableStyle())->setCellRowContentFormat('<info> %s </info>'));

        $this->table->render();
    }

    /**
     * @return void
     */
    private function createTableHeader(): void
    {
        $this->table->setHeaders(['  ', ...range(AlphabetHelper::getLetter(0), AlphabetHelper::getLetter($this->board->getSize()-1))]);
    }

    private function createRowHeader(int $number): string {
        if ($number < 10) {
            return $number.' ';
        }
        return $number;
    }

    /**
     * @return void
     */
    private function createRows(): void
    {
        foreach ($this->board as $key => $row) {
            $this->table->setRow($key, [$this->createRowHeader($key), ...array_map([$this, 'createRowItem'], $row)]);
        }
    }

    private function createShipsList(): void
    {
        $this->io->listing(array_map([$this, 'createShipInfo'], $this->board->getShips()));
    }

    private function createRowItem(Field $item): string
    {
        if ($item->isHitShip()) {
            return '#';
        }
        else if ($item->isMissed()) {
            return 'X';
        }
//        else if ($item->isOccupied) {
//            return '@';
//        }
        return '.';
    }

    private function createShipInfo(Ship $ship): string
    {
        if ($ship->getRemainingSize() > 0) {
            return $ship->getName() . ': ' . $ship->getRemainingSize() . ' hits left';
        }
        else {
            return $ship->getName() . ': sank';
        }
    }
}