<?php

namespace Moneo\LaravelMorphMap\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class NewTag extends Model
{
    protected $fillable = ['name'];
    protected $table = 'new_tags';

}
