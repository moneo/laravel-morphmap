<?php

namespace Moneo\LaravelMorphMap\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Moneo\LaravelMorphMap\Database\Eloquent\Concerns\HasCustomMorphMap;

class Category extends Model
{
    use HasCustomMorphMap;

    protected $fillable = ['name'];

    protected $table = 'categories';

    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'categoryable');
    }

    public function videos(): MorphToMany
    {
        return $this->morphedByMany(Video::class, 'categoryable');
    }
}
