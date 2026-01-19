<?php
declare(strict_types=1);

namespace Philiagus\Figment\Cli\Command;

use Philiagus\Figment\Cli\Contract;
use Philiagus\Figment\Cli\Contract\Command\OptionConfigurationProvider;
use Philiagus\Parser\Base\Subject;
use Philiagus\Parser\Contract\Parser;

class Configuration implements Contract\Command\ConfigurationReceiver
{

    private string $name;
    private string $description;

    /** @var OptionConfigurationProvider[] */
    private array $options = [];

    /** @var array<string, OptionConfigurationProvider> */
    private array $nameToOption = [];
    /** @var array<string, OptionConfigurationProvider> */
    private array $shortToOption = [];

    private ?Parser $leftoversParser = null;

    public function __construct(
        private readonly Contract\Command $command
    )
    {
        $command->populateConfiguration($this);
    }

    public function invoke(Contract\Terminal $terminal): int|false
    {
        $arguments = $terminal->arguments;
        if (($arguments[0] ?? '') !== $this->name) {
            return false;
        }

        /** @var \SplObjectStorage<OptionConfigurationProvider, mixed> $optionToValues */
        $optionToValues = new \SplObjectStorage();
        foreach ($this->options as $option) {
            if ($option->isBoolean()) {
                $optionToValues[$option] = false;
            }
        }
        $optionGreedyArguments = null;
        $optionMaxArguments = null;
        $activeOption = null;
        $leftoverArguments = [];
        $forceAppend = false;
        foreach ($arguments as $index => $argument) {
            if ($index === 0)
                continue;
            if ($forceAppend) {
                $leftoverArguments[] = $argument;
            }
            if (
                $optionGreedyArguments !== null &&
                $optionGreedyArguments > 0 &&
                $optionMaxArguments > 0
            ) {
                $optionGreedyArguments--;
                $optionMaxArguments--;
                $optionToValues[$activeOption] = [...$optionToValues[$activeOption], $argument];
                continue;
            }

            if ($argument === '--') {
                $forceAppend = true;
                continue;
            }

            if (str_starts_with($argument, '--')) {
                $activeOption = null;
                $optionMaxArguments = null;
                $optionGreedyArguments = null;
                $argumentName = substr($argument, 2);
                if (!isset($this->nameToOption[$argumentName])) {
                    throw new \OutOfBoundsException("Argument --$argumentName is not supported by command {$this->name}");
                }
                $option = $this->nameToOption[$argumentName];
                if ($option->isBoolean()) {
                    $optionToValues[$option] = true;
                    continue;
                }
                $optionToValues[$option] ??= [];
                $activeOption = $option;
                $optionMaxArguments = $option->getMaxArgumentCount();
                $optionGreedyArguments = $option->getGreedyArgumentCount();
                continue;
            }

            if (str_starts_with($argument, '-')) {
                $activeOption = null;
                $optionMaxArguments = null;
                $optionGreedyArguments = null;
                foreach (mb_str_split(substr($argument, 1), encoding: 'UTF-8') as $shortArgument) {
                    if (!isset($this->shortToOption[$shortArgument])) {
                        throw new \OutOfBoundsException("The short argument -$shortArgument is not supported");
                    }
                    $option = $this->shortToOption[$shortArgument];
                    if ($option->isBoolean()) {
                        $optionToValues[$option] = true;
                        continue;
                    }
                    $optionToValues[$option] ??= [];
                    if ($activeOption !== null) {
                        throw new \RuntimeException(
                            "Trying to use both -" . $option->getShortName() .
                            " and -" . $activeOption->getShortName() .
                            " in short argument " . $argument .
                            ". Only one parameter-taking option can be defined per short definition"
                        );
                    }
                    $activeOption = $option;
                    $optionMaxArguments = $option->getMaxArgumentCount();
                    $optionGreedyArguments = $option->getGreedyArgumentCount();
                }
                continue;
            }
            if ($optionMaxArguments !== null && $optionMaxArguments > 0) {
                $optionMaxArguments--;
                $optionToValues[$activeOption] = [...$optionToValues[$activeOption], $argument];
                continue;
            }
            $leftoverArguments[] = $argument;
        }

        $missingMandatory = [];
        foreach ($this->options as $option) {
            if ($option->isMandatory() && !isset($optionToValues[$option])) {
                $missingMandatory[] = $option;
            }
        }
        if ($missingMandatory) {
            $missing = [];
            foreach ($missingMandatory as $missingOption) {
                $name = $missingOption->getLongName();
                $short = $missingOption->getShortName();
                $names = [];
                if ($name !== null) $names[] = "--$name";
                if ($short !== null) $names[] = "-$short";
                $missing[] = implode('/', $names);
            }
            throw new \RuntimeException(
                "You must provide the following options: " . implode(', ', $missing)
            );
        }
        foreach ($optionToValues as $option) {
            $value = $optionToValues[$option];
            if ($option->getMaxArgumentCount() === 1) {
                $value = $value[0] ?? null;
            }
            $option->consume($value);
        }


        $this->leftoversParser?->parse(Subject::default($leftoverArguments, 'CLI Arguments'));

        return $this->command->invoke($terminal);
    }

    public function setName(string $name): Contract\Command\ConfigurationReceiver
    {
        $this->name = $name;

        return $this;
    }

    public function setDescription(string $description): Contract\Command\ConfigurationReceiver
    {
        $this->description = $description;

        return $this;
    }

    public function addOption(OptionConfigurationProvider $configuration): Contract\Command\ConfigurationReceiver
    {
        $name = $configuration->getLongName();
        $short = $configuration->getShortName();
        if ($name === null && $short === null) {
            throw new \LogicException(
                "Trying to provide an option that has neither long nor short name"
            );
        }
        $this->options[] = $configuration;
        $this->nameToOption[$name] = $configuration;
        $this->shortToOption[$short] = $configuration;

        return $this;
    }

    public function takesArguments(Parser $parser): Contract\Command\ConfigurationReceiver
    {
        $this->leftoversParser = $parser;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
