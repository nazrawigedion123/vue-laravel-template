<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogReaction extends Model
{
    // Custom table name since we named our model BlogReaction instead of Reaction
    protected $table = 'blog_reactions'; 

    protected $fillable = ['user_id', 'blog_id', 'reaction_type'];

    public function blog(): BelongsTo {
        return $this->belongsTo(Blog::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}