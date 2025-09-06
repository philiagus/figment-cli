<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\EscapeCode;

use Philiagus\Figment\Cli\Contract;

readonly class Custom implements Contract\Stream\EscapeCode
{

    public function __construct(private string $code)
    {
    }

    public function code(): string
    {
        return $this->code;
    }
}
