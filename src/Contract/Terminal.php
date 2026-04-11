<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\Contract;

use Philiagus\Figment\Cli\Contract\Stream\InStream;
use Philiagus\Figment\Cli\Contract\Stream\OutStream;

interface Terminal
{

    public InStream $stdin {get;}
    public OutStream $stdout {get;}
    public OutStream $stderr {get;}

    public \SplFileInfo $phpBinary {get;}
    public \SplFileInfo $scriptFile {get;}

    public TerminalArguments $arguments {get;}

    public function inStream(int $number): InStream;

    public function outStream(int $number): OutStream;
}
