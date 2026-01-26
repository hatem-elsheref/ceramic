<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeo;
use App\Models\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductionCeramicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Production-ready seeder for ceramic tiles e-commerce
     *
     * @return void
     */
    public function run()
    {
        echo "Starting production seeder...\n\n";
        
        // Create Categories
        $this->createCategories();
        
        // Create Your Brand
        $this->createBrand();
        
        // Create Products
        $this->createProducts();
        
        // Update Business Settings
        $this->updateBusinessSettings();
        
        echo "\n✅ Production seeder completed successfully!\n";
    }

    private function createCategories()
    {
        echo "Creating categories...\n";
        
        $categories = [
            [
                'en' => 'Floor Tiles',
                'ar' => 'بلاط الأرضيات',
                'slug' => 'floor-tiles',
                'position' => 1,
                'home_status' => 1,
                'priority' => 1,
            ],
            [
                'en' => 'Wall Tiles',
                'ar' => 'بلاط الجدران',
                'slug' => 'wall-tiles',
                'position' => 2,
                'home_status' => 1,
                'priority' => 2,
            ],
            [
                'en' => 'Bathroom Tiles',
                'ar' => 'بلاط الحمامات',
                'slug' => 'bathroom-tiles',
                'position' => 3,
                'home_status' => 1,
                'priority' => 3,
            ],
            [
                'en' => 'Kitchen Tiles',
                'ar' => 'بلاط المطابخ',
                'slug' => 'kitchen-tiles',
                'position' => 4,
                'home_status' => 1,
                'priority' => 4,
            ],
            [
                'en' => 'Porcelain Tiles',
                'ar' => 'بلاط البورسلين',
                'slug' => 'porcelain-tiles',
                'position' => 5,
                'home_status' => 1,
                'priority' => 5,
            ],
            [
                'en' => 'Outdoor Tiles',
                'ar' => 'بلاط خارجي',
                'slug' => 'outdoor-tiles',
                'position' => 6,
                'home_status' => 1,
                'priority' => 6,
            ],
        ];

        $subCategories = [
            [
                'en' => 'Ceramic Floor Tiles',
                'ar' => 'بلاط أرضيات سيراميك',
                'slug' => 'ceramic-floor-tiles',
                'parent_position' => 1,
                'position' => 1,
            ],
            [
                'en' => 'Porcelain Floor Tiles',
                'ar' => 'بلاط أرضيات بورسلين',
                'slug' => 'porcelain-floor-tiles',
                'parent_position' => 1,
                'position' => 2,
            ],
            [
                'en' => 'Decorative Wall Tiles',
                'ar' => 'بلاط جدران زخرفي',
                'slug' => 'decorative-wall-tiles',
                'parent_position' => 2,
                'position' => 3,
            ],
            [
                'en' => 'Mosaic Tiles',
                'ar' => 'بلاط موزاييك',
                'slug' => 'mosaic-tiles',
                'parent_position' => 2,
                'position' => 4,
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $category) {
            $cat = Category::updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['en'],
                    'slug' => $category['slug'],
                    'icon' => 'def.png',
                    'icon_storage_type' => 'public',
                    'parent_id' => 0,
                    'position' => $category['position'],
                    'home_status' => $category['home_status'],
                    'priority' => $category['priority'],
                ]
            );

            // Add Arabic translation
            Translation::updateOrCreate(
                [
                    'translationable_type' => 'App\Models\Category',
                    'translationable_id' => $cat->id,
                    'locale' => 'sa',
                    'key' => 'name',
                ],
                ['value' => $category['ar']]
            );

            $createdCategories[$category['position']] = $cat;
        }

        // Create sub-categories
        foreach ($subCategories as $subCategory) {
            $parentId = $createdCategories[$subCategory['parent_position']]->id ?? null;
            if ($parentId) {
                $subCat = Category::updateOrCreate(
                    ['slug' => $subCategory['slug']],
                    [
                        'name' => $subCategory['en'],
                        'slug' => $subCategory['slug'],
                        'icon' => 'def.png',
                        'icon_storage_type' => 'public',
                        'parent_id' => $parentId,
                        'position' => $subCategory['position'],
                        'home_status' => 1,
                        'priority' => $subCategory['position'],
                    ]
                );

                Translation::updateOrCreate(
                    [
                        'translationable_type' => 'App\Models\Category',
                        'translationable_id' => $subCat->id,
                        'locale' => 'sa',
                        'key' => 'name',
                    ],
                    ['value' => $subCategory['ar']]
                );
            }
        }

        $this->mainCategoryIds = array_column($createdCategories, 'id');
        $this->subCategoryIds = Category::where('parent_id', '>', 0)->pluck('id')->toArray();
        
        echo "✓ Created " . count($createdCategories) . " main categories and " . count($subCategories) . " sub-categories\n\n";
    }

    private function createBrand()
    {
        echo "Creating your brand...\n";
        
        // Your brand - update with your actual brand name
        $brandNameEn = 'Premium Ceramics';
        $brandNameAr = 'سيراميك ممتاز';
        
        $brand = Brand::updateOrCreate(
            ['name' => $brandNameEn],
            [
                'name' => $brandNameEn,
                'image' => 'def.png',
                'image_storage_type' => 'public',
                'image_alt_text' => $brandNameEn,
                'status' => 1,
            ]
        );

        Translation::updateOrCreate(
            [
                'translationable_type' => 'App\Models\Brand',
                'translationable_id' => $brand->id,
                'locale' => 'sa',
                'key' => 'name',
            ],
            ['value' => $brandNameAr]
        );

        $this->brandId = $brand->id;
        echo "✓ Created brand: $brandNameEn / $brandNameAr\n\n";
    }

    private function createProducts()
    {
        echo "Creating products...\n";
        
        $products = [
            [
                'en_name' => 'Premium White Ceramic Floor Tile 60x60',
                'ar_name' => 'بلاط أرضيات سيراميك أبيض ممتاز 60x60',
                'en_details' => 'High-quality white ceramic floor tile perfect for modern interiors. Durable, easy to clean, and available in 60x60cm size. Ideal for living rooms, bedrooms, and commercial spaces. Made with premium materials for long-lasting beauty.',
                'ar_details' => 'بلاط أرضيات سيراميك أبيض عالي الجودة مثالي للديكورات الداخلية العصرية. متين وسهل التنظيف ومتاح بحجم 60x60 سم. مثالي للصالات وغرف النوم والمساحات التجارية. مصنوع من مواد ممتازة لجمال دائم.',
                'unit_price' => 45.00,
                'purchase_price' => 30.00,
                'discount' => 10.00,
                'current_stock' => 500,
                'code' => 'PCT-001',
            ],
            [
                'en_name' => 'Elegant Marble Look Porcelain Tile 80x80',
                'ar_name' => 'بلاط بورسلين أنيق بتصميم رخامي 80x80',
                'en_details' => 'Luxurious porcelain tile with authentic marble effect. Large format 80x80cm perfect for spacious areas. Water-resistant and scratch-resistant surface. Premium quality for elegant interiors.',
                'ar_details' => 'بلاط بورسلين فاخر بتأثير رخامي أصيل. حجم كبير 80x80 سم مثالي للمساحات الواسعة. مقاوم للماء والخدوش. جودة ممتازة للديكورات الداخلية الأنيقة.',
                'unit_price' => 85.00,
                'purchase_price' => 60.00,
                'discount' => 15.00,
                'current_stock' => 300,
                'code' => 'PCT-002',
            ],
            [
                'en_name' => 'Modern Geometric Wall Tile 30x60',
                'ar_name' => 'بلاط جدران هندسي عصري 30x60',
                'en_details' => 'Contemporary geometric pattern wall tile. Perfect for accent walls and feature areas. Available in multiple color options. Adds modern flair to any space.',
                'ar_details' => 'بلاط جدران بتصميم هندسي معاصر. مثالي للجدران المميزة والمناطق الخاصة. متوفر بعدة ألوان. يضيف لمسة عصرية لأي مساحة.',
                'unit_price' => 35.00,
                'purchase_price' => 22.00,
                'discount' => 5.00,
                'current_stock' => 400,
                'code' => 'PCT-003',
            ],
            [
                'en_name' => 'Bathroom Mosaic Tile Collection',
                'ar_name' => 'مجموعة بلاط موزاييك للحمامات',
                'en_details' => 'Beautiful mosaic tile collection designed specifically for bathrooms. Waterproof and mold-resistant. Easy installation and maintenance. Available in various patterns and colors.',
                'ar_details' => 'مجموعة بلاط موزاييك جميلة مصممة خصيصاً للحمامات. مقاوم للماء والعفن. سهل التركيب والصيانة. متوفر بأنماط وألوان متنوعة.',
                'unit_price' => 55.00,
                'purchase_price' => 35.00,
                'discount' => 12.00,
                'current_stock' => 250,
                'code' => 'PCT-004',
            ],
            [
                'en_name' => 'Kitchen Backsplash Ceramic Tile 25x40',
                'ar_name' => 'بلاط سيراميك لواجهة المطبخ 25x40',
                'en_details' => 'Splash-resistant ceramic tile perfect for kitchen backsplashes. Heat and stain resistant. Available in various patterns and colors. Easy to clean and maintain.',
                'ar_details' => 'بلاط سيراميك مقاوم للتناثر مثالي لواجهات المطابخ. مقاوم للحرارة والبقع. متوفر بأنماط وألوان متنوعة. سهل التنظيف والصيانة.',
                'unit_price' => 28.00,
                'purchase_price' => 18.00,
                'discount' => 8.00,
                'current_stock' => 600,
                'code' => 'PCT-005',
            ],
            [
                'en_name' => 'Premium Wood Look Porcelain Tile 20x120',
                'ar_name' => 'بلاط بورسلين فاخر بتصميم خشبي 20x120',
                'en_details' => 'Authentic wood appearance with porcelain durability. Long plank format 20x120cm. Perfect for living areas and bedrooms. Combines natural beauty with modern technology.',
                'ar_details' => 'مظهر خشبي أصيل مع متانة البورسلين. لوح طويل بحجم 20x120 سم. مثالي للصالات وغرف النوم. يجمع بين الجمال الطبيعي والتكنولوجيا الحديثة.',
                'unit_price' => 75.00,
                'purchase_price' => 50.00,
                'discount' => 18.00,
                'current_stock' => 350,
                'code' => 'PCT-006',
            ],
            [
                'en_name' => 'Glossy Black Ceramic Floor Tile 50x50',
                'ar_name' => 'بلاط أرضيات سيراميك أسود لامع 50x50',
                'en_details' => 'Sleek glossy black ceramic tile for modern interiors. High-gloss finish adds elegance to any space. Easy to maintain and perfect for contemporary designs.',
                'ar_details' => 'بلاط سيراميك أسود لامع أنيق للديكورات الداخلية العصرية. لمسة نهائية عالية اللمعان تضيف الأناقة لأي مساحة. سهل الصيانة ومثالي للتصاميم المعاصرة.',
                'unit_price' => 42.00,
                'purchase_price' => 28.00,
                'discount' => 10.00,
                'current_stock' => 450,
                'code' => 'PCT-007',
            ],
            [
                'en_name' => 'Decorative Pattern Wall Tile 40x40',
                'ar_name' => 'بلاط جدران زخرفي 40x40',
                'en_details' => 'Artistic decorative pattern wall tile. Perfect for creating focal points in living rooms and hallways. Premium quality with intricate designs.',
                'ar_details' => 'بلاط جدران بتصميم زخرفي فني. مثالي لإنشاء نقاط محورية في الصالات والممرات. جودة ممتازة بتصاميم معقدة.',
                'unit_price' => 38.00,
                'purchase_price' => 24.00,
                'discount' => 7.00,
                'current_stock' => 380,
                'code' => 'PCT-008',
            ],
            [
                'en_name' => 'Anti-Slip Bathroom Floor Tile 30x30',
                'ar_name' => 'بلاط أرضيات حمامات مضاد للانزلاق 30x30',
                'en_details' => 'Safety-first anti-slip bathroom floor tile. Textured surface provides excellent grip. Water-resistant and easy to clean. Perfect for family bathrooms.',
                'ar_details' => 'بلاط أرضيات حمامات مضاد للانزلاق يضع السلامة أولاً. سطح منسوج يوفر قبضة ممتازة. مقاوم للماء وسهل التنظيف. مثالي لحمامات العائلة.',
                'unit_price' => 32.00,
                'purchase_price' => 20.00,
                'discount' => 6.00,
                'current_stock' => 520,
                'code' => 'PCT-009',
            ],
            [
                'en_name' => 'Premium Stone Look Porcelain Tile 60x120',
                'ar_name' => 'بلاط بورسلين فاخر بتصميم حجري 60x120',
                'en_details' => 'Natural stone appearance with modern porcelain technology. Large format tile perfect for commercial and residential projects. Durable and elegant.',
                'ar_details' => 'مظهر حجري طبيعي مع تقنية البورسلين الحديثة. بلاط بحجم كبير مثالي للمشاريع التجارية والسكنية. متين وأنيق.',
                'unit_price' => 95.00,
                'purchase_price' => 65.00,
                'discount' => 20.00,
                'current_stock' => 280,
                'code' => 'PCT-010',
            ],
            [
                'en_name' => 'Classic Subway Wall Tile 7.5x15',
                'ar_name' => 'بلاط جدران كلاسيكي على شكل مترو 7.5x15',
                'en_details' => 'Timeless classic subway tile design. Versatile and suitable for kitchens, bathrooms, and accent walls. Easy to install and maintain.',
                'ar_details' => 'تصميم بلاط مترو كلاسيكي خالد. متعدد الاستخدامات ومناسب للمطابخ والحمامات والجدران المميزة. سهل التركيب والصيانة.',
                'unit_price' => 25.00,
                'purchase_price' => 15.00,
                'discount' => 5.00,
                'current_stock' => 700,
                'code' => 'PCT-011',
            ],
            [
                'en_name' => 'Luxury Gold Accent Mosaic Tile',
                'ar_name' => 'بلاط موزاييك فاخر بلمسات ذهبية',
                'en_details' => 'Opulent gold accent mosaic tile for luxury interiors. Perfect for feature walls and decorative elements. Adds elegance and sophistication.',
                'ar_details' => 'بلاط موزاييك بلمسات ذهبية فاخرة للديكورات الداخلية الفاخرة. مثالي للجدران المميزة والعناصر الزخرفية. يضيف الأناقة والرقي.',
                'unit_price' => 120.00,
                'purchase_price' => 80.00,
                'discount' => 25.00,
                'current_stock' => 150,
                'code' => 'PCT-012',
            ],
            [
                'en_name' => 'Rustic Terracotta Floor Tile 40x40',
                'ar_name' => 'بلاط أرضيات تراكوتا ريفي 40x40',
                'en_details' => 'Warm terracotta floor tile with rustic charm. Perfect for Mediterranean and rustic style interiors. Natural earth tones create cozy atmosphere.',
                'ar_details' => 'بلاط أرضيات تراكوتا دافئ بسحر ريفي. مثالي للديكورات الداخلية على الطراز المتوسطي والريفي. الألوان الترابية الطبيعية تخلق أجواء دافئة.',
                'unit_price' => 40.00,
                'purchase_price' => 26.00,
                'discount' => 9.00,
                'current_stock' => 420,
                'code' => 'PCT-013',
            ],
            [
                'en_name' => 'Modern Hexagon Wall Tile 20x23',
                'ar_name' => 'بلاط جدران سداسي عصري 20x23',
                'en_details' => 'Contemporary hexagon pattern wall tile. Unique geometric design adds modern flair to any space. Perfect for accent walls and creative designs.',
                'ar_details' => 'بلاط جدران بتصميم سداسي معاصر. تصميم هندسي فريد يضيف لمسة عصرية لأي مساحة. مثالي للجدران المميزة والتصاميم الإبداعية.',
                'unit_price' => 48.00,
                'purchase_price' => 30.00,
                'discount' => 12.00,
                'current_stock' => 320,
                'code' => 'PCT-014',
            ],
            [
                'en_name' => 'Premium Glossy White Bathroom Tile 30x60',
                'ar_name' => 'بلاط حمامات أبيض لامع ممتاز 30x60',
                'en_details' => 'High-gloss white bathroom tile for a clean, modern look. Reflects light beautifully and easy to maintain. Perfect for contemporary bathrooms.',
                'ar_details' => 'بلاط حمامات أبيض عالي اللمعان لمظهر نظيف وعصري. يعكس الضوء بشكل جميل وسهل الصيانة. مثالي للحمامات المعاصرة.',
                'unit_price' => 36.00,
                'purchase_price' => 23.00,
                'discount' => 8.00,
                'current_stock' => 480,
                'code' => 'PCT-015',
            ],
            [
                'en_name' => 'Industrial Concrete Look Porcelain Tile 60x60',
                'ar_name' => 'بلاط بورسلين بتصميم خرساني صناعي 60x60',
                'en_details' => 'Urban industrial style porcelain tile with concrete texture. Perfect for modern lofts and commercial spaces. Durable and stylish.',
                'ar_details' => 'بلاط بورسلين على الطراز الصناعي الحضري بملمس خرساني. مثالي للشقق العصرية والمساحات التجارية. متين وأنيق.',
                'unit_price' => 68.00,
                'purchase_price' => 45.00,
                'discount' => 15.00,
                'current_stock' => 360,
                'code' => 'PCT-016',
            ],
            [
                'en_name' => 'Elegant Beige Ceramic Floor Tile 50x50',
                'ar_name' => 'بلاط أرضيات سيراميك بيج أنيق 50x50',
                'en_details' => 'Sophisticated beige ceramic floor tile. Neutral color complements any interior design style. Versatile and timeless.',
                'ar_details' => 'بلاط أرضيات سيراميك بيج راقي. لون محايد يكمل أي نمط تصميم داخلي. متعدد الاستخدامات وخالد.',
                'unit_price' => 39.00,
                'purchase_price' => 25.00,
                'discount' => 9.00,
                'current_stock' => 550,
                'code' => 'PCT-017',
            ],
            [
                'en_name' => 'Artistic Floral Pattern Wall Tile 25x40',
                'ar_name' => 'بلاط جدران بتصميم زهري فني 25x40',
                'en_details' => 'Beautiful floral pattern wall tile for decorative purposes. Adds elegance and charm to any room. Perfect for feature walls.',
                'ar_details' => 'بلاط جدران بتصميم زهري جميل للأغراض الزخرفية. يضيف الأناقة والسحر لأي غرفة. مثالي للجدران المميزة.',
                'unit_price' => 44.00,
                'purchase_price' => 28.00,
                'discount' => 10.00,
                'current_stock' => 290,
                'code' => 'PCT-018',
            ],
            [
                'en_name' => 'Premium Non-Slip Pool Tile 15x15',
                'ar_name' => 'بلاط برك سباحة فاخر مضاد للانزلاق 15x15',
                'en_details' => 'Specialized pool tile with excellent anti-slip properties. Resistant to chlorine and pool chemicals. Safe and durable for pool areas.',
                'ar_details' => 'بلاط برك سباحة متخصص بخصائص ممتازة مضادة للانزلاق. مقاوم للكلور ومواد البرك الكيميائية. آمن ومتين لمناطق البرك.',
                'unit_price' => 52.00,
                'purchase_price' => 33.00,
                'discount' => 11.00,
                'current_stock' => 200,
                'code' => 'PCT-019',
            ],
            [
                'en_name' => 'Luxury Marble Effect Porcelain Tile 120x120',
                'ar_name' => 'بلاط بورسلين فاخر بتأثير رخامي 120x120',
                'en_details' => 'Ultra-luxury large format marble effect porcelain tile. Perfect for high-end residential and commercial projects. Exquisite quality and design.',
                'ar_details' => 'بلاط بورسلين بتأثير رخامي فاخر جداً بحجم كبير. مثالي للمشاريع السكنية والتجارية عالية الجودة. جودة وتصميم رائعان.',
                'unit_price' => 150.00,
                'purchase_price' => 100.00,
                'discount' => 30.00,
                'current_stock' => 120,
                'code' => 'PCT-020',
            ],
        ];

        $categoryIds = $this->mainCategoryIds ?? Category::where('parent_id', 0)->pluck('id')->toArray();
        $subCategoryIds = $this->subCategoryIds ?? Category::where('parent_id', '>', 0)->pluck('id')->toArray();
        $brandId = $this->brandId ?? Brand::first()->id ?? 1;

        // Get all available images from storage
        $availableImages = $this->getAvailableImages();
        $usedImages = [];
        
        echo "Found " . count($availableImages) . " images available in storage\n\n";

        $created = 0;
        $skipped = 0;

        foreach ($products as $index => $productData) {
            // Check if product already exists
            $existing = Product::where('code', $productData['code'])->first();
            if ($existing) {
                $skipped++;
                continue;
            }

            $categoryId = $categoryIds[array_rand($categoryIds)] ?? $categoryIds[0] ?? 1;
            $subCategoryId = !empty($subCategoryIds) ? $subCategoryIds[array_rand($subCategoryIds)] : null;

            // Generate image names
            $codeLower = strtolower($productData['code']);
            $imageName = $codeLower . '.webp';
            $thumbnailName = $codeLower . '-thumb.webp';
            
            // Try to find matching image from available images
            $foundImage = $this->findMatchingImage($codeLower, $availableImages, $usedImages);
            
            // Use found image or default
            $defaultImage = 'def.webp';
            $finalImageName = $foundImage ?: $defaultImage;
            
            // Create thumbnail
            $finalThumbName = $this->createThumbnail($finalImageName, $codeLower);
            
            // Mark image as used if it's not the default
            if ($foundImage && $foundImage != $defaultImage) {
                $usedImages[] = $foundImage;
            }
            
            $images = json_encode([
                ['image_name' => $finalImageName, 'storage' => 'public'],
                ['image_name' => $finalImageName, 'storage' => 'public'],
            ]);

            try {
                $product = Product::create([
                    'added_by' => 'admin',
                    'user_id' => 1,
                    'name' => $productData['en_name'],
                    'code' => $productData['code'],
                    'slug' => Str::slug($productData['en_name']) . '-' . Str::random(6),
                    'category_ids' => (string)$categoryId,
                    'category_id' => $categoryId,
                    'sub_category_id' => $subCategoryId,
                    'sub_sub_category_id' => null,
                    'brand_id' => $brandId,
                    'unit' => 'piece',
                    'product_type' => 'physical',
                    'digital_product_type' => null,
                    'details' => $productData['en_details'],
                    'colors' => json_encode([]),
                    'choice_options' => json_encode([]),
                    'variation' => json_encode([]),
                    'unit_price' => $productData['unit_price'],
                    'purchase_price' => $productData['purchase_price'],
                    'tax' => 15.00,
                    'tax_type' => 'percent',
                    'tax_model' => 'exclude',
                    'discount' => $productData['discount'],
                    'discount_type' => 'percent',
                    'current_stock' => $productData['current_stock'],
                    'minimum_order_qty' => 1,
                    'min_qty' => 1,
                    'status' => 1,
                    'request_status' => 1,
                    'featured_status' => $index < 10 ? 1 : 0, // Mark first 10 products as featured
                    'featured' => $index < 10 ? 1 : 0, // Mark first 10 products as featured
                    'published' => 1,
                    'refundable' => 1,
                    'free_shipping' => 0,
                    'shipping_cost' => 25.00,
                    'multiply_qty' => 0,
                    'images' => $images,
                    'thumbnail' => $finalThumbName,
                    'thumbnail_storage_type' => 'public',
                    'color_image' => json_encode([]),
                    'meta_title' => $productData['en_name'] . ' - Premium Ceramic Tiles Saudi Arabia',
                    'meta_description' => $productData['en_details'],
                    'meta_image' => $finalThumbName,
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
                    'image' => $finalThumbName,
                ]);

                $created++;
            } catch (\Exception $e) {
                echo "✗ Error creating product {$productData['code']}: " . $e->getMessage() . "\n";
                $skipped++;
            }
        }

        echo "✓ Created $created products\n";
        if ($skipped > 0) {
            echo "⊘ Skipped $skipped products (already exist)\n";
        }
        echo "\n";
    }

    private function updateBusinessSettings()
    {
        echo "Updating business settings...\n";
        
        $settings = [
            [
                'type' => 'company_name',
                'value' => 'Premium Ceramics Store',
            ],
            [
                'type' => 'company_email',
                'value' => 'info@premiumceramics.sa',
            ],
            [
                'type' => 'company_phone',
                'value' => '+966 11 123 4567',
            ],
            [
                'type' => 'country_code',
                'value' => 'SA',
            ],
            [
                'type' => 'shop_address',
                'value' => 'King Fahd Road, Riyadh, Saudi Arabia',
            ],
            [
                'type' => 'currency_symbol_position',
                'value' => 'right',
            ],
            [
                'type' => 'decimal_point_settings',
                'value' => '2',
            ],
            [
                'type' => 'business_mode',
                'value' => 'single',
            ],
            [
                'type' => 'pagination_limit',
                'value' => '20',
            ],
            [
                'type' => 'timezone',
                'value' => 'Asia/Riyadh',
            ],
            [
                'type' => 'default_location',
                'value' => json_encode(['lat' => 24.7136, 'lng' => 46.6753]),
            ],
        ];

        foreach ($settings as $setting) {
            BusinessSetting::updateOrInsert(
                ['type' => $setting['type']],
                ['value' => $setting['value'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
        
        echo "✓ Business settings updated\n\n";
    }

    /**
     * Get all available images from storage
     */
    private function getAvailableImages(): array
    {
        $imagePath = storage_path('app/public/product');
        $images = [];
        
        if (is_dir($imagePath)) {
            $files = scandir($imagePath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && !is_dir($imagePath . '/' . $file)) {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['webp', 'jpg', 'jpeg', 'png']) && $file != 'def.webp') {
                        $images[] = $file;
                    }
                }
            }
        }
        
        return $images;
    }

    /**
     * Find matching image for product code
     */
    private function findMatchingImage(string $codeLower, array $availableImages, array $usedImages): ?string
    {
        $codeNoDash = str_replace('-', '', $codeLower);
        
        // Method 1: Exact match by product code
        foreach ($availableImages as $image) {
            if (in_array($image, $usedImages)) continue;
            
            $imageBase = strtolower(pathinfo($image, PATHINFO_FILENAME));
            
            // Check for exact code match
            if ($imageBase == $codeLower || $imageBase == $codeNoDash) {
                return $image;
            }
        }
        
        // Method 2: Use next available image
        foreach ($availableImages as $image) {
            if (!in_array($image, $usedImages)) {
                return $image;
            }
        }
        
        return null; // Will use default
    }

    /**
     * Create thumbnail from image
     */
    private function createThumbnail(string $imageName, string $codeLower): string
    {
        $thumbName = $codeLower . '-thumb.webp';
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
