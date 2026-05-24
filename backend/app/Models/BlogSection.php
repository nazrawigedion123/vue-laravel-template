<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogSection extends Model
{
    protected $fillable = ['blog_id', 'order', 'image'];

    public function blog(): BelongsTo {
        return $this->belongsTo(Blog::class);
    }

    public function translations(): HasMany {
        return $this->hasMany(BlogSectionTranslation::class, 'blog_section_id');
    }

    public function getTranslation(?string $langCode = null)
    {
        if ($this->relationLoaded('translations')) {
            if ($langCode) {
                $match = $this->translations->first(fn($t) => $t->language->code === $langCode);
                if ($match) return $match;
            }
            $default = $this->translations->first(fn($t) => $t->language->default == true);
            return $default ?? $this->translations->first();
        }

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
        return $t ? ($t->title ?? '') : '';
    }

    public function getContent(?string $langCode = null): string {
        $t = $this->getTranslation($langCode);
        return $t ? ($t->content ?? '') : '';
    }
}