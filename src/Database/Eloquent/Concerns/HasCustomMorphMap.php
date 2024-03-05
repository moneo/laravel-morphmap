<?php

namespace MCUCEN\LaravelMorphMap\Database\Eloquent\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;

trait HasCustomMorphMap
{
    protected array $customMorphMap = [];
    protected string $defaultMorphType = __CLASS__;

    public function morphToMany($related, $name, $table = null, $foreignPivotKey = null,
                                $relatedPivotKey = null, $parentKey = null,
                                $relatedKey = null, $relation = null, $inverse = false): MorphToMany
    {
        Relation::morphMap([
            $this->customMorphMap($related) => __CLASS__,
        ]);

        $args = func_get_args();

        return parent::morphToMany(...$args);
    }

    private function customMorphMap(string $related): string
    {
        if (array_key_exists($related, $this->customMorphMap)) {
            return $this->customMorphMap[$related];
        }

        return $this->defaultMorphType;
    }
}
