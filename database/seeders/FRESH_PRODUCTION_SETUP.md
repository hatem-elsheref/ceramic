# Fresh Production Setup Guide

## Overview
This guide helps you set up a fresh, production-ready ceramic e-commerce store with your own brand.

## Step 1: Clear Existing Products

**Option A: Clear Only Products (Recommended)**
```bash
php artisan db:seed --class=ClearProductsSeeder
```

**Option B: Fresh Database (Complete Reset)**
```bash
php artisan migrate:fresh --seed
```

## Step 2: Run Production Seeder

```bash
php artisan db:seed --class=ProductionCeramicSeeder
```

Or add to `DatabaseSeeder.php`:
```php
$this->call([
    AdminRoleTable::class,
    AdminTable::class,
    SellerTableSeeder::class,
    ClearProductsSeeder::class,  // Clear old products first
    ProductionCeramicSeeder::class, // Add fresh production data
]);
```

## What Gets Created

### Categories (6 Main + 4 Sub)
- Floor Tiles (بلاط الأرضيات)
- Wall Tiles (بلاط الجدران)
- Bathroom Tiles (بلاط الحمامات)
- Kitchen Tiles (بلاط المطابخ)
- Porcelain Tiles (بلاط البورسلين)
- Outdoor Tiles (بلاط خارجي)

### Your Brand
- **Brand Name**: "Premium Ceramics" / "سيراميك ممتاز"
- **Status**: Active
- All products assigned to this brand

### Products (20 Products)
- All with Arabic and English names
- Complete descriptions in both languages
- Realistic pricing (SAR)
- Stock quantities
- SEO tags
- Product codes: PCT-001 to PCT-020

### Business Settings
- Company name: Premium Ceramics Store
- Email: info@premiumceramics.sa
- Phone: +966 11 123 4567
- Address: King Fahd Road, Riyadh, Saudi Arabia
- Timezone: Asia/Riyadh
- Currency: SAR

## Customizing Your Brand

Edit `ProductionCeramicSeeder.php` and update:

```php
// Line ~150
$brandNameEn = 'Your Brand Name';
$brandNameAr = 'اسم علامتك التجارية';
```

Also update business settings:
```php
// Line ~550
'company_name' => 'Your Company Name',
'company_email' => 'your@email.com',
```

## Image Setup

Products use placeholder images (`def.webp`) by default. To add real images:

1. **Upload via Admin Panel**
   - Go to `/admin/products/list`
   - Edit each product
   - Upload real images

2. **Manual Upload**
   - Save images as: `{code-lowercase}.webp` (e.g., `pct-001.webp`)
   - Place in: `storage/app/public/product/`
   - Thumbnails: `storage/app/public/product/thumbnail/`

3. **Run Fix Script** (if needed)
   ```bash
   php database/seeders/fix_product_images.php
   ```

## Files Removed

- ❌ `AhmedElsallabSeeder.php` - Removed
- ❌ `AhmedElsallabScraper.php` - Removed
- ✅ `ProductionCeramicSeeder.php` - New production seeder
- ✅ `ClearProductsSeeder.php` - Clear old products

## Verification

After running the seeder:

1. **Check Products**: `/admin/products/list`
2. **Check Categories**: `/admin/category/view`
3. **Check Brand**: `/admin/brand/view`
4. **Check Settings**: `/admin/business-settings/website-info`

## Production Checklist

- [ ] Clear old products
- [ ] Run production seeder
- [ ] Update brand name to yours
- [ ] Update company information
- [ ] Upload real product images
- [ ] Verify all products display correctly
- [ ] Test frontend product pages
- [ ] Check Arabic/English translations
- [ ] Verify SEO tags
- [ ] Test product search

## Notes

- All products are assigned to **your brand** (Premium Ceramics)
- No references to Ahmed Elsallab or any scraper
- All data is production-ready
- Images use placeholders until you upload real ones
- All products have proper SEO tags
- Bilingual support (Arabic/English) included
