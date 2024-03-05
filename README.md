![](https://banners.beyondco.de/Custom%20Morph%20Map.png?theme=light&packageManager=composer+require&packageName=mcucen%2Flaravel-morphmap&pattern=anchorsAway&style=style_1&description=Custom+morphMap+for+each+relation&md=1&showWatermark=0&fontSize=100px&images=link)

# Laravel Custom Morph Map

This package provides use different `morphMap` for different relationships.

If your polymorphic relation structures does not satisfy with each other, this package lets you use custom map for each
relation definition.

PS: By default, Laravel does not support custom mapping for each relation.

## Installation

    composer require mcucen/laravel-morphmap

## Usage
```php
class Post extends Model
{
    // 🚀 Add HasCustomMorphMap trait!
    use HasCustomMorphMap;

    public function __construct(array $attributes = [])
    {
        // 👋 Custom definition for Category relation!    
        $this->customMorphMap = [
            Category::class => 'post',
        ];
        
        // 👋 Default for all others! (__CLASS__ definition is default. You don't need to add this.)
        $this->defaultMorphType = Post::class;

        parent::__construct($attributes);
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
```

This usage example covers the example data below.

### Example Case:

Assume you use these tables in your project.

```
posts
    id - integer
    name - string
 
videos
    id - integer
    name - string
 
tags
    id - integer
    name - string
    
categories
    id - integer
    name - string
 
taggables
    tag_id - integer
    taggable_id - integer
    taggable_type - string
    
categoryables
    category_id - integer
    categoryable_id - integer
    categoryable_type - string
```

#### Example Data:

#### posts

| id | name                          |
|----|-------------------------------|
| 1  | Easy Seralization in Doctrine |

#### videos

| id | name                                   |
|----|----------------------------------------|
| 1  | Beyond Controllers in Laravel Projects |

#### tags

| id | name     |
|----|----------|
| 1  | PHP      |
| 2  | Laravel  |
| 3  | Doctrine |

#### categories

| id | name            |
|----|-----------------|
| 1  | Articles        |
| 2  | Video Tutorials |

#### taggables

| tag_id | taggable_id | taggable_type    |
|--------|-------------|------------------|
| 1      | 1           | App\Models\Post  |
| 3      | 1           | App\Models\Post  |
| 1      | 1           | App\Models\Video |
| 2      | 1           | App\Models\Video |

#### categoryables

| category_id | categoryable_id | categoryable_type |
|-------------|-----------------|-------------------|
| 1           | 1               | post              |
| 3           | 1               | post              |
| 1           | 1               | video             |
| 2           | 1               | video             |

## Contributing
Contributions are always welcome, [thanks to all of our contributors](https://github.com/mcucen/laravel-morphmap/graphs/contributors)!
