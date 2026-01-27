<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearAllDataSeeder extends Seeder
{
    /**
     * Clear all data from all tables in the database
     *
     * WARNING: This will delete ALL data from ALL tables!
     * Use with caution in production.
     *
     * @return void
     */
    public function run()
    {
        echo "⚠️  WARNING: This will delete ALL data from ALL tables!\n";
        echo "Starting database cleanup...\n\n";

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Get all table names from the database
            $tables = DB::select('SHOW TABLES');
            $databaseName = DB::getDatabaseName();
            $tableKey = 'Tables_in_' . $databaseName;

            $clearedCount = 0;
            $errorCount = 0;

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;

                // Skip migrations table (optional - uncomment if you want to keep migration history)
                 if ($tableName === 'migrations') {
                     continue;
                 }

                try {
                    // Truncate the table
                    DB::table($tableName)->truncate();
                    echo "✓ Cleared table: {$tableName}\n";
                    $clearedCount++;
                } catch (\Exception $e) {
                    echo "✗ Error clearing table {$tableName}: " . $e->getMessage() . "\n";
                    $errorCount++;
                }
            }

            echo "\n";
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "✅ Database cleanup completed!\n";
            echo "   Tables cleared: {$clearedCount}\n";
            if ($errorCount > 0) {
                echo "   Errors: {$errorCount}\n";
            }
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "\nYou can now run a fresh seeder:\n";
            echo "  php artisan db:seed\n";
            echo "  or\n";
            echo "  php artisan migrate:fresh --seed\n";

        } catch (\Exception $e) {
            echo "\n❌ Fatal error during cleanup: " . $e->getMessage() . "\n";
        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
