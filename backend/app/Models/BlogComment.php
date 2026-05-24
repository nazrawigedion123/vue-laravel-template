<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogComment extends Model
{
    protected $fillable = ['reply_to_id', 'blog_id', 'user_id', 'content'];

    public function blog(): BelongsTo {
        return $this->belongsTo(Blog::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    // Parent comment relationship (reply_to)
    public function parent(): BelongsTo {
        return $this->belongsTo(BlogComment::class, 'reply_to_id');
    }

    // Nested replies relationship
    public function replies(): HasMany {
        return $this->hasMany(BlogComment::class, 'reply_to_id');
    }
}
