<?php

declare(strict_types=1);

namespace Battleship;

use Symfony\Component\Console\Application as BaseApplication;

final class Application extends BaseApplication
{
    public function __construct(iterable $commands = [])
    {
        parent::__construct();

        foreach ($commands as $command) {
            $this->add($command);
        }
    }
}