<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli;

use Philiagus\Figment\Cli\Stream\InStream;
use Philiagus\Figment\Cli\Stream\OutStream;

class Terminal implements Contract\Terminal
{


    public Contract\TerminalArguments $arguments {
        get => $this->arguments;
    }

    public function __construct(array $argv)
    {
        $this->arguments = new TerminalArguments(...array_slice($argv, 1));
    }

    public function phpBinary(): \SplFileInfo
    {
        return new \SplFileInfo(PHP_BINARY);
    }

    public function scriptFile(): \SplFileInfo
    {
        return new \SplFileInfo($_SERVER['SCRIPT_FILENAME']);
    }

    public function stdin(): Contract\Stream\InStream
    {
        return $this->inStream(0);
    }

    public function inStream(int $number): Contract\Stream\InStream
    {
        return new InStream($number);
    }

    public function stdout(): Contract\Stream\OutStream
    {
        return $this->outStream(1);
    }

    public function outStream(int $number): Contract\Stream\OutStream
    {
        return new OutStream($number);
    }

    public function stderr(): Contract\Stream\OutStream
    {
        return $this->outStream(2);
    }
}
