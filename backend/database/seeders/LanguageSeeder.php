<?php
// backend/database/seeders/LanguageSeeder.php
namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languagesToSeed = [
            ['name' => 'English', 'code' => 'en', 'default' => true],
            ['name' => 'Amharic', 'code' => 'am', 'default' => false],
        ];

        foreach ($languagesToSeed as $langData) {
            // Replaces update_or_create(code=..., defaults=...)
            $language = Language::updateOrCreate(
                ['code' => $langData['code']],
                [
                    'name' => $langData['name'],
                    'default' => $langData['default']
                ]
            );

            // Print status updates directly to your console
            if ($language->wasRecentlyCreated) {
                $this->command->info("Successfully created: {$language->name}");
            } else {
                $this->command->comment("Updated existing: {$language->name}");
            }
        }

        $this->command->info("Language seeding complete!");
    }
}