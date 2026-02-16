<?php

namespace Moneo\LaravelMorphMap\Tests;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Moneo\LaravelMorphMap\Tests\Models\Category;
use Moneo\LaravelMorphMap\Tests\Models\Comment;
use Moneo\LaravelMorphMap\Tests\Models\Post;
use Moneo\LaravelMorphMap\Tests\Models\Tag;
use Moneo\LaravelMorphMap\Tests\Models\Video;
use Orchestra\Testbench\TestCase;

class HasCustomMorphMapTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->resetMorphMap();
        $this->createTables();
    }

    protected function tearDown(): void
    {
        $this->resetMorphMap();

        parent::tearDown();
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    private function resetMorphMap(): void
    {
        Relation::morphMap([], false);
    }

    private function createTables(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string('body');
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('taggables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tag_id');
            $table->unsignedBigInteger('taggable_id');
            $table->string('taggable_type');
            $table->timestamps();
        });

        Schema::create('categoryables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('categoryable_id');
            $table->string('categoryable_type');
            $table->timestamps();
        });
    }

    // -------------------------------------------------------------------------
    // Trait Initialization
    // -------------------------------------------------------------------------

    public function test_initialize_sets_default_morph_type_to_class_name(): void
    {
        $post = new Post;

        $reflection = new \ReflectionProperty($post, 'defaultMorphType');
        $this->assertSame(Post::class, $reflection->getValue($post));
    }

    public function test_initialize_uses_late_static_binding(): void
    {
        $video = new Video;

        $reflection = new \ReflectionProperty($video, 'defaultMorphType');
        $this->assertSame(Video::class, $reflection->getValue($video));
    }

    public function test_custom_morph_map_property_is_set_correctly(): void
    {
        $post = new Post;

        $reflection = new \ReflectionProperty($post, 'customMorphMap');
        $map = $reflection->getValue($post);

        $this->assertArrayHasKey(Category::class, $map);
        $this->assertSame('post', $map[Category::class]);
    }

    // -------------------------------------------------------------------------
    // morphToMany - Custom Morph Type Registration
    // -------------------------------------------------------------------------

    public function test_registers_custom_morph_type_for_related_model(): void
    {
        $post = new Post;
        $post->categories();

        $morphMap = Relation::morphMap();

        $this->assertArrayHasKey('post', $morphMap);
        $this->assertSame(Post::class, $morphMap['post']);
    }

    public function test_uses_default_morph_type_when_no_custom_mapping(): void
    {
        $post = new Post;
        $post->tags();

        $morphMap = Relation::morphMap();

        $this->assertArrayHasKey(Post::class, $morphMap);
        $this->assertSame(Post::class, $morphMap[Post::class]);
    }

    public function test_different_models_register_different_custom_morph_types(): void
    {
        $post = new Post;
        $post->categories();

        $video = new Video;
        $video->categories();

        $morphMap = Relation::morphMap();

        $this->assertSame(Post::class, $morphMap['post']);
        $this->assertSame(Video::class, $morphMap['video']);
    }

    public function test_morph_to_many_returns_morph_to_many_relation(): void
    {
        $post = new Post;
        $relation = $post->tags();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\MorphToMany::class,
            $relation,
        );
    }

    // -------------------------------------------------------------------------
    // morphedByMany - Inverse Relationship Support
    // -------------------------------------------------------------------------

    public function test_morphed_by_many_returns_morph_to_many_relation(): void
    {
        $tag = new Tag;
        $relation = $tag->posts();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\MorphToMany::class,
            $relation,
        );
    }

    public function test_morphed_by_many_registers_morph_map(): void
    {
        $category = new Category;
        $category->posts();

        $morphMap = Relation::morphMap();

        $this->assertArrayHasKey(Category::class, $morphMap);
        $this->assertSame(Category::class, $morphMap[Category::class]);
    }

    // -------------------------------------------------------------------------
    // Multiple Relationships on Same Model
    // -------------------------------------------------------------------------

    public function test_model_with_multiple_relationships_registers_correct_maps(): void
    {
        $post = new Post;

        $post->tags();
        $post->categories();

        $morphMap = Relation::morphMap();

        $this->assertArrayHasKey('post', $morphMap);
        $this->assertSame(Post::class, $morphMap['post']);
    }

    // -------------------------------------------------------------------------
    // Database Integration Tests
    // -------------------------------------------------------------------------

    public function test_custom_morph_type_is_stored_in_pivot_table(): void
    {
        $post = Post::create(['title' => 'Test Post']);
        $category = Category::create(['name' => 'Articles']);

        $post->categories()->attach($category);

        $this->assertDatabaseHas('categoryables', [
            'category_id' => $category->id,
            'categoryable_id' => $post->id,
            'categoryable_type' => 'post',
        ]);
    }

    public function test_default_morph_type_is_stored_in_pivot_table(): void
    {
        $post = Post::create(['title' => 'Test Post']);
        $tag = Tag::create(['name' => 'PHP']);

        $post->tags()->attach($tag);

        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tag->id,
            'taggable_id' => $post->id,
            'taggable_type' => Post::class,
        ]);
    }

    public function test_different_models_store_different_morph_types_for_same_relation(): void
    {
        $post = Post::create(['title' => 'Test Post']);
        $video = Video::create(['title' => 'Test Video']);
        $category = Category::create(['name' => 'Tutorials']);

        $post->categories()->attach($category);
        $video->categories()->attach($category);

        $this->assertDatabaseHas('categoryables', [
            'categoryable_id' => $post->id,
            'categoryable_type' => 'post',
        ]);

        $this->assertDatabaseHas('categoryables', [
            'categoryable_id' => $video->id,
            'categoryable_type' => 'video',
        ]);
    }

    public function test_retrieve_related_models_through_custom_morph_map(): void
    {
        $post = Post::create(['title' => 'Test Post']);
        $category1 = Category::create(['name' => 'Articles']);
        $category2 = Category::create(['name' => 'Tutorials']);

        $post->categories()->attach([$category1->id, $category2->id]);

        $freshPost = Post::find($post->id);
        $categories = $freshPost->categories;

        $this->assertCount(2, $categories);
        $this->assertTrue($categories->contains('name', 'Articles'));
        $this->assertTrue($categories->contains('name', 'Tutorials'));
    }

    public function test_inverse_relationship_retrieves_through_custom_morph_map(): void
    {
        $post = Post::create(['title' => 'Test Post']);
        $video = Video::create(['title' => 'Test Video']);
        $category = Category::create(['name' => 'Tutorials']);

        $post->categories()->attach($category);
        $video->categories()->attach($category);

        $freshCategory = Category::find($category->id);
        $posts = $freshCategory->posts;
        $videos = $freshCategory->videos;

        $this->assertCount(1, $posts);
        $this->assertSame('Test Post', $posts->first()->title);
        $this->assertCount(1, $videos);
        $this->assertSame('Test Video', $videos->first()->title);
    }

    // -------------------------------------------------------------------------
    // Edge Cases
    // -------------------------------------------------------------------------

    public function test_empty_custom_morph_map_uses_default_for_all(): void
    {
        $comment = new Comment;
        $comment->tags();

        $morphMap = Relation::morphMap();

        $this->assertArrayHasKey(Comment::class, $morphMap);
        $this->assertSame(Comment::class, $morphMap[Comment::class]);
    }

    public function test_morph_map_does_not_overwrite_unrelated_entries(): void
    {
        Relation::morphMap(['existing_alias' => 'App\\Models\\SomeModel']);

        $post = new Post;
        $post->categories();

        $morphMap = Relation::morphMap();

        $this->assertSame('App\\Models\\SomeModel', $morphMap['existing_alias']);
        $this->assertSame(Post::class, $morphMap['post']);
    }

    public function test_static_class_is_used_not_trait_class(): void
    {
        $post = new Post;
        $post->categories();

        $morphMap = Relation::morphMap();

        $this->assertSame(Post::class, $morphMap['post']);
        $this->assertStringContainsString('Post', $morphMap['post']);
        $this->assertStringNotContainsString('HasCustomMorphMap', $morphMap['post']);
    }
}
