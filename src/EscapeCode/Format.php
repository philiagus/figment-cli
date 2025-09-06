<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\EscapeCode;

use Philiagus\Figment\Cli\Contract;

enum Format: string implements Contract\Stream\EscapeCode
{

    case BOLD = "1";
    case UNDERLINE = "4";
    case BLINK = "5";
    case REVERSE = "7";
    case FRAMED = "51";
    case ENCIRCLED = "52";
    case OVERLINED = "53";

    public function code(): string
    {
        return $this->value;
    }
}
