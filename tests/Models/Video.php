<?php

namespace Moneo\LaravelMorphMap\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Moneo\LaravelMorphMap\Database\Eloquent\Concerns\HasCustomMorphMap;

class Video extends Model
{
    use HasCustomMorphMap;

    protected $fillable = ['title'];

    protected $table = 'videos';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->customMorphMap = [
            Category::class => 'video',
        ];
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }
}
