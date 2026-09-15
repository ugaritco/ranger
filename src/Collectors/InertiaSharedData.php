<?php

namespace Ugarit\Ranger\Collectors;

use Heritage\Support\Collection;
use Inertia\Middleware;
use Ugarit\Ranger\Components\InertiaSharedData as SharedDataComponent;
use Ugarit\Surveyor\Analyzer\Analyzer;
use Ugarit\Surveyor\Types\ArrayType;
use Ugarit\Surveyor\Types\BoolType;
use Ugarit\Surveyor\Types\Type;
use Ugarit\Surveyor\Types\UnionType;

class InertiaSharedData extends Collector
{
    public function __construct(protected Analyzer $analyzer)
    {
        //
    }

    /**
     * @return Collection<SharedDataComponent>
     */
    public function collect(): Collection
    {
        $discovered = $this->inventory()->classesExtending('Inertia\\Middleware');

        return collect($discovered)->map($this->processSharedData(...));
    }

    /**
     * @param  class-string<Middleware>  $class
     */
    protected function processSharedData(string $class): SharedDataComponent
    {
        $result = $this->analyzer->analyzeClass($class)->result();

        if (! $result->hasMethod('share')) {
            return new SharedDataComponent(new ArrayType([]));
        }

        $data = $result->getMethod('share')->returnType();

        if ($data instanceof UnionType) {
            $finalArray = [];

            foreach ($data->types as $type) {
                if ($type instanceof ArrayType) {
                    foreach ($type->value as $key => $value) {
                        $finalArray[$key] ??= [];
                        $finalArray[$key][] = $value;
                    }
                }
            }

            foreach ($finalArray as $key => $values) {
                $finalArray[$key] = Type::union(...$values);
            }

            $data = new ArrayType($finalArray);
        }

        $withAllErrors = $result->hasProperty('withAllErrors')
            && $result->getProperty('withAllErrors')->type instanceof BoolType
            && $result->getProperty('withAllErrors')->type->value === true;

        return new SharedDataComponent($data, $withAllErrors);
    }
}
