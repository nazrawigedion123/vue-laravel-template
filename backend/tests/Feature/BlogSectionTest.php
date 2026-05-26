<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_section_to_blog()
    {
        Storage::fake('public');

        $user = User::factory()->create(['is_superuser' => true]);
        $language = Language::create(['name' => 'English', 'code' => 'en', 'default' => true]);
        
        $blog = Blog::create([
            'author_id' => $user->id,
            'comment_count' => 0,
            'reaction_count' => 0,
        ]);

        $response = $this->actingAs($user, 'api')->postJson("/api/blogs/{$blog->id}/sections", [
            'order' => 1,
            'image' => UploadedFile::fake()->image('section.jpg'),
            'translations' => [
                [
                    'language_id' => $language->id,
                    'title' => 'Section Title',
                    'content' => 'Section Content',
                ]
            ]
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('blog_sections', [
            'blog_id' => $blog->id,
            'order' => 1,
        ]);
        $this->assertDatabaseHas('blog_section_translations', [
            'title' => 'Section Title',
            'content' => 'Section Content',
        ]);
    }

    public function test_can_delete_comment()
    {
        $user = User::factory()->create();
        $language = Language::create(['name' => 'English', 'code' => 'en', 'default' => true]);
        
        $blog = Blog::create([
            'author_id' => $user->id,
            'comment_count' => 1,
            'reaction_count' => 0,
        ]);

        $comment = $blog->comments()->create([
            'user_id' => $user->id,
            'content' => 'Test Comment',
        ]);

        $response = $this->actingAs($user, 'api')->deleteJson("/api/comments/{$comment->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('blog_comments', ['id' => $comment->id]);
        $this->assertEquals(0, $blog->fresh()->comment_count);
    }
}
