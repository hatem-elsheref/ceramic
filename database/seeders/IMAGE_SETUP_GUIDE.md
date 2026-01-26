# Image Setup Guide for Ceramic Demo Seeder

## How to Add Real Product Images

### Step 1: Download Images from RAK Ceramics Website

1. Visit: https://www.rakceramics.com/ksa/ar/
2. Browse products and download images for each product
3. Save images with descriptive names matching product codes

### Step 2: Upload Images to Storage

Images should be placed in:
- **Product Images**: `storage/app/public/product/`
- **Thumbnails**: `storage/app/public/product/thumbnail/`
- **Meta Images**: `storage/app/public/product/meta/`

### Step 3: Update Image References in Seeder

Each product in the seeder has a `code` field. Update the image filenames to match:

**Product Codes:**
- CFT-001 → `cft-001.webp`
- PFT-002 → `pft-002.webp`
- WWT-003 → `wwt-003.webp`
- BMT-004 → `bmt-004.webp`
- KBT-005 → `kbt-005.webp`
- WLT-006 → `wlt-006.webp`
- BFT-007 → `bft-007.webp`
- DPT-008 → `dpt-008.webp`
- ASB-009 → `asb-009.webp`
- SLT-010 → `slt-010.webp`
- SWT-011 → `swt-011.webp`
- GAM-012 → `gam-012.webp`
- TFT-013 → `tft-013.webp`
- HWT-014 → `hwt-014.webp`
- WBT-015 → `wbt-015.webp`
- CLT-016 → `clt-016.webp`
- BFT-017 → `bft-017.webp`
- FPT-018 → `fpt-018.webp`
- PPT-019 → `ppt-019.webp`
- MLT-020 → `mlt-020.webp`

### Step 4: Image Naming Convention

For each product, you need:
1. **Main product image**: `{code}.webp` (e.g., `cft-001.webp`)
2. **Thumbnail**: `{code}-thumb.webp` (e.g., `cft-001-thumb.webp`)
3. **Meta image**: Same as thumbnail

### Step 5: Run the Seeder

After uploading images, run:
```bash
php artisan db:seed --class=CeramicDemoSeeder
```

## Alternative: Use Placeholder Images

If you don't have real images yet, the seeder will use `def.webp` as a placeholder. You can replace these later via the admin panel.

## Quick Image Download Script

You can use browser extensions or tools like:
- **Image Downloader** browser extension
- **wget** or **curl** for command-line downloads
- **Python script** with requests/BeautifulSoup (requires permission)

## Notes

- Images should be in WebP format for best performance
- Recommended size: 800x800px for product images, 300x300px for thumbnails
- Ensure images are optimized for web use
