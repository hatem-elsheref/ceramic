<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class UpdateFeaturedProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Updates existing products to mark more as featured
     *
     * @return void
     */
    public function run()
    {
        echo "Updating featured products...\n\n";

        // Get all active products
        $products = Product::where('status', 1)
            ->orderBy('id', 'asc')
            ->get();

        if ($products->isEmpty()) {
            echo "No products found to update.\n";
            return;
        }

        // Mark first 10 products as featured
        $featuredCount = min(10, $products->count());
        $updated = 0;

        foreach ($products->take($featuredCount) as $product) {
            if ($product->featured != 1 || $product->featured_status != 1) {
                $product->update([
                    'featured' => 1,
                    'featured_status' => 1,
                ]);
                $updated++;
                echo "✓ Marked as featured: {$product->code} - {$product->name}\n";
            }
        }

        // Unmark remaining products as not featured (optional - comment out if you want to keep existing featured status)
        foreach ($products->skip($featuredCount) as $product) {
            if ($product->featured == 1) {
                $product->update([
                    'featured' => 0,
                    'featured_status' => 0,
                ]);
            }
        }

        echo "\n✅ Featured products updated: $updated products marked as featured\n";
        echo "   Total featured products: " . Product::where('featured', 1)->count() . "\n\n";
    }
}
