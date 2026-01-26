<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeo;
use App\Models\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class BrandCategoryProductsSeeder extends Seeder
{
    private $faker;
    private $availableImages = [];
    private $usedImageIndex = 0;
    private $productCounter = 1;

    /**
     * Run the database seeds.
     * Creates 10-15 products for each brand and each category
     *
     * @return void
     */
    public function run()
    {
        echo "Starting Brand & Category Products Seeder...\n\n";
        
        $this->faker = Faker::create();
        $this->availableImages = $this->getAvailableImages();
        
        echo "Found " . count($this->availableImages) . " images available\n\n";
        
        // Get all brands and categories
        $brands = Brand::where('status', 1)->get();
        $categories = Category::all();
        $mainCategories = Category::where('parent_id', 0)->get();
        $subCategories = Category::where('parent_id', '>', 0)->get();
        
        echo "Brands: " . $brands->count() . "\n";
        echo "Categories: " . $categories->count() . " (Main: " . $mainCategories->count() . ", Sub: " . $subCategories->count() . ")\n\n";
        
        // Create products for each brand (10-15 per brand)
        $this->createProductsForBrands($brands, $categories, $subCategories);
        
        // Ensure each category has at least 10-15 products
        $this->ensureCategoryProductCount($categories, $brands, $subCategories);
        
        echo "\n✅ Brand & Category Products Seeder completed!\n";
        echo "Total products created: " . ($this->productCounter - 1) . "\n";
    }

    private function createProductsForBrands($brands, $categories, $subCategories)
    {
        echo "Creating products for brands...\n";
        
        $totalCreated = 0;
        
        foreach ($brands as $brandIndex => $brand) {
            $productsPerBrand = rand(10, 15);
            echo "\nBrand: {$brand->name} - Creating {$productsPerBrand} products...\n";
            
            $created = 0;
            for ($i = 0; $i < $productsPerBrand; $i++) {
                // Distribute categories evenly
                $categoryIndex = ($brandIndex * $productsPerBrand + $i) % $categories->count();
                $category = $categories[$categoryIndex];
                
                // Prefer sub-categories if available, otherwise use main category
                $subCategory = null;
                if ($subCategories->isNotEmpty()) {
                    $subCategoryIndex = ($brandIndex * $productsPerBrand + $i) % $subCategories->count();
                    $subCategory = $subCategories[$subCategoryIndex];
                    // Make sure sub-category belongs to the main category
                    if ($subCategory->parent_id != $category->id) {
                        $subCategory = $subCategories->where('parent_id', $category->id)->first() ?? null;
                    }
                }
                
                $product = $this->createProduct($brand, $category, $subCategory);
                
                if ($product) {
                    $created++;
                    $totalCreated++;
                }
            }
            
            echo "  ✓ Created {$created} products for {$brand->name}\n";
        }
        
        echo "\n✓ Total products created for brands: {$totalCreated}\n";
    }

    private function ensureCategoryProductCount($categories, $brands, $subCategories)
    {
        echo "\nEnsuring each category has 10-15 products...\n";
        
        $totalAdded = 0;
        
        foreach ($categories as $category) {
            $currentCount = Product::where('category_id', $category->id)->count();
            $targetCount = rand(10, 15);
            
            if ($currentCount < $targetCount) {
                $needed = $targetCount - $currentCount;
                echo "Category: {$category->name} - Has {$currentCount}, needs {$needed} more...\n";
                
                for ($i = 0; $i < $needed; $i++) {
                    // Assign to a random brand
                    $brand = $brands->random();
                    
                    // Get sub-category if available
                    $subCategory = null;
                    if ($subCategories->isNotEmpty()) {
                        $subCategory = $subCategories->where('parent_id', $category->id)->first() ?? null;
                    }
                    
                    $product = $this->createProduct($brand, $category, $subCategory);
                    
                    if ($product) {
                        $totalAdded++;
                    }
                }
                
                echo "  ✓ Added {$needed} products to {$category->name}\n";
            }
        }
        
        echo "\n✓ Total additional products added: {$totalAdded}\n";
    }

    private function createProduct($brand, $category, $subCategory = null)
    {
        try {
            // Generate product code
            $code = 'PCT-' . str_pad($this->productCounter++, 4, '0', STR_PAD_LEFT);
            
            // Check if product with this code already exists
            if (Product::where('code', $code)->exists()) {
                $code = 'PCT-' . str_pad($this->productCounter++, 4, '0', STR_PAD_LEFT) . '-' . Str::random(3);
            }
            
            // Generate product names and details
            $productData = $this->generateProductData($category, $brand);
            
            // Get image
            $imageName = $this->getNextImage();
            $thumbnailName = $this->createThumbnail($imageName, $code);
            
            $images = json_encode([
                ['image_name' => $imageName, 'storage' => 'public'],
                ['image_name' => $imageName, 'storage' => 'public'],
            ]);
            
            // Generate pricing
            $unitPrice = $this->faker->randomFloat(2, 25, 200);
            $purchasePrice = $unitPrice * 0.65; // 35% margin
            $discount = $this->faker->randomFloat(2, 5, 25);
            $stock = $this->faker->numberBetween(100, 1000);
            
            $product = Product::create([
                'added_by' => 'admin',
                'user_id' => 1,
                'name' => $productData['en_name'],
                'code' => $code,
                'slug' => Str::slug($productData['en_name']) . '-' . Str::random(6),
                'category_ids' => (string)$category->id,
                'category_id' => $category->id,
                'sub_category_id' => $subCategory ? $subCategory->id : null,
                'sub_sub_category_id' => null,
                'brand_id' => $brand->id,
                'unit' => 'piece',
                'product_type' => 'physical',
                'digital_product_type' => null,
                'details' => $productData['en_details'],
                'colors' => json_encode([]),
                'choice_options' => json_encode([]),
                'variation' => json_encode([]),
                'unit_price' => $unitPrice,
                'purchase_price' => $purchasePrice,
                'tax' => 15.00,
                'tax_type' => 'percent',
                'tax_model' => 'exclude',
                'discount' => $discount,
                'discount_type' => 'percent',
                'current_stock' => $stock,
                'minimum_order_qty' => 1,
                'min_qty' => 1,
                'status' => 1,
                'request_status' => 1,
                'featured_status' => 0,
                'featured' => 0,
                'published' => 1,
                'refundable' => 1,
                'free_shipping' => 0,
                'shipping_cost' => 25.00,
                'multiply_qty' => 0,
                'images' => $images,
                'thumbnail' => $thumbnailName,
                'thumbnail_storage_type' => 'public',
                'color_image' => json_encode([]),
                'meta_title' => $productData['en_name'] . ' - Premium Ceramic Tiles Saudi Arabia',
                'meta_description' => $productData['en_details'],
                'meta_image' => $thumbnailName,
                'video_provider' => null,
                'video_url' => null,
                'attributes' => json_encode([]),
            ]);
            
            // Add Arabic translations
            Translation::create([
                'translationable_type' => 'App\Models\Product',
                'translationable_id' => $product->id,
                'locale' => 'sa',
                'key' => 'name',
                'value' => $productData['ar_name'],
            ]);
            
            Translation::create([
                'translationable_type' => 'App\Models\Product',
                'translationable_id' => $product->id,
                'locale' => 'sa',
                'key' => 'description',
                'value' => $productData['ar_details'],
            ]);
            
            // Create SEO data
            ProductSeo::create([
                'product_id' => $product->id,
                'title' => $productData['en_name'] . ' | Premium Ceramic Tiles Saudi Arabia',
                'description' => $productData['en_details'] . ' Buy online with free shipping across Saudi Arabia.',
                'index' => 'index',
                'no_follow' => 'follow',
                'no_image_index' => 'index',
                'no_archive' => 'archive',
                'no_snippet' => 'snippet',
                'max_snippet' => null,
                'max_snippet_value' => null,
                'max_video_preview' => null,
                'max_video_preview_value' => null,
                'max_image_preview' => null,
                'max_image_preview_value' => null,
                'image' => $thumbnailName,
            ]);
            
            return $product;
            
        } catch (\Exception $e) {
            echo "  ✗ Error creating product: " . $e->getMessage() . "\n";
            return null;
        }
    }

    private function generateProductData($category, $brand)
    {
        $categoryNameEn = $category->name;
        $categoryNameAr = $category->translations->where('key', 'name')->where('locale', 'sa')->first()->value ?? $categoryNameEn;
        $brandNameEn = $brand->name;
        $brandNameAr = $brand->translations->where('key', 'name')->where('locale', 'sa')->first()->value ?? $brandNameEn;
        
        // Product name templates
        $nameTemplates = [
            'en' => [
                'Premium {category} {brand} {size}',
                'Elegant {category} {brand} {style}',
                'Modern {category} {brand} {color}',
                'Luxury {category} {brand} {pattern}',
                'Classic {category} {brand} {finish}',
                'Contemporary {category} {brand} {design}',
                'Stylish {category} {brand} {texture}',
            ],
            'ar' => [
                '{category} {brand} ممتاز {size}',
                '{category} {brand} أنيق {style}',
                '{category} {brand} عصري {color}',
                '{category} {brand} فاخر {pattern}',
                '{category} {brand} كلاسيكي {finish}',
                '{category} {brand} معاصر {design}',
                '{category} {brand} أنيق {texture}',
            ],
        ];
        
        $sizes = ['30x30', '40x40', '50x50', '60x60', '80x80', '60x120', '120x120'];
        $styles = ['Design', 'Pattern', 'Collection', 'Series', 'Edition'];
        $colors = ['White', 'Beige', 'Gray', 'Black', 'Brown', 'Cream'];
        $patterns = ['Marble', 'Wood', 'Stone', 'Geometric', 'Floral', 'Abstract'];
        $finishes = ['Glossy', 'Matte', 'Polished', 'Textured', 'Smooth'];
        $designs = ['Modern', 'Traditional', 'Minimalist', 'Ornate', 'Simple'];
        $textures = ['Smooth', 'Rough', 'Embossed', 'Relief', '3D'];
        
        $size = $sizes[array_rand($sizes)];
        $style = $styles[array_rand($styles)];
        $color = $colors[array_rand($colors)];
        $pattern = $patterns[array_rand($patterns)];
        $finish = $finishes[array_rand($finishes)];
        $design = $designs[array_rand($designs)];
        $texture = $textures[array_rand($textures)];
        
        // Arabic translations
        $sizeAr = ['30x30', '40x40', '50x50', '60x60', '80x80', '60x120', '120x120'];
        $styleAr = ['تصميم', 'نمط', 'مجموعة', 'سلسلة', 'إصدار'];
        $colorAr = ['أبيض', 'بيج', 'رمادي', 'أسود', 'بني', 'كريمي'];
        $patternAr = ['رخامي', 'خشبي', 'حجري', 'هندسي', 'زهري', 'مجرد'];
        $finishAr = ['لامع', 'مطفي', 'مصقول', 'منسوج', 'ناعم'];
        $designAr = ['عصري', 'تقليدي', 'بسيط', 'منقوش', 'بسيط'];
        $textureAr = ['ناعم', 'خشن', 'بارز', 'نقش', 'ثلاثي الأبعاد'];
        
        $templateEn = $nameTemplates['en'][array_rand($nameTemplates['en'])];
        $templateAr = $nameTemplates['ar'][array_rand($nameTemplates['ar'])];
        
        $enName = str_replace(
            ['{category}', '{brand}', '{size}', '{style}', '{color}', '{pattern}', '{finish}', '{design}', '{texture}'],
            [$categoryNameEn, $brandNameEn, $size, $style, $color, $pattern, $finish, $design, $texture],
            $templateEn
        );
        
        $arName = str_replace(
            ['{category}', '{brand}', '{size}', '{style}', '{color}', '{pattern}', '{finish}', '{design}', '{texture}'],
            [$categoryNameAr, $brandNameAr, $size, $sizeAr[array_rand($sizeAr)], $colorAr[array_rand($colorAr)], $patternAr[array_rand($patternAr)], $finishAr[array_rand($finishAr)], $designAr[array_rand($designAr)], $textureAr[array_rand($textureAr)]],
            $templateAr
        );
        
        // Generate descriptions
        $enDetails = "High-quality {$categoryNameEn} from {$brandNameEn}. Perfect for modern interiors. Available in {$size} size. Durable, easy to clean, and suitable for residential and commercial projects. Made with premium materials for long-lasting beauty.";
        
        $arDetails = "{$categoryNameAr} عالي الجودة من {$brandNameAr}. مثالي للديكورات الداخلية العصرية. متاح بحجم {$size}. متين وسهل التنظيف ومناسب للمشاريع السكنية والتجارية. مصنوع من مواد ممتازة لجمال دائم.";
        
        return [
            'en_name' => $enName,
            'ar_name' => $arName,
            'en_details' => $enDetails,
            'ar_details' => $arDetails,
        ];
    }

    private function getAvailableImages(): array
    {
        $imagePath = storage_path('app/public/product');
        $images = [];
        
        if (is_dir($imagePath)) {
            $files = scandir($imagePath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && !is_dir($imagePath . '/' . $file)) {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['webp', 'jpg', 'jpeg', 'png']) && $file != 'def.webp' && $file != 'def.png') {
                        $images[] = $file;
                    }
                }
            }
        }
        
        // If no images found, return default
        if (empty($images)) {
            return ['def.webp'];
        }
        
        return $images;
    }

    private function getNextImage(): string
    {
        if (empty($this->availableImages)) {
            return 'def.webp';
        }
        
        $image = $this->availableImages[$this->usedImageIndex % count($this->availableImages)];
        $this->usedImageIndex++;
        
        return $image;
    }

    private function createThumbnail(string $imageName, string $code): string
    {
        $thumbName = strtolower($code) . '-thumb.webp';
        $imagePath = storage_path('app/public/product/' . $imageName);
        $thumbPath = storage_path('app/public/product/thumbnail/' . $thumbName);
        
        // Check if thumbnail already exists
        if (Storage::disk('public')->exists('product/thumbnail/' . $thumbName)) {
            return $thumbName;
        }
        
        // Create thumbnail directory if needed
        $thumbDir = dirname($thumbPath);
        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0755, true);
        }
        
        // Copy image as thumbnail if it exists
        if (file_exists($imagePath) && $imageName != 'def.webp') {
            copy($imagePath, $thumbPath);
            return $thumbName;
        }
        
        // Use same image name if default or image doesn't exist
        return $imageName;
    }
}
