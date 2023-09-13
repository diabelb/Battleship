<?php

namespace Battleship\Component\LetterHelper;

final class AlphabetHelper
{
    const FIRST_LETTER_CODE = 65;
    static function getLetter(int $number): string
    {
        return chr(self::FIRST_LETTER_CODE + $number);
    }

    static function getLetterNumber(string $letter): int
    {
        return ord($letter) - self::FIRST_LETTER_CODE;
    }
}