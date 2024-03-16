<?php

namespace Moneo\LaravelMorphMap\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Moneo\LaravelMorphMap\Database\Eloquent\Concerns\HasCustomMorphMap;

class Comment extends Model
{
    use HasCustomMorphMap;

    protected $fillable = ['content'];
    protected $table = 'comments';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->customMorphMap = [
            'commentable' => self::class,
        ];
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
