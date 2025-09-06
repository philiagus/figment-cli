<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli;

use Philiagus\Figment\Cli\Command\Configuration;
use Philiagus\Figment\Cli\Contract\Command;
use Philiagus\Figment\Cli\Contract\Invocation;
use Philiagus\Figment\Cli\Stream\StreamProvider;
use Philiagus\Figment\Container\Attribute\Instance;
use Philiagus\Figment\Container\Contract\InstanceList;

class CommandRouter {

    public function __construct(
        #[Instance('cli.commands')] private InstanceList $commands,
        #[Instance('cli.stream-collection', StreamProvider::class)] private Contract\Stream\StreamProvider $streams,
    ) {}

    public function invoke(Invocation $invocation): int
    {
        $streams = new StreamProvider();
        try {
            foreach ($this->commands->traverseInstances(Command::class) as $command) {
                $result = new Configuration($command)->invoke($invocation, $this->streams);
                if ($result !== null) {
                    return $result;
                }
            }
        } catch (\Throwable $e) {
            $streams->stderr()->print((string)$e);
        }

        throw new \OutOfBoundsException("No command found");
    }

}
