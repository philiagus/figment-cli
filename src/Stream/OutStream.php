<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\Stream;

use Philiagus\Figment\Cli\Contract;
use Philiagus\Figment\Cli\Contract\Stream\EscapeCode;

class OutStream implements Contract\Stream\OutStream
{

    /** @var resource */
    private readonly mixed $stream;

    /** @var array<string, EscapeCode> */
    private array $activeCodes = [];

    public function __construct(private readonly int $fd)
    {
        $this->stream = fopen('php://fd/' . $this->fd, 'w') ?:
            throw new \RuntimeException("Could not open fd({$this->fd}) for write");
    }

    public function activateEscapeCodes(EscapeCode ...$escapeCodes): Contract\Stream\OutStream
    {
        if (empty($escapeCodes))
            return $this;

        foreach ($escapeCodes as $escapeCode) {
            $code = $escapeCode->code();
            if (!isset($this->activeCodes[$code])) {
                $this->activeCodes[$code] = $escapeCode;
            }
        }

        return $this->printEscapeCodes(...$escapeCodes);
    }

    private function printEscapeCodes(EscapeCode ...$escapeCodes): self
    {
        if (empty($escapeCodes))
            return $this;
        $codes = array_unique(array_map(
            static fn(EscapeCode $code): string => $code->code(),
            $escapeCodes
        ));
        return $this->raw("\e[" . implode(';', $codes) . "m");
    }

    public function raw(string $string): Contract\Stream\OutStream
    {
        if (fwrite($this->stream, $string) === false) {
            throw new \RuntimeException(
                "Could not write to output fd({$this->fd})"
            );
        }

        return $this;
    }

    public function deactivateAllEscapeCodes(): Contract\Stream\OutStream
    {
        $this->activeCodes = [];
        $this->raw("\e[0m");

        return $this;
    }

    public function out(string|EscapeCode ...$output): Contract\Stream\OutStream
    {
        $collectedCodes = [];
        foreach($output as $line) {
            if($line instanceof EscapeCode) {
                $collectedCodes[] = $line;
                continue;
            }
            $this->print($line, ...$collectedCodes);
            $collectedCodes = [];
        }

        return $this;
    }

    public function println(string $content, EscapeCode ...$escapeCodes): Contract\Stream\OutStream
    {
        return $this->print($content, ...$escapeCodes)->ln();
    }

    public function ln(): Contract\Stream\OutStream
    {
        return $this->raw(PHP_EOL);
    }

    public function print(string $content, EscapeCode ...$escapeCodes): Contract\Stream\OutStream
    {
        if (empty($escapeCodes)) {
            $this->raw($content);
            return $this;
        }

        $this->printEscapeCodes(...$this->activeCodes, ...$escapeCodes);
        $this->raw($content)->raw("\e[0m");
        $this->printEscapeCodes(...$this->activeCodes);

        return $this;
    }
}
