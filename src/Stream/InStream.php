<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\Stream;

use Philiagus\Figment\Cli\Contract;

class InStream implements Contract\Stream\InStream
{

    /** @var resource */
    private mixed $stream = null;

    public function __construct(private readonly int $fd)
    {
        $this->stream = fopen('php://fd/' . $this->fd, 'r') ?:
            throw new \RuntimeException("Could not open fd({$this->fd}) for read");
    }

    public function readLine(?int $maxLength = null): string
    {
        $line = fgets($this->stream, $maxLength);
        if($line === false) {
            throw new \RuntimeException("Could not read from fd({$this->fd})");
        }
        return substr($line, 0, -strlen(PHP_EOL));
    }

    public function readBytes(int $maxLength = 1024): string
    {
        return fread($this->stream, $maxLength);
    }
}
