<?php

namespace Ugarit\Ranger\Collectors;

use Heritage\Database\Eloquent\Model;
use Heritage\Database\Eloquent\Relations\BelongsToMany;
use Heritage\Database\Eloquent\Relations\HasMany;
use Heritage\Database\Eloquent\Relations\HasManyThrough;
use Heritage\Database\Eloquent\Relations\HasOneOrMany;
use Heritage\Database\Eloquent\Relations\HasOneOrManyThrough;
use Heritage\Database\Eloquent\Relations\MorphMany;
use Heritage\Database\Eloquent\Relations\MorphOneOrMany;
use Heritage\Database\Eloquent\Relations\MorphToMany;
use Heritage\Database\Eloquent\Relations\Pivot;
use Heritage\Support\Collection;
use Heritage\Support\Str;
use Ugarit\Ranger\Components\Model as ModelComponent;
use Ugarit\Ranger\Support\Ignores;
use Ugarit\Surveyor\Analyzed\ClassLikeResult;
use Ugarit\Surveyor\Analyzer\Analyzer;
use Ugarit\Surveyor\Types\ArrayType;
use Ugarit\Surveyor\Types\BoolType;
use Ugarit\Surveyor\Types\ClassType;
use Ugarit\Surveyor\Types\Contracts\Type as SurveyorTypeContract;
use Ugarit\Surveyor\Types\Type;
use ReflectionClass;
use Throwable;

class Models extends Collector
{
    protected Collection $modelComponents;

    /** @var array<class-string<Model>, true> */
    protected array $ignoredModels = [];

    public function __construct(protected Analyzer $analyzer)
    {
        $this->modelComponents = collect();
    }

    /**
     * @return Collection<ModelComponent>
     */
    public function collect(): Collection
    {
        $discovered = $this->inventory()->classesExtending(Model::class, 'Heritage\Foundation\Auth\User', Pivot::class);

        foreach ($discovered as $model) {
            $this->toComponent($model);
        }

        return $this->modelComponents->values();
    }

    /**
     * @param  class-string<Model>  $model
     */
    public function get(string $model): ?ModelComponent
    {
        return $this->getCollection()->first(
            fn (ModelComponent $component) => $component->name === $model,
        );
    }

    /**
     * @param  class-string<Model>  $model
     */
    protected function toComponent(string $model): void
    {
        $result = $this->analyzer->analyzeClass($model)->result();

        if ($result === null) {
            return;
        }

        if ($result->isIgnored() || Ignores::markedClass($model)) {
            $this->ignoredModels[$model] = true;

            return;
        }

        $modelComponent = new ModelComponent($model);

        $eagerLoadRelations = $this->getModelArrayProperty($model, 'with');
        $modelComponent->setSnakeCaseAttributes($this->shouldSnakeCase($result));
        $modelComponent->setHidden($this->getModelArrayProperty($model, 'hidden'));
        $modelComponent->setVisible($this->getModelArrayProperty($model, 'visible'));
        $modelComponent->setAppends($this->getModelArrayProperty($model, 'appends'));

        $this->modelComponents->offsetSet($modelComponent->name, $modelComponent);
        $modelComponent->setFilePath($result->filePath());

        foreach ($result->publicProperties() as $property) {
            if ($property->modelAttribute || $property->fromDocBlock) {
                $modelComponent->addAttribute($property->name, $property->type);
            }
        }

        foreach ($result->publicMethods() as $method) {
            if ($method->isModelRelation()) {
                $returnType = $this->resolveReturnType(
                    $method->returnType(),
                    in_array($method->name(), $eagerLoadRelations)
                        || in_array(Str::snake($method->name()), $eagerLoadRelations),
                );

                if ($returnType === null) {
                    continue;
                }

                $modelComponent->addRelation($method->name(), $returnType);
            }
        }
    }

    /**
     * @param  class-string<Model>  $model
     * @return list<string>
     */
    protected function getModelArrayProperty(string $model, string $propertyName): array
    {
        $reflection = new ReflectionClass($model);
        $defaults = $reflection->getDefaultProperties();

        $propertyValues = [];

        if (array_key_exists($propertyName, $defaults) && is_array($defaults[$propertyName])) {
            $propertyValues = array_values(array_filter($defaults[$propertyName], 'is_string'));
        }

        $attributeClass = match ($propertyName) {
            'visible' => 'Heritage\Database\Eloquent\Attributes\Visible',
            'hidden' => 'Heritage\Database\Eloquent\Attributes\Hidden',
            'appends' => 'Heritage\Database\Eloquent\Attributes\Appends',
            default => null,
        };

        $attributeValues = [];

        if ($attributeClass) {
            try {
                do {
                    $attributes = $reflection->getAttributes($attributeClass);

                    if (count($attributes) > 0) {
                        $arguments = $attributes[0]->getArguments();
                        $columns = [];

                        if (count($arguments) > 0) {
                            $firstArg = reset($arguments);

                            if (is_array($firstArg)) {
                                $columns = $firstArg;
                            } else {
                                $columns = $arguments;
                            }
                        }

                        $attributeValues = array_values(array_filter($columns, 'is_string'));

                        break;
                    }
                } while ($reflection = $reflection->getParentClass());
            } catch (Throwable) {
                // Ignore any reflection or class loading errors
            }
        }

        return array_values(array_unique(array_merge($propertyValues, $attributeValues)));
    }

    protected function shouldSnakeCase(ClassLikeResult $result): bool
    {
        $propertyName = 'snakeAttributes';

        if (! $result->hasProperty($propertyName) || ! $result->getProperty($propertyName)->type instanceof BoolType) {
            return true;
        }

        return $result->getProperty($propertyName)->type->value;
    }

    protected function resolveReturnType(SurveyorTypeContract $type, bool $required): ?SurveyorTypeContract
    {
        if (! $type instanceof ClassType) {
            return null;
        }

        $relatedModel = $type->genericTypes()['TRelatedModel'] ?? null;

        if (! $relatedModel || ! $relatedModel instanceof ClassType) {
            return null;
        }

        if (! $this->modelComponents->offsetExists($relatedModel->value)) {
            $this->toComponent($relatedModel->value);
        }

        // The related model is not being generated, so there is no type left
        // for the relation to point at.
        if (isset($this->ignoredModels[$relatedModel->value])) {
            return null;
        }

        $collectionRelations = [
            BelongsToMany::class,
            HasMany::class,
            HasManyThrough::class,
            MorphMany::class,
            MorphToMany::class,
        ];

        if (in_array($type->value, $collectionRelations)) {
            return (new ArrayType([$relatedModel]))->required($required);
        }

        $maybeCollectionRelation = [
            MorphOneOrMany::class,
            HasOneOrMany::class,
            HasOneOrManyThrough::class,
        ];

        if (in_array($type->value, $maybeCollectionRelation)) {
            return Type::union(new ClassType($type->value), new ArrayType([$relatedModel]))->nullable()->required($required);
        }

        return $relatedModel->nullable()->required($required);
    }
}
