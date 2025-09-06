<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\Contract;

use Philiagus\Figment\Cli\Contract\Stream\InStream;
use Philiagus\Figment\Cli\Contract\Stream\OutStream;

interface Terminal
{

    public TerminalArguments $arguments {
        get;
    }

    public function phpBinary(): \SplFileInfo;

    public function scriptFile(): \SplFileInfo;
    public function inStream(int $number): InStream;

    public function outStream(int $number): OutStream;

    public function stdin(): InStream;

    public function stdout(): OutStream;
    public function stderr(): OutStream;
}
