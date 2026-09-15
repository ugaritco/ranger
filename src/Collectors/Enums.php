<?php

namespace Ugarit\Ranger\Collectors;

use BackedEnum;
use Heritage\Support\Collection;
use Ugarit\Ranger\Components\Enum as EnumComponent;
use Ugarit\Ranger\Support\Ignores;
use ReflectionEnum;
use ReflectionEnumBackedCase;

class Enums extends Collector
{
    /**
     * @return Collection<EnumComponent>
     */
    public function collect(): Collection
    {
        return collect($this->inventory()->enums())
            ->map($this->toComponent(...))
            ->filter()
            ->values();
    }

    /**
     * @param  class-string<BackedEnum|\UnitEnum>  $enum
     */
    protected function toComponent(string $enum): ?EnumComponent
    {
        $reflection = new ReflectionEnum($enum);

        if (Ignores::marked($reflection)) {
            return null;
        }

        $cases = [];

        foreach ($reflection->getCases() as $index => $case) {
            if (Ignores::marked($case)) {
                continue;
            }

            $cases[$case->getName()] = $case instanceof ReflectionEnumBackedCase
                ? $case->getBackingValue()
                : $index;
        }

        $component = new EnumComponent($enum, $cases);
        $component->setFilePath($reflection->getFileName());

        return $component;
    }
}
