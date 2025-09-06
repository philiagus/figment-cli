<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\Command;

use Philiagus\Figment\Cli\Contract;

class Invocation implements Contract\Invocation {

    private array $arguments;
    private string $scriptFile;

    public function __construct(array $argv) {
        $this->scriptFile = $_SERVER['SCRIPT_FILENAME'];
        $this->arguments = array_slice($argv, 1);
    }

    public function phpBinary(): \SplFileInfo
    {
        return new \SplFileInfo(PHP_BINARY);
    }

    public function scriptFile(): \SplFileInfo
    {
        return new \SplFileInfo($this->scriptFile);
    }

    public function argumentCount(): int
    {
        return count($this->arguments);
    }

    public function allArguments(): array{
        return $this->arguments;
    }

    public function getArgument(int $number): string
    {
        return $this->arguments[$number] ??
            throw new \OutOfBoundsException("Invocation has no argument $number");
    }
}
