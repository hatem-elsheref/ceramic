<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call([
              //ClearAllDataSeeder::class,  // ⚠️ WARNING: Uncomment to clear ALL data from ALL tables first
              ClearProductsSeeder::class,  // Uncomment to clear old products first
             AdminRoleTable::class,
             AdminTable::class,
             SellerTableSeeder::class,
             ProductionCeramicSeeder::class,  // Production-ready seeder with your brand
             BrandCategoryProductsSeeder::class,  // Add 10-15 products per brand and category
             UpdateFeaturedProductsSeeder::class,  // Update existing products to be featured
             StaticPagesSeeder::class,  // Static pages (About Us, Terms, Privacy, etc.)
             CategoryBrandImagesSeeder::class,  // Assign images to categories and brands
             HeroBannerSeeder::class,  // Hero section banners for home page
             AllBannersSeeder::class,  // All banner types with images
             CompleteSetupSeeder::class,  // Complete setup: Logo, Categories, Payments, Banners
             FixArabicTranslations::class,  // Fix Arabic translations for ceramic e-commerce
         ]);
    }
}
