<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\Contract;

interface TerminalArguments extends \Countable, \ArrayAccess
{

    public function count(): int;

    /**
     * @param int $number
     *
     * @return string
     * @throws \OutOfBoundsException
     */
    public function get(int $number): string;

    public function has(int $number): bool;

    public function offsetSet(mixed $offset, mixed $value): never;
    public function offsetUnset(mixed $offset): never;

}
