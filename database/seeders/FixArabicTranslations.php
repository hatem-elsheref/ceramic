<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class FixArabicTranslations extends Seeder
{
    /**
     * Run the database seeds.
     * Fixes Arabic translations for ceramic e-commerce context
     *
     * @return void
     */
    public function run()
    {
        echo "Fixing Arabic translations...\n\n";
        
        $filePath = resource_path('lang/sa/messages.php');
        
        if (!File::exists($filePath)) {
            echo "❌ Translation file not found: {$filePath}\n";
            return;
        }
        
        // Read the file
        $content = File::get($filePath);
        
        // Define fixes - key => proper Arabic translation
        $fixes = [
            // E-commerce core terms
            '"i_Accept" => "i Accept"' => '"i_Accept" => "أوافق"',
            '"my_cart" => "my cart"' => '"my_cart" => "سلة التسوق"',
            '"shopping_cart" => "عربة التسوق"' => '"shopping_cart" => "سلة التسوق"',
            '"cart" => "عربة"' => '"cart" => "سلة التسوق"',
            '"featured_products" => "feature products"' => '"featured_products" => "المنتجات المميزة"',
            '"latest_products" => "أحدث المنتجات"' => '"latest_products" => "أحدث المنتجات"',
            '"best_selling_product" => "best sell product"' => '"best_selling_product" => "الأكثر مبيعاً"',
            '"top_rated_product" => "top rated product"' => '"top_rated_product" => "الأعلى تقييماً"',
            '"discounted_products" => "Discounted products"' => '"discounted_products" => "المنتجات المخفضة"',
            '"flash_deal" => "flash deal"' => '"flash_deal" => "عروض سريعة"',
            '"Flash_Deal_Products" => "Flash Deal Products"' => '"Flash_Deal_Products" => "عروض سريعة"',
            '"clearance_Sale" => "Clear Sale"' => '"clearance_Sale" => "تخفيضات"',
            
            // Cart & Checkout
            '"item_has_been_removed_from_cart" => "item has been removed from cart"' => '"item_has_been_removed_from_cart" => "تم إزالة المنتج من السلة"',
            '"Product_added_to_wishlist" => "Product added to wishlist"' => '"Product_added_to_wishlist" => "تم إضافة المنتج إلى قائمة الأمنيات"',
            '"proceed_to_Checkout" => "الشروع في الخروج"' => '"proceed_to_Checkout" => "المتابعة للدفع"',
            '"proceed_to_checkout" => "الشروع في الخروج"' => '"proceed_to_checkout" => "المتابعة للدفع"',
            '"continue_Shopping" => "continue Shopping"' => '"continue_Shopping" => "متابعة التسوق"',
            '"order_note" => "order note"' => '"order_note" => "ملاحظات الطلب"',
            '"sub_total" => "sub total"' => '"sub_total" => "المجموع الفرعي"',
            '"discount_on_product" => "Discount on product"' => '"discount_on_product" => "خصم على المنتج"',
            '"total_price" => "Total price"' => '"total_price" => "السعر الإجمالي"',
            
            // Product terms
            '"product" => "منتج"' => '"product" => "منتج"',
            '"products" => "منتجات"' => '"products" => "منتجات"',
            '"Product_Type" => "نوع المنتج"' => '"Product_Type" => "نوع المنتج"',
            '"Out_of_stock" => "إنتهى من المخزن"' => '"Out_of_stock" => "نفدت الكمية"',
            '"out_of_stock" => "إنتهى من المخزن"' => '"out_of_stock" => "نفدت الكمية"',
            '"this_product_is_low_on_stock" => "this product is low on stock"' => '"this_product_is_low_on_stock" => "هذا المنتج كمية محدودة"',
            
            // Category & Brand
            '"category" => "فئة"' => '"category" => "فئة"',
            '"categories" => "فئات"' => '"categories" => "فئات"',
            '"brand" => "ماركة"' => '"brand" => "علامة تجارية"',
            '"brands" => "العلامات التجارية"' => '"brands" => "العلامات التجارية"',
            '"all_Categories" => "all Categories"' => '"all_Categories" => "جميع الفئات"',
            '"all_Brands" => "all Brands"' => '"all_Brands" => "جميع العلامات التجارية"',
            '"Search_Categories" => "Search Categories"' => '"Search_Categories" => "البحث في الفئات"',
            '"Search_Brands" => "Search Brands"' => '"Search_Brands" => "البحث في العلامات التجارية"',
            '"Find_your_favorite_categories_and_products" => "ابحث عن الفئات والمنتجات المفضلة لديك"' => '"Find_your_favorite_categories_and_products" => "ابحث عن الفئات والمنتجات المفضلة لديك"',
            '"Find_your_favourite_brands_and_products" => "ابحث عن العلامات التجارية والمنتجات المفضلة لديك"' => '"Find_your_favourite_brands_and_products" => "ابحث عن العلامات التجارية والمنتجات المفضلة لديك"',
            
            // Search & Filter
            '"Search_for_items..." => "بحث عن العناصر..."' => '"Search_for_items..." => "ابحث عن المنتجات..."',
            '"search_for_items" => "search for items"' => '"search_for_items" => "ابحث عن المنتجات"',
            '"items_found" => "items found"' => '"items_found" => "منتج موجود"',
            '"sort_by" => "Sort by"' => '"sort_by" => "ترتيب حسب"',
            '"Sort_By" => "فرز بواسطة"' => '"Sort_By" => "ترتيب حسب"',
            '"Filter_By" => "تصفية بواسطة"' => '"Filter_By" => "تصفية حسب"',
            '"Best_Selling" => "الأكثر مبيعاً"' => '"Best_Selling" => "الأكثر مبيعاً"',
            '"Top_Rated" => "Top Rated"' => '"Top_Rated" => "الأعلى تقييماً"',
            '"Most_Favorite" => "الأكثر المفضلة"' => '"Most_Favorite" => "الأكثر تفضيلاً"',
            '"search_by_brands" => "search by brands"' => '"search_by_brands" => "البحث حسب العلامات التجارية"',
            
            // Order & Tracking
            '"track_order" => "Track order"' => '"track_order" => "تتبع الطلب"',
            '"track_Order_Result" => "Track Order Result"' => '"track_Order_Result" => "نتيجة تتبع الطلب"',
            '"order_id" => "order id"' => '"order_id" => "رقم الطلب"',
            '"Order_id" => "معرف الطلب"' => '"Order_id" => "رقم الطلب"',
            '"orders" => "طلبات"' => '"orders" => "الطلبات"',
            '"order_management" => "order management"' => '"order_management" => "إدارة الطلبات"',
            
            // Payment & Shipping
            '"shipping" => "شحن"' => '"shipping" => "الشحن"',
            '"please_select_a_payment_Methods" => "يرجى تحديد أ طرق الدفع"' => '"please_select_a_payment_Methods" => "يرجى اختيار طريقة الدفع"',
            '"account_&_shipping_info" => "account & shipping info"' => '"account_&_shipping_info" => "معلومات الحساب والشحن"',
            
            // UI Elements
            '"view_more" => "view more"' => '"view_more" => "عرض المزيد"',
            '"view_all" => "view all"' => '"view_all" => "عرض الكل"',
            '"View_All" => "عرض الكل"' => '"View_All" => "عرض الكل"',
            '"click_to_view" => "click to view"' => '"click_to_view" => "انقر للعرض"',
            '"No_Data_Found" => "No Data Found"' => '"No_Data_Found" => "لا توجد بيانات"',
            '"no_data_found" => "no data found"' => '"no_data_found" => "لا توجد بيانات"',
            
            // Common phrases
            '"home" => "بيت"' => '"home" => "الرئيسية"',
            '"wish_list" => "Wish list"' => '"wish_list" => "قائمة الأمنيات"',
            '"rating" => "تصنيف"' => '"rating" => "التقييم"',
            '"reviews" => "المراجعات"' => '"reviews" => "المراجعات"',
            '"contact_us" => "اتصل بنا"' => '"contact_us" => "اتصل بنا"',
            '"contact_Us" => "اتصل بنا"' => '"contact_Us" => "اتصل بنا"',
            
            // Error messages
            '"sorry_stock_limit_exceeded" => "آسف المخزون الحد تجاوز"' => '"sorry_stock_limit_exceeded" => "عذراً، تم تجاوز الحد المتاح من المخزون"',
            '"sorry_the_minimum_order_quantity_does_not_match" => "آسف the minimum order quantity does not match"' => '"sorry_the_minimum_order_quantity_does_not_match" => "عذراً، الكمية لا تطابق الحد الأدنى للطلب"',
            '"minimum_order_quantity_cannot_be_less_than_" => "الحد الأدنى لكمية الطلب لا يمكن أن يكون أقل من "' => '"minimum_order_quantity_cannot_be_less_than_" => "الحد الأدنى لكمية الطلب لا يمكن أن يكون أقل من "',
            '"there_is_not_enough_quantity_on_stock" => "لا يوجد كمية كافية في المخزون"' => '"there_is_not_enough_quantity_on_stock" => "لا توجد كمية كافية في المخزون"',
            '"There_is_not_enough_quantity_on_stock" => "لا توجد كمية كافية في المخزون"' => '"There_is_not_enough_quantity_on_stock" => "لا توجد كمية كافية في المخزون"',
        ];
        
        $fixed = 0;
        foreach ($fixes as $old => $new) {
            if (strpos($content, $old) !== false) {
                $content = str_replace($old, $new, $content);
                $fixed++;
                echo "✓ Fixed: " . substr($old, 0, 50) . "...\n";
            }
        }
        
        // Write back to file
        File::put($filePath, $content);
        
        echo "\n✅ Fixed {$fixed} translations\n";
        echo "   File: {$filePath}\n\n";
    }
}
