<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\Contract;

interface Invocation
{

    public function phpBinary(): \SplFileInfo;

    public function scriptFile(): \SplFileInfo;


    public function argumentCount(): int;

    public function allArguments(): array;

    public function getArgument(int $number): string;

}
