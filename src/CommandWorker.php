<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli;

use Philiagus\Figment\Cli\Command\Configuration;
use Philiagus\Figment\Cli\Contract\Command;
use Philiagus\Figment\Container\Attribute\Inject;
use Philiagus\Figment\Container\Contract\InstanceList;
use Philiagus\Figment\Container\EmptyInstanceList;

readonly class CommandWorker
{

    /**
     * @param InstanceList $commands
     */
    public function __construct(
        #[Inject('figment.cli.commands')] private InstanceList $commands = new EmptyInstanceList()
    )
    {
    }

    public function work(Contract\Terminal $terminal): int
    {
        $commands = [];
        $commandConfigurations = [];
        try {
            foreach ($this->commands->traverseInstances(Command::class) as $command) {
                $commands[] = $command;
                $config = new Configuration($command);
                $commandConfigurations[] = $config;
                $result = $config->invoke($terminal);
                if ($result !== false) {
                    return $result;
                }
            }
        } catch (\Throwable $e) {
            $terminal->stderr()->print((string) $e);
        }

        foreach ($commandConfigurations as $configuration) {
            $terminal->stdout()->println($configuration->getName());

            return 255;
        }

        throw new \OutOfBoundsException("No command found");
    }

}
