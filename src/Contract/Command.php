<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\Contract;

use Philiagus\Figment\Cli\Contract\Stream\StreamProvider;

interface Command {

    public function invoke(Terminal $terminal): int;

    public function populateConfiguration(Command\ConfigurationReceiver $configuration): void;

}
