<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeo;
use App\Models\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CeramicDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Categories
        $this->createCategories();
        
        // Create Brands
        $this->createBrands();
        
        // Create Products
        $this->createProducts();
        
        // Create Business Settings
        $this->createBusinessSettings();
    }

    private function createCategories()
    {
        $categories = [
            // Main Categories
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
        ];

        $subCategories = [
            // Sub-categories for Floor Tiles
            [
                'en' => 'Ceramic Floor Tiles',
                'ar' => 'بلاط أرضيات سيراميك',
                'slug' => 'ceramic-floor-tiles',
                'parent_id' => null, // Will be set after main category creation
                'position' => 1,
            ],
            [
                'en' => 'Porcelain Floor Tiles',
                'ar' => 'بلاط أرضيات بورسلين',
                'slug' => 'porcelain-floor-tiles',
                'parent_id' => null,
                'position' => 2,
            ],
            // Sub-categories for Wall Tiles
            [
                'en' => 'Decorative Wall Tiles',
                'ar' => 'بلاط جدران زخرفي',
                'slug' => 'decorative-wall-tiles',
                'parent_id' => null,
                'position' => 3,
            ],
            [
                'en' => 'Mosaic Tiles',
                'ar' => 'بلاط موزاييك',
                'slug' => 'mosaic-tiles',
                'parent_id' => null,
                'position' => 4,
            ],
        ];

        $createdCategories = [];
        foreach ($categories as $index => $category) {
            $cat = Category::create([
                'name' => $category['en'],
                'slug' => $category['slug'],
                'icon' => 'def.png',
                'icon_storage_type' => 'public',
                'parent_id' => null,
                'position' => $category['position'],
                'home_status' => $category['home_status'],
                'priority' => $category['priority'],
            ]);

            // Add Arabic translation
            Translation::create([
                'translationable_type' => 'App\Models\Category',
                'translationable_id' => $cat->id,
                'locale' => 'sa',
                'key' => 'name',
                'value' => $category['ar'],
            ]);

            $createdCategories[] = $cat;
        }

        // Create sub-categories
        $subCategoryParents = [
            0 => $createdCategories[0]->id, // Floor Tiles
            1 => $createdCategories[0]->id, // Floor Tiles
            2 => $createdCategories[1]->id, // Wall Tiles
            3 => $createdCategories[1]->id, // Wall Tiles
        ];

        foreach ($subCategories as $index => $subCategory) {
            $subCat = Category::create([
                'name' => $subCategory['en'],
                'slug' => $subCategory['slug'],
                'icon' => 'def.png',
                'icon_storage_type' => 'public',
                'parent_id' => $subCategoryParents[$index] ?? null,
                'position' => $subCategory['position'],
                'home_status' => 1,
                'priority' => $subCategory['position'],
            ]);

            // Add Arabic translation
            Translation::create([
                'translationable_type' => 'App\Models\Category',
                'translationable_id' => $subCat->id,
                'locale' => 'sa',
                'key' => 'name',
                'value' => $subCategory['ar'],
            ]);
        }

        // Store category IDs for products
        $this->mainCategoryIds = array_column($createdCategories, 'id');
        $this->subCategoryIds = Category::whereNotNull('parent_id')->pluck('id')->toArray();
    }

    private function createBrands()
    {
        $brands = [
            [
                'en' => 'Rak Ceramics',
                'ar' => 'راك سيراميك',
                'image' => 'def.png',
            ],
            [
                'en' => 'Porcelanosa',
                'ar' => 'بورسلانوسا',
                'image' => 'def.png',
            ],
            [
                'en' => 'VitrA',
                'ar' => 'فيترا',
                'image' => 'def.png',
            ],
            [
                'en' => 'Emser Tile',
                'ar' => 'إيمسر تايل',
                'image' => 'def.png',
            ],
            [
                'en' => 'Riyadh Ceramics',
                'ar' => 'سيراميك الرياض',
                'image' => 'def.png',
            ],
        ];

        $createdBrands = [];
        foreach ($brands as $brandData) {
            $brand = Brand::create([
                'name' => $brandData['en'],
                'image' => $brandData['image'],
                'image_storage_type' => 'public',
                'image_alt_text' => $brandData['en'],
                'status' => 1,
            ]);

            // Add Arabic translation
            Translation::create([
                'translationable_type' => 'App\Models\Brand',
                'translationable_id' => $brand->id,
                'locale' => 'sa',
                'key' => 'name',
                'value' => $brandData['ar'],
            ]);

            $createdBrands[] = $brand;
        }

        $this->brandIds = array_column($createdBrands, 'id');
    }

    private function createProducts()
    {
        $products = [
            [
                'en_name' => 'Premium White Ceramic Floor Tile 60x60',
                'ar_name' => 'بلاط أرضيات سيراميك أبيض ممتاز 60x60',
                'en_details' => 'High-quality white ceramic floor tile perfect for modern interiors. Durable, easy to clean, and available in 60x60cm size. Ideal for living rooms, bedrooms, and commercial spaces.',
                'ar_details' => 'بلاط أرضيات سيراميك أبيض عالي الجودة مثالي للديكورات الداخلية العصرية. متين وسهل التنظيف ومتاح بحجم 60x60 سم. مثالي للصالات وغرف النوم والمساحات التجارية.',
                'unit_price' => 45.00,
                'purchase_price' => 30.00,
                'discount' => 10.00,
                'current_stock' => 500,
                'code' => 'CFT-001',
            ],
            [
                'en_name' => 'Elegant Marble Look Porcelain Tile 80x80',
                'ar_name' => 'بلاط بورسلين أنيق بتصميم رخامي 80x80',
                'en_details' => 'Luxurious porcelain tile with marble effect. Large format 80x80cm perfect for spacious areas. Water-resistant and scratch-resistant surface.',
                'ar_details' => 'بلاط بورسلين فاخر بتأثير رخامي. حجم كبير 80x80 سم مثالي للمساحات الواسعة. مقاوم للماء والخدوش.',
                'unit_price' => 85.00,
                'purchase_price' => 60.00,
                'discount' => 15.00,
                'current_stock' => 300,
                'code' => 'PFT-002',
            ],
            [
                'en_name' => 'Modern Geometric Wall Tile 30x60',
                'ar_name' => 'بلاط جدران هندسي عصري 30x60',
                'en_details' => 'Contemporary geometric pattern wall tile. Perfect for accent walls and feature areas. Available in multiple color options.',
                'ar_details' => 'بلاط جدران بتصميم هندسي معاصر. مثالي للجدران المميزة والمناطق الخاصة. متوفر بعدة ألوان.',
                'unit_price' => 35.00,
                'purchase_price' => 22.00,
                'discount' => 5.00,
                'current_stock' => 400,
                'code' => 'WWT-003',
            ],
            [
                'en_name' => 'Bathroom Mosaic Tile Collection',
                'ar_name' => 'مجموعة بلاط موزاييك للحمامات',
                'en_details' => 'Beautiful mosaic tile collection designed specifically for bathrooms. Waterproof and mold-resistant. Easy installation and maintenance.',
                'ar_details' => 'مجموعة بلاط موزاييك جميلة مصممة خصيصاً للحمامات. مقاوم للماء والعفن. سهل التركيب والصيانة.',
                'unit_price' => 55.00,
                'purchase_price' => 35.00,
                'discount' => 12.00,
                'current_stock' => 250,
                'code' => 'BMT-004',
            ],
            [
                'en_name' => 'Kitchen Backsplash Ceramic Tile 25x40',
                'ar_name' => 'بلاط سيراميك لواجهة المطبخ 25x40',
                'en_details' => 'Splash-resistant ceramic tile perfect for kitchen backsplashes. Heat and stain resistant. Available in various patterns and colors.',
                'ar_details' => 'بلاط سيراميك مقاوم للتناثر مثالي لواجهات المطابخ. مقاوم للحرارة والبقع. متوفر بأنماط وألوان متنوعة.',
                'unit_price' => 28.00,
                'purchase_price' => 18.00,
                'discount' => 8.00,
                'current_stock' => 600,
                'code' => 'KBT-005',
            ],
            [
                'en_name' => 'Premium Wood Look Porcelain Tile 20x120',
                'ar_name' => 'بلاط بورسلين فاخر بتصميم خشبي 20x120',
                'en_details' => 'Authentic wood appearance with porcelain durability. Long plank format 20x120cm. Perfect for living areas and bedrooms.',
                'ar_details' => 'مظهر خشبي أصيل مع متانة البورسلين. لوح طويل بحجم 20x120 سم. مثالي للصالات وغرف النوم.',
                'unit_price' => 75.00,
                'purchase_price' => 50.00,
                'discount' => 18.00,
                'current_stock' => 350,
                'code' => 'WLT-006',
            ],
            [
                'en_name' => 'Glossy Black Ceramic Floor Tile 50x50',
                'ar_name' => 'بلاط أرضيات سيراميك أسود لامع 50x50',
                'en_details' => 'Sleek glossy black ceramic tile for modern interiors. High-gloss finish adds elegance to any space. Easy to maintain.',
                'ar_details' => 'بلاط سيراميك أسود لامع أنيق للديكورات الداخلية العصرية. لمسة نهائية عالية اللمعان تضيف الأناقة لأي مساحة. سهل الصيانة.',
                'unit_price' => 42.00,
                'purchase_price' => 28.00,
                'discount' => 10.00,
                'current_stock' => 450,
                'code' => 'BFT-007',
            ],
            [
                'en_name' => 'Decorative Pattern Wall Tile 40x40',
                'ar_name' => 'بلاط جدران زخرفي 40x40',
                'en_details' => 'Artistic decorative pattern wall tile. Perfect for creating focal points in living rooms and hallways. Premium quality.',
                'ar_details' => 'بلاط جدران بتصميم زخرفي فني. مثالي لإنشاء نقاط محورية في الصالات والممرات. جودة ممتازة.',
                'unit_price' => 38.00,
                'purchase_price' => 24.00,
                'discount' => 7.00,
                'current_stock' => 380,
                'code' => 'DPT-008',
            ],
            [
                'en_name' => 'Anti-Slip Bathroom Floor Tile 30x30',
                'ar_name' => 'بلاط أرضيات حمامات مضاد للانزلاق 30x30',
                'en_details' => 'Safety-first anti-slip bathroom floor tile. Textured surface provides excellent grip. Water-resistant and easy to clean.',
                'ar_details' => 'بلاط أرضيات حمامات مضاد للانزلاق يضع السلامة أولاً. سطح منسوج يوفر قبضة ممتازة. مقاوم للماء وسهل التنظيف.',
                'unit_price' => 32.00,
                'purchase_price' => 20.00,
                'discount' => 6.00,
                'current_stock' => 520,
                'code' => 'ASB-009',
            ],
            [
                'en_name' => 'Premium Stone Look Porcelain Tile 60x120',
                'ar_name' => 'بلاط بورسلين فاخر بتصميم حجري 60x120',
                'en_details' => 'Natural stone appearance with modern porcelain technology. Large format tile perfect for commercial and residential projects.',
                'ar_details' => 'مظهر حجري طبيعي مع تقنية البورسلين الحديثة. بلاط بحجم كبير مثالي للمشاريع التجارية والسكنية.',
                'unit_price' => 95.00,
                'purchase_price' => 65.00,
                'discount' => 20.00,
                'current_stock' => 280,
                'code' => 'SLT-010',
            ],
            [
                'en_name' => 'Classic Subway Wall Tile 7.5x15',
                'ar_name' => 'بلاط جدران كلاسيكي على شكل مترو 7.5x15',
                'en_details' => 'Timeless classic subway tile design. Versatile and suitable for kitchens, bathrooms, and accent walls.',
                'ar_details' => 'تصميم بلاط مترو كلاسيكي خالد. متعدد الاستخدامات ومناسب للمطابخ والحمامات والجدران المميزة.',
                'unit_price' => 25.00,
                'purchase_price' => 15.00,
                'discount' => 5.00,
                'current_stock' => 700,
                'code' => 'SWT-011',
            ],
            [
                'en_name' => 'Luxury Gold Accent Mosaic Tile',
                'ar_name' => 'بلاط موزاييك فاخر بلمسات ذهبية',
                'en_details' => 'Opulent gold accent mosaic tile for luxury interiors. Perfect for feature walls and decorative elements.',
                'ar_details' => 'بلاط موزاييك بلمسات ذهبية فاخرة للديكورات الداخلية الفاخرة. مثالي للجدران المميزة والعناصر الزخرفية.',
                'unit_price' => 120.00,
                'purchase_price' => 80.00,
                'discount' => 25.00,
                'current_stock' => 150,
                'code' => 'GAM-012',
            ],
            [
                'en_name' => 'Rustic Terracotta Floor Tile 40x40',
                'ar_name' => 'بلاط أرضيات تراكوتا ريفي 40x40',
                'en_details' => 'Warm terracotta floor tile with rustic charm. Perfect for Mediterranean and rustic style interiors.',
                'ar_details' => 'بلاط أرضيات تراكوتا دافئ بسحر ريفي. مثالي للديكورات الداخلية على الطراز المتوسطي والريفي.',
                'unit_price' => 40.00,
                'purchase_price' => 26.00,
                'discount' => 9.00,
                'current_stock' => 420,
                'code' => 'TFT-013',
            ],
            [
                'en_name' => 'Modern Hexagon Wall Tile 20x23',
                'ar_name' => 'بلاط جدران سداسي عصري 20x23',
                'en_details' => 'Contemporary hexagon pattern wall tile. Unique geometric design adds modern flair to any space.',
                'ar_details' => 'بلاط جدران بتصميم سداسي معاصر. تصميم هندسي فريد يضيف لمسة عصرية لأي مساحة.',
                'unit_price' => 48.00,
                'purchase_price' => 30.00,
                'discount' => 12.00,
                'current_stock' => 320,
                'code' => 'HWT-014',
            ],
            [
                'en_name' => 'Premium Glossy White Bathroom Tile 30x60',
                'ar_name' => 'بلاط حمامات أبيض لامع ممتاز 30x60',
                'en_details' => 'High-gloss white bathroom tile for a clean, modern look. Reflects light beautifully and easy to maintain.',
                'ar_details' => 'بلاط حمامات أبيض عالي اللمعان لمظهر نظيف وعصري. يعكس الضوء بشكل جميل وسهل الصيانة.',
                'unit_price' => 36.00,
                'purchase_price' => 23.00,
                'discount' => 8.00,
                'current_stock' => 480,
                'code' => 'WBT-015',
            ],
            [
                'en_name' => 'Industrial Concrete Look Porcelain Tile 60x60',
                'ar_name' => 'بلاط بورسلين بتصميم خرساني صناعي 60x60',
                'en_details' => 'Urban industrial style porcelain tile with concrete texture. Perfect for modern lofts and commercial spaces.',
                'ar_details' => 'بلاط بورسلين على الطراز الصناعي الحضري بملمس خرساني. مثالي للشقق العصرية والمساحات التجارية.',
                'unit_price' => 68.00,
                'purchase_price' => 45.00,
                'discount' => 15.00,
                'current_stock' => 360,
                'code' => 'CLT-016',
            ],
            [
                'en_name' => 'Elegant Beige Ceramic Floor Tile 50x50',
                'ar_name' => 'بلاط أرضيات سيراميك بيج أنيق 50x50',
                'en_details' => 'Sophisticated beige ceramic floor tile. Neutral color complements any interior design style.',
                'ar_details' => 'بلاط أرضيات سيراميك بيج راقي. لون محايد يكمل أي نمط تصميم داخلي.',
                'unit_price' => 39.00,
                'purchase_price' => 25.00,
                'discount' => 9.00,
                'current_stock' => 550,
                'code' => 'BFT-017',
            ],
            [
                'en_name' => 'Artistic Floral Pattern Wall Tile 25x40',
                'ar_name' => 'بلاط جدران بتصميم زهري فني 25x40',
                'en_details' => 'Beautiful floral pattern wall tile for decorative purposes. Adds elegance and charm to any room.',
                'ar_details' => 'بلاط جدران بتصميم زهري جميل للأغراض الزخرفية. يضيف الأناقة والسحر لأي غرفة.',
                'unit_price' => 44.00,
                'purchase_price' => 28.00,
                'discount' => 10.00,
                'current_stock' => 290,
                'code' => 'FPT-018',
            ],
            [
                'en_name' => 'Premium Non-Slip Pool Tile 15x15',
                'ar_name' => 'بلاط برك سباحة فاخر مضاد للانزلاق 15x15',
                'en_details' => 'Specialized pool tile with excellent anti-slip properties. Resistant to chlorine and pool chemicals.',
                'ar_details' => 'بلاط برك سباحة متخصص بخصائص ممتازة مضادة للانزلاق. مقاوم للكلور ومواد البرك الكيميائية.',
                'unit_price' => 52.00,
                'purchase_price' => 33.00,
                'discount' => 11.00,
                'current_stock' => 200,
                'code' => 'PPT-019',
            ],
            [
                'en_name' => 'Luxury Marble Effect Porcelain Tile 120x120',
                'ar_name' => 'بلاط بورسلين فاخر بتأثير رخامي 120x120',
                'en_details' => 'Ultra-luxury large format marble effect porcelain tile. Perfect for high-end residential and commercial projects.',
                'ar_details' => 'بلاط بورسلين بتأثير رخامي فاخر جداً بحجم كبير. مثالي للمشاريع السكنية والتجارية عالية الجودة.',
                'unit_price' => 150.00,
                'purchase_price' => 100.00,
                'discount' => 30.00,
                'current_stock' => 120,
                'code' => 'MLT-020',
            ],
        ];

        $categoryIds = $this->mainCategoryIds ?? Category::whereNull('parent_id')->pluck('id')->toArray();
        $subCategoryIds = $this->subCategoryIds ?? Category::whereNotNull('parent_id')->pluck('id')->toArray();
        $brandIds = $this->brandIds ?? Brand::pluck('id')->toArray();

        foreach ($products as $index => $productData) {
            $categoryId = $categoryIds[array_rand($categoryIds)];
            $subCategoryId = !empty($subCategoryIds) ? $subCategoryIds[array_rand($subCategoryIds)] : null;
            $brandId = $brandIds[array_rand($brandIds)];

            // Generate image names (using default placeholder - replace with actual product images later)
            // Note: You can upload actual product images via admin panel and update these references
            $imageName = 'def.webp'; // Default placeholder - replace with actual product images
            $thumbnailName = 'def.webp'; // Default placeholder - replace with actual product images
            
            $images = json_encode([
                ['image_name' => $imageName, 'storage' => 'public'],
                ['image_name' => $imageName, 'storage' => 'public'],
            ]);

            $product = Product::create([
                'added_by' => 'admin',
                'user_id' => 1,
                'name' => $productData['en_name'],
                'code' => $productData['code'],
                'slug' => Str::slug($productData['en_name']),
                'category_ids' => (string)$categoryId,
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'sub_sub_category_id' => null,
                'brand_id' => $brandId,
                'unit' => 'piece',
                'product_type' => 'physical',
                'digital_product_type' => null,
                'details' => $productData['en_details'],
                'colors' => null,
                'choice_options' => null,
                'variation' => null,
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
                'featured_status' => $index < 5 ? 1 : 0,
                'featured' => $index < 5 ? 1 : 0,
                'published' => 1,
                'refundable' => 1,
                'free_shipping' => 0,
                'shipping_cost' => 25.00,
                'multiply_qty' => 0,
                'images' => $images,
                'thumbnail' => $thumbnailName,
                'thumbnail_storage_type' => 'public',
                'color_image' => null,
                'meta_title' => $productData['en_name'] . ' - Premium Ceramic Tiles',
                'meta_description' => $productData['en_details'],
                'meta_image' => $thumbnailName,
                'video_provider' => null,
                'video_url' => null,
                'attributes' => null,
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
        }
    }

    private function createBusinessSettings()
    {
        $settings = [
            [
                'type' => 'company_name',
                'value' => 'Saudi Ceramics Store',
            ],
            [
                'type' => 'company_email',
                'value' => 'info@saudiceramics.com',
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
    }
}
