<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Media;
use App\Models\Blog;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('create-blog', fn (User $user): bool => $user->canCreateBlog());
        Gate::define('edit-blog', fn (User $user): bool => $user->canEditBlog());
        Gate::define('delete-blog', fn (User $user):bool =>$user->canDeleteBlog());

        Gate::define('manage-blog', fn (User $user) => 
            $user->canCreateBlog() || $user->canEditBlog() || $user->canDeleteBlog()
        );


        Gate::define('upload-media', fn(User $user):bool=>$user->canManageMedia());
        
        
        
        Gate::define('own-blog',function(User $user,Blog $blog):bool{
            return $blog->author_id===$user->id || $user->is_superuser;
            });


        Gate::define('edit-media', function (User $user, Media $media): bool {
        return $user->id === $media->user_id || $user->is_superuser; 
        // Note: Replace $user->hasRole('super-user') with whatever check you use for your super admins (e.g., $user->is_admin)
             });

    // DELETE MEDIA: Must be the owner OR have a super-user permission
    Gate::define('delete-media', function (User $user, Media $media): bool {
        return $user->id === $media->user_id || $user->is_superuser;
    });
    }
}
