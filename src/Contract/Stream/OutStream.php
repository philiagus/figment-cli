<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\Contract\Stream;

interface OutStream
{

    public const string CLEAR_LINE = "\r";

    public function print(string $content, EscapeCode ...$escapeCodes): self;

    public function println(string $content, EscapeCode ...$escapeCodes): self;

    public function ln(): self;

    public function activateEscapeCodes(EscapeCode ...$escapeCodes): self;

    public function deactivateAllEscapeCodes(): self;

    public function raw(string $string): self;

}
