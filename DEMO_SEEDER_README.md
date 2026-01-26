# Ceramic Demo Seeder - Saudi Arabia

This seeder creates comprehensive demo data for a ceramic tiles e-commerce store in Saudi Arabia with full Arabic and English support.

## What's Included

### 1. Categories (5 Main + 4 Sub-categories)
- **Main Categories:**
  - Floor Tiles (بلاط الأرضيات)
  - Wall Tiles (بلاط الجدران)
  - Bathroom Tiles (بلاط الحمامات)
  - Kitchen Tiles (بلاط المطابخ)
  - Porcelain Tiles (بلاط البورسلين)

- All categories include Arabic (SA) and English translations

### 2. Brands (5 Brands)
- Rak Ceramics (راك سيراميك)
- Porcelanosa (بورسلانوسا)
- VitrA (فيترا)
- Emser Tile (إيمسر تايل)
- Riyadh Ceramics (سيراميك الرياض)

- All brands include Arabic (SA) and English translations

### 3. Products (20 Products)
Each product includes:
- ✅ English and Arabic names
- ✅ English and Arabic descriptions
- ✅ Pricing (unit price, purchase price, discount)
- ✅ Stock quantities
- ✅ Product codes
- ✅ Category and brand assignments
- ✅ SEO tags (meta title, meta description)
- ✅ Product SEO data in ProductSeo table
- ✅ Featured status (first 5 products are featured)
- ✅ Tax settings (15% VAT)
- ✅ Shipping costs

**Product Examples:**
- Premium White Ceramic Floor Tile 60x60
- Elegant Marble Look Porcelain Tile 80x80
- Modern Geometric Wall Tile 30x60
- Bathroom Mosaic Tile Collection
- Kitchen Backsplash Ceramic Tile 25x40
- And 15 more...

### 4. Business Settings
- Company name: Saudi Ceramics Store
- Email: info@saudiceramics.com
- Phone: +966 11 123 4567
- Country: Saudi Arabia (SA)
- Address: King Fahd Road, Riyadh, Saudi Arabia
- Timezone: Asia/Riyadh
- Currency symbol position: right
- Business mode: single
- Default location: Riyadh coordinates

## How to Run

### Option 1: Run All Seeders
```bash
php artisan db:seed
```

### Option 2: Run Only Demo Seeder
```bash
php artisan db:seed --class=CeramicDemoSeeder
```

### Option 3: Fresh Migration with Seeders
```bash
php artisan migrate:fresh --seed
```

## Important Notes

### Images
- Products currently use placeholder images (`def.webp`)
- **To add real product images:**
  1. Upload images via the admin panel at `/admin/products/add`
  2. Or manually upload images to `public/storage/product/` directory
  3. Update product records with actual image filenames

### Translations
- All translations are stored in the `translations` table
- English is the default language (stored in main fields)
- Arabic (SA) translations are stored separately
- The system automatically displays the correct language based on user preference

### SEO Tags
- Each product has complete SEO data in the `product_seo` table
- Meta titles and descriptions are optimized for search engines
- SEO images are linked to product thumbnails

### Pricing
- Prices are in SAR (Saudi Riyal)
- All products include:
  - Unit price (selling price)
  - Purchase price (cost)
  - Discount percentage
  - Tax (15% VAT - standard in Saudi Arabia)
  - Shipping cost (25 SAR default)

## Customization

You can modify the seeder file at:
`database/seeds/CeramicDemoSeeder.php`

To customize:
- Product names and descriptions
- Prices and stock levels
- Categories and brands
- Business settings

## Verification

After running the seeder, verify the data:
1. Check categories: `/admin/category/view`
2. Check brands: `/admin/brand/view`
3. Check products: `/admin/product/list`
4. Check business settings: `/admin/business-settings/website-info`

## Support

For issues or questions about the seeder, refer to:
- Laravel Seeder Documentation
- Project documentation in `/README.md`
