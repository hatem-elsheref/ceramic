# Quick Start - Production Setup

## Fresh Production Setup (Recommended)

### Step 1: Clear Old Products
```bash
php artisan db:seed --class=ClearProductsSeeder
```

### Step 2: Add Fresh Production Data
```bash
php artisan db:seed --class=ProductionCeramicSeeder
```

## What You Get

✅ **20 Professional Products** with:
- Arabic & English names and descriptions
- Realistic pricing (SAR)
- Stock quantities
- SEO tags
- Your brand (Premium Ceramics)

✅ **6 Categories** + 4 Sub-categories
✅ **Your Brand** (customizable)
✅ **Business Settings** for Saudi Arabia

## Customize Your Brand

Edit `ProductionCeramicSeeder.php`:
- Line ~150: Update brand name
- Line ~550: Update company information

## All Ahmed Elsallab References Removed ✅

- ❌ Ahmed Elsallab seeder - Deleted
- ❌ Ahmed Elsallab scraper - Deleted
- ❌ All scraper files - Cleaned up
- ✅ Fresh production seeder ready

## Next Steps

1. Run the seeders
2. Customize brand name
3. Upload real product images
4. Verify in admin panel
