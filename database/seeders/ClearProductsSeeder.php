<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductSeo;
use App\Models\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClearProductsSeeder extends Seeder
{
    /**
     * Clear all existing products and related data
     *
     * @return void
     */
    public function run()
    {
        echo "Clearing all products and related data...\n";
        
        // Delete product SEO data
        ProductSeo::truncate();
        echo "✓ Cleared product SEO data\n";
        
        // Delete product translations
        DB::table('translations')
            ->where('translationable_type', 'App\Models\Product')
            ->delete();
        echo "✓ Cleared product translations\n";
        
        // Delete products
        Product::truncate();
        echo "✓ Cleared all products\n";
        
        echo "\n✅ All products cleared successfully!\n";
        echo "You can now run a fresh seeder.\n";
    }
}
