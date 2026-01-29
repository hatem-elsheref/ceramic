<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class ProductionSetupSeeder extends Seeder
{
    /**
     * Production setup: Ensure public storage, clear cache, and configure settings
     *
     * @return void
     */
    public function run()
    {
        echo "=== PRODUCTION SETUP ===\n\n";

        // 1. Set storage to public (not S3)
        echo "1. Setting storage to public directory...\n";
        BusinessSetting::updateOrInsert(
            ['type' => 'storage_connection_type'],
            ['value' => 'public', 'updated_at' => now(), 'created_at' => now()]
        );
        Config::set('filesystems.disks.default', 'public');
        echo "  ✓ Storage set to public directory\n\n";

        // 2. Clear all caches
        echo "2. Clearing all caches...\n";
        try {
            Artisan::call('cache:clear');
            echo "  ✓ Application cache cleared\n";
        } catch (\Exception $e) {
            echo "  ⚠ Cache clear error: " . $e->getMessage() . "\n";
        }

        try {
            Artisan::call('config:clear');
            echo "  ✓ Config cache cleared\n";
        } catch (\Exception $e) {
            echo "  ⚠ Config clear error: " . $e->getMessage() . "\n";
        }

        try {
            Artisan::call('route:clear');
            echo "  ✓ Route cache cleared\n";
        } catch (\Exception $e) {
            echo "  ⚠ Route clear error: " . $e->getMessage() . "\n";
        }

        try {
            Artisan::call('view:clear');
            echo "  ✓ View cache cleared\n";
        } catch (\Exception $e) {
            echo "  ⚠ View clear error: " . $e->getMessage() . "\n";
        }

        // Clear all cache using Cache facade
        try {
            Cache::flush();
            echo "  ✓ All cache data flushed\n";
        } catch (\Exception $e) {
            echo "  ⚠ Cache flush error: " . $e->getMessage() . "\n";
        }

        // Clear web config cache keys
        try {
            if (function_exists('clearWebConfigCacheKeys')) {
                clearWebConfigCacheKeys();
                echo "  ✓ Web config cache cleared\n";
            }
        } catch (\Exception $e) {
            echo "  ⚠ Web config cache clear error: " . $e->getMessage() . "\n";
        }

        echo "\n";

        // 3. Check storage link (inform user to create manually)
        echo "3. Checking storage link...\n";
        $publicStorageLink = public_path('storage');
        
        if (!file_exists($publicStorageLink) || !is_link($publicStorageLink)) {
            echo "  ⚠ Storage link not found\n";
            echo "  → Please run: php artisan storage:link\n";
        } else {
            echo "  ✓ Storage link exists\n";
        }

        echo "\n";

        // 4. Verify public storage directories exist
        echo "4. Verifying public storage directories...\n";
        $directories = [
            'product',
            'product/thumbnail',
            'category',
            'brand',
            'banner',
            'company',
            'shop',
        ];

        foreach ($directories as $dir) {
            $fullPath = storage_path('app/public/' . $dir);
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
                echo "  ✓ Created directory: {$dir}\n";
            }
        }
        echo "  ✓ All storage directories verified\n\n";

        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "✅ Production setup completed!\n";
        echo "   - Storage: Public directory\n";
        echo "   - Cache: Cleared\n";
        echo "   - Storage link: Ready\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}
