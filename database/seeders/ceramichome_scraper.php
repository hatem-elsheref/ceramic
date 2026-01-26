<?php
/**
 * CeramicHome.sa Product Data Extractor
 * 
 * This script helps extract product information from ceramichome.sa
 * 
 * Usage:
 * 1. Visit https://ceramichome.sa/en/products in your browser
 * 2. Open browser developer tools (F12)
 * 3. Copy product data from the page
 * 4. Update the $products array below with real data
 * 5. Run: php ceramichome_scraper.php
 * 
 * Alternative: Use browser extensions like "Web Scraper" or "Data Miner"
 */

// Product data structure from ceramichome.sa
// Update this array with actual product data from the website
$products = [
    // Example structure - replace with real data
    [
        'name_en' => '',
        'name_ar' => '',
        'description_en' => '',
        'description_ar' => '',
        'price' => 0,
        'image_url' => '',
        'category' => '',
        'code' => '',
    ],
];

/**
 * Extract product data from ceramichome.sa
 * 
 * You can use this function with actual scraping tools or browser automation
 */
function extractProductData($url) {
    // Note: This is a template. You'll need to implement actual scraping
    // Recommended tools:
    // - Goutte (PHP web scraper)
    // - Puppeteer (Node.js)
    // - Selenium (Browser automation)
    // - Browser extensions (Web Scraper, Data Miner)
    
    $html = @file_get_contents($url);
    if (!$html) {
        return null;
    }
    
    // Parse HTML and extract product information
    // This is a placeholder - implement based on actual HTML structure
    return [
        'name' => '',
        'price' => 0,
        'image' => '',
        'description' => '',
    ];
}

/**
 * Download product images
 */
function downloadProductImage($url, $savePath) {
    if (empty($url)) {
        return false;
    }
    
    $ch = curl_init($url);
    $fp = fopen($savePath, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    
    return $httpCode == 200 && file_exists($savePath);
}

/**
 * Generate seeder data format
 */
function generateSeederData($products) {
    $seederData = [];
    
    foreach ($products as $index => $product) {
        $code = $product['code'] ?? 'PROD-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
        $codeLower = strtolower($code);
        
        $seederData[] = [
            'en_name' => $product['name_en'] ?? '',
            'ar_name' => $product['name_ar'] ?? '',
            'en_details' => $product['description_en'] ?? '',
            'ar_details' => $product['description_ar'] ?? '',
            'unit_price' => $product['price'] ?? 0,
            'purchase_price' => ($product['price'] ?? 0) * 0.7, // 30% margin
            'discount' => $product['discount'] ?? 0,
            'current_stock' => $product['stock'] ?? 100,
            'code' => $code,
            'image_url' => $product['image_url'] ?? '',
            'category' => $product['category'] ?? '',
        ];
        
        // Download image if URL provided
        if (!empty($product['image_url'])) {
            $baseDir = __DIR__ . '/../../storage/app/public/product/';
            $thumbDir = $baseDir . 'thumbnail/';
            
            if (!is_dir($baseDir)) {
                mkdir($baseDir, 0755, true);
            }
            if (!is_dir($thumbDir)) {
                mkdir($thumbDir, 0755, true);
            }
            
            $imagePath = $baseDir . $codeLower . '.webp';
            $thumbPath = $thumbDir . $codeLower . '-thumb.webp';
            
            if (downloadProductImage($product['image_url'], $imagePath)) {
                echo "✓ Downloaded image for $code\n";
                // Create thumbnail (simplified - copy for now)
                if (file_exists($imagePath)) {
                    copy($imagePath, $thumbPath);
                }
            }
        }
    }
    
    return $seederData;
}

/**
 * Output PHP array format for seeder
 */
function outputSeederFormat($seederData) {
    echo "\n// Generated Seeder Data\n";
    echo "// Copy this into CeramicDemoSeeder.php\n\n";
    
    echo "[\n";
    foreach ($seederData as $product) {
        echo "    [\n";
        echo "        'en_name' => '" . addslashes($product['en_name']) . "',\n";
        echo "        'ar_name' => '" . addslashes($product['ar_name']) . "',\n";
        echo "        'en_details' => '" . addslashes($product['en_details']) . "',\n";
        echo "        'ar_details' => '" . addslashes($product['ar_details']) . "',\n";
        echo "        'unit_price' => " . $product['unit_price'] . ",\n";
        echo "        'purchase_price' => " . $product['purchase_price'] . ",\n";
        echo "        'discount' => " . ($product['discount'] ?? 0) . ",\n";
        echo "        'current_stock' => " . $product['current_stock'] . ",\n";
        echo "        'code' => '" . $product['code'] . "',\n";
        echo "    ],\n";
    }
    echo "];\n";
}

// Main execution
echo "CeramicHome.sa Product Data Extractor\n";
echo "=====================================\n\n";

if (empty($products) || empty($products[0]['name_en'])) {
    echo "⚠ No product data found.\n\n";
    echo "To use this script:\n";
    echo "1. Visit https://ceramichome.sa/en/products\n";
    echo "2. Extract product information manually or using browser tools\n";
    echo "3. Update the \$products array in this file\n";
    echo "4. Run the script again\n\n";
    echo "Recommended browser extensions:\n";
    echo "- Web Scraper (Chrome/Firefox)\n";
    echo "- Data Miner\n";
    echo "- Scraper (Chrome)\n";
} else {
    echo "Processing " . count($products) . " products...\n\n";
    $seederData = generateSeederData($products);
    outputSeederFormat($seederData);
    echo "\n✓ Done! Copy the output above into your seeder file.\n";
}
