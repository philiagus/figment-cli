<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\Contract\Stream;

interface InStream {

    public function readLine(?int $maxLength = null): string;

    public function readBytes(int $maxLength = 1024): string;

}
