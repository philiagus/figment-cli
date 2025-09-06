<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\Stream;


use Philiagus\Figment\Cli\Contract;

class StreamProvider implements Contract\Stream\StreamProvider {

    public function in(int $number): Contract\Stream\InStream
    {
        return new InStream($number);
    }

    public function out(int $number): Contract\Stream\OutStream
    {
        return new OutStream($number);
    }

    public function stdin(): Contract\Stream\InStream
    {
        return new InStream(0);
    }

    public function stdout(): Contract\Stream\OutStream
    {
        return new OutStream(1);
    }

    public function stderr(): Contract\Stream\OutStream
    {
        return new OutStream(2);
    }
}
