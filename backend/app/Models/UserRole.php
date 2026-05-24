<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRole extends Model
{
    // Explicitly define the table name if it differs from conventional plurals
    protected $table = 'user_roles';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'can_create_blog',
        'can_edit_blog',
        'can_delete_blog',
        'can_publish_blog',
        'can_manage_users',
        'can_create_media',
        'can_edit_media',
        'can_delete_media',
        'can_manage_subscribers',
        'can_manage_contacts',
        'can_manage_settings',
    ];

    /**
     * The attributes that should be cast to native types.
     * This ensures MySQL TINYINT(1) fields return as boolean true/false in PHP.
     */
    protected $casts = [
        'can_create_blog'        => 'boolean',
        'can_edit_blog'          => 'boolean',
        'can_delete_blog'        => 'boolean',
        'can_publish_blog'       => 'boolean',
        'can_manage_users'       => 'boolean',
        'can_create_media'       => 'boolean',
        'can_edit_media'         => 'boolean',
        'can_delete_media'       => 'boolean',
        'can_manage_subscribers' => 'boolean',
        'can_manage_contacts'    => 'boolean',
        'can_manage_settings'    => 'boolean',
    ];

    /**
     * Get the user that owns this role profile.
     * Replaces the Django ForeignKey link back to the Custom User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
