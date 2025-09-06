<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\Contract\Command;

use Philiagus\Parser\Contract\Parser;

interface OptionConfigurationProvider
{
    public function getLongName(): ?string;

    public function getShortName(): ?string;

    public function getNames(): string;

    public function getGreedyArgumentCount(): int;
    public function getMaxArgumentCount(): int;

    public function isBoolean(): bool;

    /**
     * @param null|bool|string|string[] $value
     *
     * @return void
     */
    public function consume(null|bool|string|array $value): void;

    public function isMandatory(): bool;

}
