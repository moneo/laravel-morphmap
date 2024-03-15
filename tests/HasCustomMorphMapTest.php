<?php

namespace Moneo\LaravelMorphMap\Tests;

use Moneo\LaravelMorphMap\Tests\Models\Comment;
use Moneo\LaravelMorphMap\Tests\Models\Post;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Illuminate\Database\Eloquent\Relations\Relation;

class HasCustomMorphMapTest extends OrchestraTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /** @test */
    public function it_registers_custom_morph_map()
    {

        Relation::morphMap(
            [
                'tag' => 'Moneo\LaravelMorphMap\Tests\Models\Tag',
            ]
        );

        $post = new Post();
        $post->morphToMany('Moneo\LaravelMorphMap\Tests\Models\Tag', 'taggable');

        $morphMap = Relation::morphMap();
        $this->assertArrayHasKey('tag', $morphMap);
        $this->assertEquals('Moneo\LaravelMorphMap\Tests\Models\Tag', $morphMap['tag']);
    }

    /** @test */
    public function it_registers_custom_morph_map_across_multiple_models()
    {
        Relation::morphMap(
            [
                'tag'     => 'Moneo\LaravelMorphMap\Tests\Models\Tag',
                'comment' => 'Moneo\LaravelMorphMap\Tests\Models\Comment',
            ]
        );

        $post = new Post();
        $post->morphToMany('Moneo\LaravelMorphMap\Tests\Models\Tag', 'taggable');

        $comment = new Comment();
        $comment->morphToMany('Moneo\LaravelMorphMap\Tests\Models\Comment', 'commentable');

        $morphMap = Relation::morphMap();

        $this->assertEquals('Moneo\LaravelMorphMap\Tests\Models\Tag', $morphMap['tag']);
        $this->assertEquals('Moneo\LaravelMorphMap\Tests\Models\Comment', $morphMap['comment']);
    }

    /** @test */
    public function it_allows_dynamic_changes_to_the_custom_morph_map()
    {
        Relation::morphMap([ 'tag' => 'Moneo\LaravelMorphMap\Tests\Models\Tag' ]);

        $post = new Post();
        $post->morphToMany('Moneo\LaravelMorphMap\Tests\Models\Tag', 'taggable');

        $this->assertEquals('Moneo\LaravelMorphMap\Tests\Models\Tag', Relation::morphMap()['tag']);

        Relation::morphMap([ 'tag' => 'Moneo\LaravelMorphMap\Tests\Models\NewTag' ]);

        $this->assertEquals('Moneo\LaravelMorphMap\Tests\Models\NewTag', Relation::morphMap()['tag']);
    }

    /** @test */
    public function it_handles_invalid_custom_morph_map_values_gracefully()
    {
        Relation::morphMap([ 'tag' => null ]);

        $post = new Post();

        $this->assertNull(Relation::morphMap()['tag']);
    }


}
