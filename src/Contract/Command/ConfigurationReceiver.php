<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\Contract\Command;

use Philiagus\Parser\Contract\Parser;

interface ConfigurationReceiver
{
    public function setName(string $name): self;

    public function setDescription(string $description): self;

    public function addOption(OptionConfigurationProvider $configuration): self;

    public function takesArguments(Parser $parser): self;
}
