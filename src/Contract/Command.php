<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\Contract;

use Philiagus\Figment\Cli\Contract\Stream\StreamProvider;

interface Command {

    public function invoke(Invocation $invocation, StreamProvider $streams): int;

    public function populateConfiguration(Command\ConfigurationReceiver $configuration): void;

}
