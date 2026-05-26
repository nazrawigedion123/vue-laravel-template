<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogSectionTranslation extends Model
{
    protected $fillable = [
        'blog_section_id',
        'language_id',
        'title',
        'content',
    ];

    public function blogSection(): BelongsTo
    {
        return $this->belongsTo(BlogSection::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
