# Ahmed Elsallab Product Scraper

This scraper extracts product data from Ahmed Elsallab's website and creates a seeder.

## How to Use

### Step 1: Run the Scraper

```bash
php database/seeders/AhmedElsallabScraper.php
```

The scraper will:
1. **Test with page 1 first** - Shows you an example product
2. **Ask for confirmation** - Proceed with all 9 pages?
3. **Scrape all pages** - Extracts products from pages 1-9
4. **Generate seeder data** - Creates `ahmedelsallab_products.php`
5. **Download images** - Optionally downloads product images

### Step 2: Run the Seeder

After scraping, run the seeder:

```bash
php artisan db:seed --class=AhmedElsallabSeeder
```

Or add it to `DatabaseSeeder.php`:

```php
$this->call([
    // ... other seeders
    AhmedElsallabSeeder::class,
]);
```

## What Gets Scraped

- **Product Name** (Arabic & English)
- **Description** (Arabic & English)
- **Price**
- **Product Code/SKU**
- **Product Images**
- **Category** (Ceramic Porcelain)

## Missing Data Handling

If any data is missing, the scraper will:
- Use **Faker** to generate realistic placeholder data
- Translate Arabic names to English
- Generate descriptions based on product type
- Create product codes (AES-0001, AES-0002, etc.)

## Output Files

- `ahmedelsallab_products.php` - Product data in PHP array format
- `ahmedelsallab_sample.html` - Sample HTML for debugging (if no products found)
- Product images in `storage/app/public/product/`

## Troubleshooting

### No Products Found

If the scraper can't find products:
1. Check `ahmedelsallab_sample.html` to see the HTML structure
2. Update the selectors in `extractProductData()` method
3. The website structure may have changed

### Images Not Downloading

- Check internet connection
- Verify image URLs are accessible
- Some images may be lazy-loaded (check `data-src` attribute)

### Rate Limiting

The scraper includes a 2-second delay between pages to be polite. If you get blocked:
- Increase the delay in `scrapeAllPages()` method
- Use a proxy
- Run during off-peak hours

## Customization

### Adjust Selectors

Edit the `extractProductData()` method to match the website's HTML structure:

```php
// Update these selectors based on actual HTML
$nameSelectors = [
    './/h2',
    './/h3',
    // Add more selectors here
];
```

### Add More Pages

Change the `$maxPages` parameter:

```php
$scraper->scrapeAllPages(15); // Scrape 15 pages instead of 9
```

### Custom Faker Data

The scraper uses Faker for missing data. You can customize:

```php
if ($this->faker) {
    $product['description_ar'] = $this->faker->sentence(15);
}
```

## Notes

- The scraper respects the website by including delays
- All data is saved locally before seeding
- You can review and edit `ahmedelsallab_products.php` before running the seeder
- Images are downloaded with product code as filename (e.g., `aes-0001.webp`)

## Example Output

After running, you'll see:

```
=== TESTING WITH PAGE 1 FIRST ===

Found products using selector: //div[contains(@class, "product")]
Found 20 potential product nodes
  ✓ Found 20 products on page 1
  Total products so far: 20

First product example:
Array
(
    [name_ar] => بلاط سيراميك أبيض
    [name_en] => White Ceramic Tile
    [price] => 45.50
    [code] => AES-0001
    ...
)

Proceed with scraping all pages? (y/n):
```
