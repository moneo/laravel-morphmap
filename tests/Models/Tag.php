<?php

namespace Moneo\LaravelMorphMap\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];
    protected $table = 'tags';
}
