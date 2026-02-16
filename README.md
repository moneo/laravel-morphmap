![](https://banners.beyondco.de/Custom%20Morph%20Map.png?theme=light&packageManager=composer+require&packageName=moneo%2Flaravel-morphmap&pattern=anchorsAway&style=style_1&description=Custom+morphMap+for+each+relation&md=1&showWatermark=0&fontSize=100px&images=link)

# Laravel Custom Morph Map

[![Tests](https://github.com/moneo/laravel-morphmap/actions/workflows/php.yml/badge.svg)](https://github.com/moneo/laravel-morphmap/actions/workflows/php.yml)
[![Latest Stable Version](https://poser.pugx.org/moneo/laravel-morphmap/v)](https://packagist.org/packages/moneo/laravel-morphmap)
[![License](https://poser.pugx.org/moneo/laravel-morphmap/license)](https://packagist.org/packages/moneo/laravel-morphmap)

Use **different morph maps for different relationships** on the same model.

By default, Laravel's `Relation::morphMap()` applies globally to all polymorphic relationships. This package lets you define a custom morph map **per relationship**, so different polymorphic relations on the same model can use different type aliases.

## Requirements

| Version | PHP     | Laravel     |
|---------|---------|-------------|
| 2.x     | ^8.1    | 10, 11, 12  |
| 1.x     | >=8.0   | 8, 9, 10, 11|

## Installation

```bash
composer require moneo/laravel-morphmap
```

No service provider registration is needed. Just use the trait.

## Usage

Add the `HasCustomMorphMap` trait to your model and define the `$customMorphMap` property in your constructor.

The array keys are the **related model's fully qualified class name**, and the values are the **morph type alias** you want stored in the database for that relationship:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Moneo\LaravelMorphMap\Database\Eloquent\Concerns\HasCustomMorphMap;

class Post extends Model
{
    use HasCustomMorphMap;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Define custom morph types per related model.
        // Key: related model class, Value: morph type alias
        $this->customMorphMap = [
            Category::class => 'post',
        ];

        // Optional: override the default morph type for all other relationships.
        // If not set, defaults to the fully qualified class name (static::class).
        // $this->defaultMorphType = 'post';
    }

    /**
     * Tags relationship — no custom mapping defined for Tag,
     * so the default morph type (App\Models\Post) will be used.
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Categories relationship — custom mapping defined above,
     * so 'post' will be stored as the morph type.
     */
    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }
}
```

### Inverse Relationships (`morphedByMany`)

The trait also supports `morphedByMany` for inverse polymorphic many-to-many relationships:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Moneo\LaravelMorphMap\Database\Eloquent\Concerns\HasCustomMorphMap;

class Category extends Model
{
    use HasCustomMorphMap;

    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'categoryable');
    }

    public function videos(): MorphToMany
    {
        return $this->morphedByMany(Video::class, 'categoryable');
    }
}
```

### How It Works

The trait uses Laravel's `initializeHasCustomMorphMap()` convention to automatically set the default morph type when the model is constructed. No need to call `parent::__construct()` before setting properties -- the trait handles initialization automatically.

When you call `morphToMany()` or `morphedByMany()`, the trait:

1. Looks up the related model class in your `$customMorphMap` array
2. If found, registers that alias in Laravel's global morph map
3. If not found, uses the `$defaultMorphType` (which defaults to the model's FQCN)
4. Delegates to the parent relationship method

### Example Scenario

Suppose your `taggables` table uses fully qualified class names, but your `categoryables` table uses short aliases:

#### taggables

| tag_id | taggable_id | taggable_type        |
|--------|-------------|----------------------|
| 1      | 1           | App\Models\Post      |
| 2      | 1           | App\Models\Video     |

#### categoryables

| category_id | categoryable_id | categoryable_type |
|-------------|-----------------|-------------------|
| 1           | 1               | post              |
| 2           | 1               | video             |

With this package, both table structures work seamlessly on the same model.

## Testing

```bash
composer test
```

## Static Analysis

```bash
composer analyse
```

## Code Style

```bash
# Check style
composer format:check

# Fix style
composer format
```

## Contributing

Contributions are always welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

[Thanks to all of our contributors](https://github.com/moneo/laravel-morphmap/graphs/contributors)!

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
