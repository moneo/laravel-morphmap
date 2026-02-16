<?php

namespace Moneo\LaravelMorphMap\Database\Eloquent\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Provides per-relationship custom morph map support for Eloquent models.
 *
 * This trait allows you to define a custom morph map for each polymorphic
 * relationship, overriding Laravel's default global morph map behavior.
 *
 * Usage:
 *   - Set $customMorphMap to an array keyed by the related model's FQCN,
 *     with the morph alias as the value.
 *   - Optionally set $defaultMorphType to the fallback morph alias
 *     (defaults to static::class).
 *
 * @property array<class-string, string> $customMorphMap
 * @property string $defaultMorphType
 */
trait HasCustomMorphMap
{
    /**
     * Custom morph map keyed by the related model's fully qualified class name.
     *
     * Example: [App\Models\Category::class => 'post']
     *
     * @var array<class-string, string>
     */
    protected array $customMorphMap = [];

    /**
     * The default morph type alias used when no custom mapping is found.
     * Defaults to the fully qualified class name of the model using this trait.
     */
    protected string $defaultMorphType = '';

    /**
     * Initialize the HasCustomMorphMap trait.
     *
     * This follows Laravel's trait initialization convention (initialize{TraitName}).
     * It is automatically called by the Eloquent Model constructor.
     */
    public function initializeHasCustomMorphMap(): void
    {
        if ($this->defaultMorphType === '') {
            $this->defaultMorphType = static::class;
        }
    }

    /**
     * Define a polymorphic many-to-many relationship with custom morph map support.
     *
     * Registers the custom morph type for the related model before delegating
     * to the parent morphToMany implementation.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string|null  $table
     * @param  string|null  $foreignPivotKey
     * @param  string|null  $relatedPivotKey
     * @param  string|null  $parentKey
     * @param  string|null  $relatedKey
     * @param  string|null  $relation
     * @param  bool  $inverse
     */
    public function morphToMany(
        $related,
        $name,
        $table = null,
        $foreignPivotKey = null,
        $relatedPivotKey = null,
        $parentKey = null,
        $relatedKey = null,
        $relation = null,
        $inverse = false,
    ): MorphToMany {
        $this->registerCustomMorphMap($related);

        return parent::morphToMany(
            $related,
            $name,
            $table,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey,
            $relatedKey,
            $relation,
            $inverse,
        );
    }

    /**
     * Define a polymorphic, inverse many-to-many relationship with custom morph map support.
     *
     * Registers the custom morph type for the related model before delegating
     * to the parent morphedByMany implementation.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string|null  $table
     * @param  string|null  $foreignPivotKey
     * @param  string|null  $relatedPivotKey
     * @param  string|null  $parentKey
     * @param  string|null  $relatedKey
     * @param  string|null  $relation
     */
    public function morphedByMany(
        $related,
        $name,
        $table = null,
        $foreignPivotKey = null,
        $relatedPivotKey = null,
        $parentKey = null,
        $relatedKey = null,
        $relation = null,
    ): MorphToMany {
        $this->registerCustomMorphMap($related);

        return parent::morphedByMany(
            $related,
            $name,
            $table,
            $foreignPivotKey,
            $relatedPivotKey,
            $parentKey,
            $relatedKey,
            $relation,
        );
    }

    /**
     * Register the custom morph map entry for the given related model.
     */
    protected function registerCustomMorphMap(string $related): void
    {
        $morphType = $this->resolveCustomMorphType($related);

        Relation::morphMap([
            $morphType => static::class,
        ]);
    }

    /**
     * Resolve the morph type alias for the given related model class.
     *
     * Returns the custom morph type if one is defined for the related model,
     * otherwise returns the default morph type.
     */
    protected function resolveCustomMorphType(string $related): string
    {
        return $this->customMorphMap[$related] ?? $this->defaultMorphType;
    }
}
