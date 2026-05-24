<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FrontendTranslation extends Model
{
    // Mass-assignable fields
    protected $fillable = ['language_id', 'page', 'key', 'value'];

    /**
     * Get the language that owns the translation.
     * Inverse of Django's ForeignKey
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}