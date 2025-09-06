<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli;

use Philiagus\Figment\Cli\Command\Configuration;
use Philiagus\Figment\Cli\Contract\Command;
use Philiagus\Figment\Container\Attribute\Instance;
use Philiagus\Figment\Container\Contract\InstanceList;
use Philiagus\Figment\Container\EmptyInstanceList;

readonly class CommandWorker {

    public function __construct(
        #[Instance('figment.cli.commands', new EmptyInstanceList())] private InstanceList $commands
    ) {}

    public function work(Contract\Terminal $terminal): int
    {
        try {
            foreach ($this->commands->traverseInstances(Command::class) as $command) {
                $result = new Configuration($command)->invoke($terminal);
                if ($result !== null) {
                    return $result;
                }
            }
        } catch (\Throwable $e) {
            $terminal->stderr()->print((string)$e);
        }

        throw new \OutOfBoundsException("No command found");
    }

}
