# CeramicHome.sa Product Data Extraction Guide

## Overview
This guide helps you extract product data from ceramichome.sa and add it to your seeder.

## Method 1: Manual Extraction (Easiest)

### Step 1: Visit the Website
Go to: https://ceramichome.sa/en/products

### Step 2: Extract Product Information
For each product, collect:
- Product name (English & Arabic)
- Description (English & Arabic)
- Price
- Product code/SKU
- Image URL
- Category

### Step 3: Update the Seeder
1. Open `database/seeders/CeramicDemoSeeder.php`
2. Find the `$products` array in the `createProducts()` method
3. Replace or add products with real data from ceramichome.sa

## Method 2: Browser Extension (Recommended)

### Using Web Scraper Extension

1. **Install Web Scraper** (Chrome/Firefox extension)
   - Chrome: https://chrome.google.com/webstore/detail/web-scraper
   - Firefox: https://addons.mozilla.org/firefox/addon/web-scraper/

2. **Create a Sitemap**
   - Open Web Scraper on ceramichome.sa/products page
   - Create new sitemap
   - Add selectors for:
     - Product name
     - Price
     - Image URL
     - Description
     - Product code

3. **Export Data**
   - Run the scraper
   - Export as CSV or JSON
   - Convert to seeder format

### Using Data Miner Extension

1. Install Data Miner
2. Create a recipe for ceramichome.sa
3. Extract product fields
4. Export and format for seeder

## Method 3: Using the Helper Script

1. **Update the script**
   - Open `database/seeders/ceramichome_scraper.php`
   - Fill in the `$products` array with data from the website

2. **Run the script**
   ```bash
   php database/seeders/ceramichome_scraper.php
   ```

3. **Copy output**
   - The script will generate seeder-ready PHP code
   - Copy it into `CeramicDemoSeeder.php`

## Method 4: Browser Developer Tools

### Extract Product Data via Console

1. Open ceramichome.sa/en/products
2. Press F12 (Developer Tools)
3. Go to Console tab
4. Run JavaScript to extract data:

```javascript
// Example - adjust selectors based on actual page structure
const products = [];
document.querySelectorAll('.product-item').forEach((item, index) => {
    products.push({
        name_en: item.querySelector('.product-name')?.textContent || '',
        name_ar: item.querySelector('.product-name-ar')?.textContent || '',
        price: parseFloat(item.querySelector('.price')?.textContent.replace(/[^0-9.]/g, '') || 0),
        image_url: item.querySelector('img')?.src || '',
        code: item.querySelector('.sku')?.textContent || `PROD-${String(index + 1).padStart(3, '0')}`,
        description_en: item.querySelector('.description')?.textContent || '',
    });
});
console.log(JSON.stringify(products, null, 2));
```

5. Copy the JSON output
6. Convert to seeder format

## Product Data Structure

Each product should have:

```php
[
    'en_name' => 'Product Name in English',
    'ar_name' => 'اسم المنتج بالعربية',
    'en_details' => 'Product description in English...',
    'ar_details' => 'وصف المنتج بالعربية...',
    'unit_price' => 45.00,        // Selling price
    'purchase_price' => 30.00,   // Cost price (usually 60-70% of unit_price)
    'discount' => 10.00,          // Discount percentage
    'current_stock' => 500,       // Stock quantity
    'code' => 'PROD-001',         // Product SKU/code
]
```

## Image Handling

### Download Images

1. **Manual Download**
   - Right-click on product images
   - Save with naming: `{code-lowercase}.webp`
   - Place in: `storage/app/public/product/`

2. **Using the Script**
   - Update `image_url` in the scraper script
   - Run the script to auto-download

3. **Bulk Download**
   - Use browser extensions like "Image Downloader"
   - Or use wget/curl commands

## Categories Mapping

Map ceramichome.sa categories to your seeder categories:

- Floor Tiles → Floor Tiles
- Wall Tiles → Wall Tiles
- Bathroom Tiles → Bathroom Tiles
- Kitchen Tiles → Kitchen Tiles
- Porcelain Tiles → Porcelain Tiles

## Tips

1. **Start Small**: Extract 5-10 products first to test
2. **Verify Data**: Check prices, descriptions, and images
3. **Image Quality**: Ensure images are high quality (800x800px minimum)
4. **Translations**: If Arabic is missing, use translation tools
5. **Pricing**: Adjust prices to match your business model

## After Extraction

1. Update `CeramicDemoSeeder.php` with real data
2. Run the seeder: `php artisan db:seed --class=CeramicDemoSeeder`
3. Verify products in admin panel
4. Check images are displaying correctly

## Troubleshooting

**Images not showing?**
- Check file paths match seeder code
- Verify storage link: `php artisan storage:link`
- Check file permissions

**Products not creating?**
- Check database constraints
- Verify all required fields are filled
- Check for duplicate product codes

**Translation issues?**
- Ensure Arabic text is properly encoded (UTF-8)
- Check translation table entries
