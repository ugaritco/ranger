<?php

namespace Ugarit\Ranger\Collectors;

use Heritage\Support\Collection;
use Ugarit\Ranger\Support\HasPaths;
use Ugarit\Ranger\Support\Inventory;

abstract class Collector
{
    use HasPaths;

    protected Collection $cached;

    /**
     * @param  callable[]  $callbacks
     */
    public function run(array $callbacks): void
    {
        foreach ($callbacks as $callback) {
            $this->getCollection()->each(fn ($item) => $callback($item));
        }
    }

    /**
     * @param  callable[]  $callbacks
     */
    public function runOnCollection(array $callbacks): void
    {
        collect($callbacks)->each(fn ($callback) => $callback($this->getCollection()));
    }

    public function getCollection(): Collection
    {
        return $this->cached ??= $this->collect();
    }

    protected function inventory(): Inventory
    {
        return Inventory::in(...$this->appPaths);
    }

    abstract public function collect(): Collection;
}
