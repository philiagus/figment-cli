<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli;

class TerminalArguments implements Contract\TerminalArguments
{
    /** @var string[] */
    private array $arguments;

    public function __construct(string ...$arguments) {
        $this->arguments = $arguments;
    }

    public function count(): int
    {
        return count($this->arguments);
    }

    public function get(int $number): string
    {
        return $this->arguments[$number] ??
            throw new \OutOfBoundsException("Offset $number does not exist");
    }

    public function has(int $number): bool
    {
        return isset($this->arguments[$number]);
    }

    public function offsetSet(mixed $offset, mixed $value): never
    {
        throw new \LogicException("Cannot change CLI provided arguments");
    }

    public function offsetUnset(mixed $offset): never
    {
        throw new \LogicException("Cannot unset CLI provided arguments");
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }
}
