<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Blog extends Model
{
    protected $fillable = ['author_id', 'published_by_id', 'published_at', 'comment_count', 'reaction_count'];

    // --- Relationships ---
    public function author(): BelongsTo {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function publisher(): BelongsTo {
        return $this->belongsTo(User::class, 'published_by_id');
    }

    public function translations(): HasMany {
        return $this->hasMany(BlogTranslation::class, 'blog_id');
    }

    public function sections(): HasMany {
        return $this->hasMany(BlogSection::class, 'blog_id')->orderBy('order')->orderBy('id');
    }

    public function comments(): HasMany {
        return $this->hasMany(BlogComment::class, 'blog_id');
    }

    public function reactions(): HasMany {
        return $this->hasMany(BlogReaction::class, 'blog_id');
    }

    // --- Translation Helper Logic ---
    public function getTranslation(?string $langCode = null)
    {
        // If the relation is already eager-loaded (like Django's prefetch_related via getattr check),
        // we filter the collection in memory to prevent unnecessary database hits.
        if ($this->relationLoaded('translations')) {
            if ($langCode) {
                $match = $this->translations->first(fn($t) => $t->language->code === $langCode);
                if ($match) return $match;
            }
            // Fallback to default language
            $default = $this->translations->first(fn($t) => $t->language->default == true);
            return $default ?? $this->translations->first();
        }

        // Database query fallback if not eager-loaded
        if ($langCode) {
            $translation = $this->translations()->whereHas('language', function($q) use ($langCode) {
                $q->where('code', $langCode);
            })->first();
            if ($translation) return $translation;
        }

        $defaultTranslation = $this->translations()->whereHas('language', function($q) {
            $q->where('default', true);
        })->first();

        return $defaultTranslation ?? $this->translations()->first();
    }

    public function getTitle(?string $langCode = null): string {
        $t = $this->getTranslation($langCode);
        return $t ? ($t->title ?? '(untitled)') : '(untitled)';
    }

    public function getContent(?string $langCode = null): string {
        $t = $this->getTranslation($langCode);
        return $t ? ($t->content ?? '') : '';
    }
}
