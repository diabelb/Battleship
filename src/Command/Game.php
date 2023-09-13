<?php

declare(strict_types=1);

namespace Battleship\Command;

use Battleship\Component\Board\Board;
use Battleship\Component\ConsoleRenderer\ConsoleRenderer;
use Battleship\Component\LetterHelper\AlphabetHelper;
use Battleship\Component\Ship\Ship;
use Battleship\Component\Ship\ShipAlreadySankException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class Game extends Command
{
    /**
     * @param SymfonyStyle $io
     * @param Board $board
     * @return int
     */
    public function getColumnToHit(SymfonyStyle $io, Board $board): int
    {
        do {
            $column = AlphabetHelper::getLetterNumber(strtoupper($io->ask('What column do you want to hit?') ?? ''));

            if (!$board->isValidColumn($column)) {
                $io->error('Column is not correct. Please try again.');
            }
        } while (!$board->isValidColumn($column));
        return $column;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('The Battleship');

        $board = new Board(10);
        $board->placeRandomShip(new Ship('Battleship', 5));
        $board->placeRandomShip(new Ship('Destroyer1', 4));
        $board->placeRandomShip(new Ship('Destroyer2', 4));
        $consoleRenderer = new ConsoleRenderer($board, $io);

        while (! $board->isFinished()) {
            $consoleRenderer->render();

            $column = $this->getColumnToHit($io, $board);
            $row = $this->getRowToHit($io, $board);

            try {
                $board->getField(intval($row), intval($column))->hit();
            } catch (ShipAlreadySankException $e) {
                $io->error('Something went wrong. Ship already sank. Try again.');
            }
        }

        $io->note('Congratulations!. You won.');
        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->setName('game')
            ->setDescription('Start a new game.')
        ;
    }

    /**
     * @param SymfonyStyle $io
     * @param Board $board
     * @return mixed
     */
    public function getRowToHit(SymfonyStyle $io, Board $board): mixed
    {
        do {
            $row = $io->ask('What row do you want to hit?');

            if (!$board->isValidRow($row)) {
                $io->error('Row is not correct. Please try again.');
            }
        } while (!$board->isValidRow($row));
        return $row;
    }
}