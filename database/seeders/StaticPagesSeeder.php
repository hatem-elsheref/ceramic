<?php

namespace Database\Seeders;

use App\Models\BusinessPage;
use Illuminate\Database\Seeder;

class StaticPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates all static pages with Arabic and English content
     *
     * @return void
     */
    public function run()
    {
        echo "Creating static pages...\n\n";

        $pages = [
            [
                'slug' => 'about-us',
                'title_en' => 'About Us',
                'title_ar' => 'من نحن',
                'description_en' => '<div class="page-content">
                    <h2>Welcome to Premium Ceramics</h2>
                    <p>We are a leading provider of premium ceramic and porcelain tiles in Saudi Arabia. With years of experience in the industry, we offer the finest selection of tiles for residential and commercial projects.</p>
                    <h3>Our Mission</h3>
                    <p>To provide high-quality ceramic tiles and exceptional customer service to our clients across Saudi Arabia.</p>
                    <h3>Our Vision</h3>
                    <p>To become the most trusted ceramic tile supplier in the region, known for quality, innovation, and customer satisfaction.</p>
                    <h3>Why Choose Us?</h3>
                    <ul>
                        <li>Premium quality products from trusted manufacturers</li>
                        <li>Wide variety of designs and sizes</li>
                        <li>Competitive pricing</li>
                        <li>Fast and reliable delivery across Saudi Arabia</li>
                        <li>Expert customer support</li>
                    </ul>
                </div>',
                'description_ar' => '<div class="page-content">
                    <h2>مرحباً بكم في سيراميك ممتاز</h2>
                    <p>نحن مزود رائد لبلاط السيراميك والبورسلين الممتاز في المملكة العربية السعودية. مع سنوات من الخبرة في الصناعة، نقدم أفضل مجموعة من البلاط للمشاريع السكنية والتجارية.</p>
                    <h3>مهمتنا</h3>
                    <p>تقديم بلاط سيراميك عالي الجودة وخدمة عملاء استثنائية لعملائنا في جميع أنحاء المملكة العربية السعودية.</p>
                    <h3>رؤيتنا</h3>
                    <p>أن نصبح مزود بلاط السيراميك الأكثر ثقة في المنطقة، معروفين بالجودة والابتكار ورضا العملاء.</p>
                    <h3>لماذا تختارنا؟</h3>
                    <ul>
                        <li>منتجات عالية الجودة من مصنعين موثوقين</li>
                        <li>مجموعة واسعة من التصاميم والأحجام</li>
                        <li>أسعار تنافسية</li>
                        <li>توصيل سريع وموثوق في جميع أنحاء المملكة العربية السعودية</li>
                        <li>دعم عملاء متخصص</li>
                    </ul>
                </div>',
            ],
            [
                'slug' => 'terms-and-conditions',
                'title_en' => 'Terms and Conditions',
                'title_ar' => 'الشروط والأحكام',
                'description_en' => '<div class="page-content">
                    <h2>Terms and Conditions</h2>
                    <p>Please read these terms and conditions carefully before using our website and services.</p>
                    <h3>1. Acceptance of Terms</h3>
                    <p>By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement.</p>
                    <h3>2. Products and Services</h3>
                    <p>We reserve the right to modify, suspend, or discontinue any product or service at any time without prior notice.</p>
                    <h3>3. Pricing</h3>
                    <p>All prices are in Saudi Riyal (SAR) and are subject to change without notice. We reserve the right to correct any pricing errors.</p>
                    <h3>4. Orders</h3>
                    <p>All orders are subject to product availability. We reserve the right to refuse or cancel any order at our discretion.</p>
                    <h3>5. Payment</h3>
                    <p>Payment must be made in full at the time of order unless otherwise agreed. We accept various payment methods as displayed on our website.</p>
                    <h3>6. Delivery</h3>
                    <p>Delivery times are estimates and may vary. We are not responsible for delays caused by third-party delivery services.</p>
                    <h3>7. Returns and Refunds</h3>
                    <p>Please refer to our Return Policy and Refund Policy for details on returns and refunds.</p>
                </div>',
                'description_ar' => '<div class="page-content">
                    <h2>الشروط والأحكام</h2>
                    <p>يرجى قراءة هذه الشروط والأحكام بعناية قبل استخدام موقعنا الإلكتروني وخدماتنا.</p>
                    <h3>1. قبول الشروط</h3>
                    <p>من خلال الوصول إلى هذا الموقع واستخدامه، تقبل وتوافق على الالتزام بشروط وأحكام هذه الاتفاقية.</p>
                    <h3>2. المنتجات والخدمات</h3>
                    <p>نحتفظ بالحق في تعديل أو تعليق أو إيقاف أي منتج أو خدمة في أي وقت دون إشعار مسبق.</p>
                    <h3>3. التسعير</h3>
                    <p>جميع الأسعار بالريال السعودي (SAR) وقابلة للتغيير دون إشعار. نحتفظ بالحق في تصحيح أي أخطاء في التسعير.</p>
                    <h3>4. الطلبات</h3>
                    <p>جميع الطلبات خاضعة لتوفر المنتج. نحتفظ بالحق في رفض أو إلغاء أي طلب حسب تقديرنا.</p>
                    <h3>5. الدفع</h3>
                    <p>يجب أن يتم الدفع بالكامل في وقت الطلب ما لم يتم الاتفاق على خلاف ذلك. نقبل طرق دفع متنوعة كما هو موضح على موقعنا.</p>
                    <h3>6. التوصيل</h3>
                    <p>أوقات التوصيل تقديرية وقد تختلف. لسنا مسؤولين عن التأخيرات الناجمة عن خدمات التوصيل من طرف ثالث.</p>
                    <h3>7. الإرجاع والاسترداد</h3>
                    <p>يرجى الرجوع إلى سياسة الإرجاع وسياسة الاسترداد للحصول على تفاصيل حول الإرجاع والاسترداد.</p>
                </div>',
            ],
            [
                'slug' => 'privacy-policy',
                'title_en' => 'Privacy Policy',
                'title_ar' => 'سياسة الخصوصية',
                'description_en' => '<div class="page-content">
                    <h2>Privacy Policy</h2>
                    <p>We are committed to protecting your privacy. This Privacy Policy explains how we collect, use, and safeguard your personal information.</p>
                    <h3>1. Information We Collect</h3>
                    <p>We collect information that you provide directly to us, including:</p>
                    <ul>
                        <li>Name and contact information</li>
                        <li>Billing and shipping addresses</li>
                        <li>Payment information</li>
                        <li>Order history</li>
                    </ul>
                    <h3>2. How We Use Your Information</h3>
                    <p>We use the information we collect to:</p>
                    <ul>
                        <li>Process and fulfill your orders</li>
                        <li>Communicate with you about your orders</li>
                        <li>Send you marketing communications (with your consent)</li>
                        <li>Improve our services</li>
                    </ul>
                    <h3>3. Information Sharing</h3>
                    <p>We do not sell your personal information. We may share your information with service providers who assist us in operating our website and conducting our business.</p>
                    <h3>4. Data Security</h3>
                    <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
                    <h3>5. Your Rights</h3>
                    <p>You have the right to access, update, or delete your personal information at any time by contacting us.</p>
                </div>',
                'description_ar' => '<div class="page-content">
                    <h2>سياسة الخصوصية</h2>
                    <p>نحن ملتزمون بحماية خصوصيتك. توضح سياسة الخصوصية هذه كيفية جمع معلوماتك الشخصية واستخدامها وحمايتها.</p>
                    <h3>1. المعلومات التي نجمعها</h3>
                    <p>نجمع المعلومات التي تقدمها لنا مباشرة، بما في ذلك:</p>
                    <ul>
                        <li>الاسم ومعلومات الاتصال</li>
                        <li>عناوين الفواتير والشحن</li>
                        <li>معلومات الدفع</li>
                        <li>سجل الطلبات</li>
                    </ul>
                    <h3>2. كيفية استخدام معلوماتك</h3>
                    <p>نستخدم المعلومات التي نجمعها لـ:</p>
                    <ul>
                        <li>معالجة وتنفيذ طلباتك</li>
                        <li>التواصل معك بشأن طلباتك</li>
                        <li>إرسال رسائل تسويقية لك (بموافقتك)</li>
                        <li>تحسين خدماتنا</li>
                    </ul>
                    <h3>3. مشاركة المعلومات</h3>
                    <p>لا نبيع معلوماتك الشخصية. قد نشارك معلوماتك مع مقدمي الخدمات الذين يساعدوننا في تشغيل موقعنا وإدارة أعمالنا.</p>
                    <h3>4. أمان البيانات</h3>
                    <p>نطبق تدابير أمنية مناسبة لحماية معلوماتك الشخصية من الوصول غير المصرح به أو التعديل أو الكشف أو التدمير.</p>
                    <h3>5. حقوقك</h3>
                    <p>لديك الحق في الوصول إلى معلوماتك الشخصية أو تحديثها أو حذفها في أي وقت من خلال الاتصال بنا.</p>
                </div>',
            ],
            [
                'slug' => 'refund-policy',
                'title_en' => 'Refund Policy',
                'title_ar' => 'سياسة الاسترداد',
                'description_en' => '<div class="page-content">
                    <h2>Refund Policy</h2>
                    <p>We want you to be completely satisfied with your purchase. If you are not satisfied, we offer refunds under the following conditions:</p>
                    <h3>1. Eligibility for Refund</h3>
                    <p>To be eligible for a refund, the item must be:</p>
                    <ul>
                        <li>Unused and in its original packaging</li>
                        <li>Returned within 14 days of delivery</li>
                        <li>In the same condition as when you received it</li>
                    </ul>
                    <h3>2. Refund Process</h3>
                    <p>To request a refund:</p>
                    <ol>
                        <li>Contact our customer service team</li>
                        <li>Provide your order number and reason for return</li>
                        <li>We will provide return instructions</li>
                        <li>Once we receive and inspect the item, we will process your refund</li>
                    </ol>
                    <h3>3. Refund Method</h3>
                    <p>Refunds will be issued to the original payment method within 5-10 business days after we receive the returned item.</p>
                    <h3>4. Non-Refundable Items</h3>
                    <p>Custom orders, damaged items (due to customer misuse), and items returned after 14 days are not eligible for refund.</p>
                </div>',
                'description_ar' => '<div class="page-content">
                    <h2>سياسة الاسترداد</h2>
                    <p>نريدك أن تكون راضياً تماماً عن مشترياتك. إذا لم تكن راضياً، نقدم استرداداً في الحالات التالية:</p>
                    <h3>1. الأهلية للاسترداد</h3>
                    <p>لكي تكون مؤهلاً للاسترداد، يجب أن يكون العنصر:</p>
                    <ul>
                        <li>غير مستخدم وفي عبوته الأصلية</li>
                        <li>يتم إرجاعه خلال 14 يوماً من التسليم</li>
                        <li>بنفس الحالة التي استلمتها بها</li>
                    </ul>
                    <h3>2. عملية الاسترداد</h3>
                    <p>لطلب الاسترداد:</p>
                    <ol>
                        <li>اتصل بفريق خدمة العملاء لدينا</li>
                        <li>قدم رقم طلبك وسبب الإرجاع</li>
                        <li>سنقدم تعليمات الإرجاع</li>
                        <li>بمجرد استلامنا وفحص العنصر، سنعالج استردادك</li>
                    </ol>
                    <h3>3. طريقة الاسترداد</h3>
                    <p>سيتم إصدار الاسترداد إلى طريقة الدفع الأصلية خلال 5-10 أيام عمل بعد استلام العنصر المرتجع.</p>
                    <h3>4. العناصر غير القابلة للاسترداد</h3>
                    <p>الطلبات المخصصة والعناصر التالفة (بسبب سوء استخدام العميل) والعناصر المرتجعة بعد 14 يوماً غير مؤهلة للاسترداد.</p>
                </div>',
            ],
            [
                'slug' => 'return-policy',
                'title_en' => 'Return Policy',
                'title_ar' => 'سياسة الإرجاع',
                'description_en' => '<div class="page-content">
                    <h2>Return Policy</h2>
                    <p>We accept returns within 14 days of delivery for items that are unused and in their original packaging.</p>
                    <h3>1. Return Conditions</h3>
                    <ul>
                        <li>Items must be unused and in original condition</li>
                        <li>Original packaging must be intact</li>
                        <li>Return request must be made within 14 days</li>
                    </ul>
                    <h3>2. How to Return</h3>
                    <ol>
                        <li>Contact us to initiate a return</li>
                        <li>We will provide a return authorization number</li>
                        <li>Package the item securely in its original packaging</li>
                        <li>Ship the item back to us</li>
                    </ol>
                    <h3>3. Return Shipping</h3>
                    <p>Return shipping costs are the responsibility of the customer unless the item is defective or we made an error.</p>
                    <h3>4. Processing Time</h3>
                    <p>Once we receive your return, we will process it within 5-7 business days.</p>
                </div>',
                'description_ar' => '<div class="page-content">
                    <h2>سياسة الإرجاع</h2>
                    <p>نقبل الإرجاع خلال 14 يوماً من التسليم للعناصر غير المستخدمة وفي عبوتها الأصلية.</p>
                    <h3>1. شروط الإرجاع</h3>
                    <ul>
                        <li>يجب أن تكون العناصر غير مستخدمة وفي حالتها الأصلية</li>
                        <li>يجب أن تكون العبوة الأصلية سليمة</li>
                        <li>يجب تقديم طلب الإرجاع خلال 14 يوماً</li>
                    </ul>
                    <h3>2. كيفية الإرجاع</h3>
                    <ol>
                        <li>اتصل بنا لبدء الإرجاع</li>
                        <li>سنقدم رقم تفويض الإرجاع</li>
                        <li>قم بتعبئة العنصر بأمان في عبوته الأصلية</li>
                        <li>أرسل العنصر إلينا</li>
                    </ol>
                    <h3>3. شحن الإرجاع</h3>
                    <p>تكاليف شحن الإرجاع هي مسؤولية العميل ما لم يكن العنصر معيباً أو ارتكبنا خطأً.</p>
                    <h3>4. وقت المعالجة</h3>
                    <p>بمجرد استلام إرجاعك، سنعالجه خلال 5-7 أيام عمل.</p>
                </div>',
            ],
            [
                'slug' => 'cancellation-policy',
                'title_en' => 'Cancellation Policy',
                'title_ar' => 'سياسة الإلغاء',
                'description_en' => '<div class="page-content">
                    <h2>Cancellation Policy</h2>
                    <p>You may cancel your order before it is shipped. Once an order has been shipped, it cannot be cancelled and must be returned according to our Return Policy.</p>
                    <h3>1. Before Shipping</h3>
                    <p>Orders can be cancelled free of charge before they are shipped. Contact us immediately to cancel your order.</p>
                    <h3>2. After Shipping</h3>
                    <p>Once your order has been shipped, it cannot be cancelled. You may return the item according to our Return Policy.</p>
                    <h3>3. Custom Orders</h3>
                    <p>Custom or made-to-order items cannot be cancelled once production has begun.</p>
                    <h3>4. Refund for Cancelled Orders</h3>
                    <p>If you cancel an order before shipping, you will receive a full refund to your original payment method within 5-7 business days.</p>
                </div>',
                'description_ar' => '<div class="page-content">
                    <h2>سياسة الإلغاء</h2>
                    <p>يمكنك إلغاء طلبك قبل شحنه. بمجرد شحن الطلب، لا يمكن إلغاؤه ويجب إرجاعه وفقاً لسياسة الإرجاع الخاصة بنا.</p>
                    <h3>1. قبل الشحن</h3>
                    <p>يمكن إلغاء الطلبات مجاناً قبل شحنها. اتصل بنا فوراً لإلغاء طلبك.</p>
                    <h3>2. بعد الشحن</h3>
                    <p>بمجرد شحن طلبك، لا يمكن إلغاؤه. يمكنك إرجاع العنصر وفقاً لسياسة الإرجاع الخاصة بنا.</p>
                    <h3>3. الطلبات المخصصة</h3>
                    <p>لا يمكن إلغاء العناصر المخصصة أو المصنوعة حسب الطلب بمجرد بدء الإنتاج.</p>
                    <h3>4. الاسترداد للطلبات الملغاة</h3>
                    <p>إذا ألغيت طلباً قبل الشحن، ستحصل على استرداد كامل إلى طريقة الدفع الأصلية خلال 5-7 أيام عمل.</p>
                </div>',
            ],
            [
                'slug' => 'shipping-policy',
                'title_en' => 'Shipping Policy',
                'title_ar' => 'سياسة الشحن',
                'description_en' => '<div class="page-content">
                    <h2>Shipping Policy</h2>
                    <p>We ship to locations throughout Saudi Arabia. Shipping times and costs vary based on your location and order size.</p>
                    <h3>1. Shipping Areas</h3>
                    <p>We currently ship to all major cities and regions in Saudi Arabia.</p>
                    <h3>2. Shipping Time</h3>
                    <ul>
                        <li>Riyadh: 1-2 business days</li>
                        <li>Jeddah, Dammam: 2-3 business days</li>
                        <li>Other cities: 3-5 business days</li>
                    </ul>
                    <h3>3. Shipping Costs</h3>
                    <p>Shipping costs are calculated at checkout based on your location and order weight. Free shipping may be available for orders over a certain amount.</p>
                    <h3>4. Order Processing</h3>
                    <p>Orders are typically processed within 1-2 business days. You will receive a tracking number once your order ships.</p>
                    <h3>5. Delivery</h3>
                    <p>We use trusted shipping partners to ensure safe and timely delivery of your orders.</p>
                </div>',
                'description_ar' => '<div class="page-content">
                    <h2>سياسة الشحن</h2>
                    <p>نشحن إلى مواقع في جميع أنحاء المملكة العربية السعودية. تختلف أوقات وتكاليف الشحن حسب موقعك وحجم طلبك.</p>
                    <h3>1. مناطق الشحن</h3>
                    <p>نشحن حالياً إلى جميع المدن والمناطق الرئيسية في المملكة العربية السعودية.</p>
                    <h3>2. وقت الشحن</h3>
                    <ul>
                        <li>الرياض: 1-2 أيام عمل</li>
                        <li>جدة، الدمام: 2-3 أيام عمل</li>
                        <li>المدن الأخرى: 3-5 أيام عمل</li>
                    </ul>
                    <h3>3. تكاليف الشحن</h3>
                    <p>يتم حساب تكاليف الشحن عند الدفع حسب موقعك ووزن طلبك. قد يكون الشحن المجاني متاحاً للطلبات التي تتجاوز مبلغاً معيناً.</p>
                    <h3>4. معالجة الطلب</h3>
                    <p>عادة ما تتم معالجة الطلبات خلال 1-2 أيام عمل. ستحصل على رقم تتبع بمجرد شحن طلبك.</p>
                    <h3>5. التوصيل</h3>
                    <p>نستخدم شركاء شحن موثوقين لضمان التوصيل الآمن وفي الوقت المناسب لطلباتك.</p>
                </div>',
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($pages as $page) {
            $existing = BusinessPage::where('slug', $page['slug'])->first();
            
            // Determine description based on locale (for now, we'll store English as default)
            // The system can handle translations separately if needed
            $description = $page['description_en'];
            
            if ($existing) {
                $existing->update([
                    'title' => $page['title_en'],
                    'description' => $description,
                    'status' => 1,
                    'default_status' => 1,
                ]);
                $updated++;
                echo "✓ Updated: {$page['title_en']}\n";
            } else {
                BusinessPage::create([
                    'title' => $page['title_en'],
                    'slug' => $page['slug'],
                    'description' => $description,
                    'status' => 1,
                    'default_status' => 1,
                ]);
                $created++;
                echo "✓ Created: {$page['title_en']}\n";
            }
        }

        echo "\n✅ Static pages completed: $created created, $updated updated\n\n";
    }
}
