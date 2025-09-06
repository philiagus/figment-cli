<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\Command;

use Philiagus\Figment\Cli\Contract\Command\OptionConfigurationProvider;
use Philiagus\Parser\Base\Subject;
use Philiagus\Parser\Contract\Parser;

readonly class Option implements OptionConfigurationProvider
{

    private function __construct(
        private ?string $longName,
        private ?string $shortName,
        private int $greedyArguments,
        private int $maxArguments,
        private Parser $parser,
        private bool $mandatory
    )
    {
        if ($this->longName === null && $this->shortName === null) {
            throw new \LogicException(
                "Options mus have at least a long name or a short name"
            );
        }
        if ($this->greedyArguments > $this->maxArguments) {
            throw new \LogicException(
                "Greedy arguments must not be greater than the max arguments"
            );
        }
    }


    public static function boolean(
        ?string $longName,
        ?string $shortName,
        Parser $parser,
    ): self
    {
        return new self($longName, $shortName, 0, 0, $parser, true);
    }

    public static function single(
        ?string $longName,
        ?string $shortName,
        Parser $parser,
        bool $mandatory = false,
    ): self
    {
        return new self($longName, $shortName, 1, 1, $parser, $mandatory);
    }

    public static function list(
        ?string $longName, ?string $shortName,
        Parser $parser,
        int $greedyArguments = 0, int $maxArguments = PHP_INT_MAX,
        bool $mandatory = false
    ): self {
        return new self($longName, $shortName, $greedyArguments, $maxArguments, $parser, $mandatory);
    }

    public function getLongName(): ?string
    {
        return $this->longName;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function getGreedyArgumentCount(): int
    {
        return $this->greedyArguments;
    }

    public function getMaxArgumentCount(): int
    {
        return $this->maxArguments;
    }

    public function isBoolean(): bool
    {
        return $this->maxArguments === 0;
    }

    public function consume(null|bool|array|string $value): void
    {
        $count = match(true) {
            $value === null,
                $value === true,
                $value === false => 0,
            is_string($value) => 1,
            default => count($value)
        };
        if($this->greedyArguments > $count) {
            throw new \RuntimeException(
                "Option {$this->getNames()} must be provided with at least {$this->greedyArguments} arguments"
            );
        }
        if($count > $this->maxArguments) {
            throw new \RuntimeException(
                "Option {$this->getNames()} must be provided with at most {$this->maxArguments} arguments"
            );
        }
        $this->parser->parse(Subject::default($value, $this->getNames()));
    }

    public function getNames(): string
    {
        if($this->longName !== null) {
            if($this->shortName !== null) {
                return "-$this->shortName/--$this->longName";
            }
            return "--$this->longName";
        }
        return "-$this->shortName";
    }

    public function isMandatory(): bool
    {
        return $this->mandatory;
    }
}


