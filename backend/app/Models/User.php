<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = ['email', 'password', 'first_name','last_name','is_active', 'is_superuser', 'is_staff'];
    protected $hidden = ['password'];
    

    // --- JWT Required Methods ---
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [
            'email' => $this->email,
            'is_superuser' => $this->is_superuser
        ];
    }

    // --- Relationships ---
   public function role(): HasOne {
    return $this->hasOne(UserRole::class, 'user_id')->withDefault([
        'can_create_blog' => false,
        'can_edit_blog'   => false,
        // all other permissions default to false
    ]);
}

    // --- Permission Layer Adaptations ---
    public function canCreateBlog(): bool {
        if ($this->is_superuser) return true;
        return $this->role?->can_create_blog ?? false;
    }

    public function canEditBlog(): bool {
        if ($this->is_superuser) return true;
        return $this->role?->can_edit_blog ?? false;
    }

    public function canPublishBlog(): bool {
        if ($this->is_superuser) return true;
        return $this->role?->can_publish_blog ?? false;
    }

    public function canManageMedia(): bool {
        if ($this->is_superuser) return true;
        $r = $this->role;
        return $r ? ($r->can_create_media || $r->can_edit_media || $r->can_delete_media) : false;
    }
}