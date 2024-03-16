<?php

namespace Moneo\LaravelMorphMap\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Moneo\LaravelMorphMap\Database\Eloquent\Concerns\HasCustomMorphMap;

class Post extends Model
{
    use HasCustomMorphMap;

    protected $fillable = ['title'];
    protected $table = 'posts';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->customMorphMap = [
            'tag' => 'Moneo\LaravelMorphMap\Tests\Models\Tag',
        ];
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany('Moneo\LaravelMorphMap\Tests\Models\Tag', 'taggable');
    }
}
