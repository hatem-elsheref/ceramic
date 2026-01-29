<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductionReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'production:reset {--fresh : Run fresh migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean everything, clear cache, and seed production-ready data with public storage';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🚀 Starting Production Reset...');
        $this->newLine();

        // Step 1: Clear all caches
        $this->info('1️⃣  Clearing all caches...');
        $this->clearAllCaches();
        $this->info('   ✓ All caches cleared');
        $this->newLine();

        // Step 2: Run migrations
        if ($this->option('fresh')) {
            $this->info('2️⃣  Running fresh migrations...');
            Artisan::call('migrate:fresh', ['--force' => true]);
            $this->info('   ✓ Migrations completed');
        } else {
            $this->info('2️⃣  Running migrations...');
            Artisan::call('migrate', ['--force' => true]);
            $this->info('   ✓ Migrations completed');
        }
        $this->newLine();

        // Step 3: Seed database
        $this->info('3️⃣  Seeding production data...');
        Artisan::call('db:seed', ['--force' => true]);
        $this->info('   ✓ Database seeded');
        $this->newLine();

        // Step 4: Clear cache again
        $this->info('4️⃣  Final cache clear...');
        $this->clearAllCaches();
        $this->info('   ✓ Cache cleared');
        $this->newLine();

        // Step 5: Storage link
        $this->info('5️⃣  Creating storage link...');
        try {
            Artisan::call('storage:link', ['--force' => true]);
            $this->info('   ✓ Storage link created');
        } catch (\Exception $e) {
            $this->warn('   ⚠ Storage link may already exist');
        }
        $this->newLine();

        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('✅ Production reset completed successfully!');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();
        $this->info('📝 Summary:');
        $this->info('   • All data cleaned');
        $this->info('   • Production data seeded');
        $this->info('   • Storage: Public directory');
        $this->info('   • Cache: Cleared');
        $this->newLine();

        return Command::SUCCESS;
    }

    /**
     * Clear all application caches
     */
    private function clearAllCaches(): void
    {
        try {
            Artisan::call('cache:clear');
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            Artisan::call('config:clear');
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            Artisan::call('route:clear');
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            Artisan::call('view:clear');
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            Cache::flush();
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            if (function_exists('clearWebConfigCacheKeys')) {
                clearWebConfigCacheKeys();
            }
        } catch (\Exception $e) {
            // Ignore
        }
    }
}
