<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Language extends Model
{
    protected $fillable = ['name', 'code', 'default'];

    protected $casts = [
        'default' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     * Replaces Django's save method overrides via Eloquent lifecycle hooks.
     */
    public function frontendTranslations(): HasMany
    {
        // Laravel infers the foreign key as language_id automatically
        return $this->hasMany(FrontendTranslation::class);
    }
    protected static function booted()
    {
        // Intercept right before a record is written to MySQL
        static::saving(function (Language $language) {
            
            if ($language->default) {
                // Wrap in a database transaction matching Django's transaction.atomic()
                DB::transaction(function () use ($language) {
                    // Turn off default status for all other entries
                    Language::where('default', true)
                        ->where('id', '!=', $language->id)
                        ->update(['default' => false]);
                });
            } else {
                // Logic check: If this is the absolute first record, force it to be default
                if (Language::count() === 0) {
                    $language->default = true;
                }
            }
        });
    }
}