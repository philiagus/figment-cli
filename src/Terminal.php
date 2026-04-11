<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli;

use Philiagus\Figment\Cli\Stream\InStream;
use Philiagus\Figment\Cli\Stream\OutStream;

class Terminal implements Contract\Terminal
{

    public Contract\Stream\InStream $stdin {
        get => $this->inStream(0);
    }

    public Contract\Stream\OutStream $stdout {
        get => $this->outStream(1);
    }

    public Contract\Stream\OutStream $stderr {
        get => $this->outStream(2);
    }

    public Contract\TerminalArguments $arguments;

    public \SplFileInfo $phpBinary {
        get => new \SplFileInfo(PHP_BINARY);
    }
    public \SplFileInfo $scriptFile {
        get => new \SplFileInfo($_SERVER['SCRIPT_FILENAME']);
    }

    public function __construct(array $argv)
    {
        $this->arguments = new TerminalArguments(...array_slice($argv, 1));
    }

    public function inStream(int $number): Contract\Stream\InStream
    {
        return new InStream($number);
    }

    public function outStream(int $number): Contract\Stream\OutStream
    {
        return new OutStream($number);
    }
}
