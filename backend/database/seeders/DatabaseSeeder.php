<?php
// backend/database/seeders/DatabaseSeeder.php
namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'password' => Hash::make('123'),
                'is_superuser' => true,
                'is_staff' => true,
            ]
        );

        $this->call([
        LanguageSeeder::class,
        // Add other seeders here as you build them
        ]);

        
    }
}
