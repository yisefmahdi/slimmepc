# تحليل نظام المتجر - قبل vs بعد التحسين | SlimmePC

> **المصدر:** `C:\laragon\www\slimmepc` (النظام القديم) + dump حقيقي `D:\sm 2026\slimmepc2026nieuwe\h_00094667_slimmepc.sql` (623,347 byte - 30-08-2026 MariaDB 10.11)  
> **الهدف:** المتجر جداوله لحالها منفصلة عن الـ CMS (`content_blocks` الـ 18 migration مالهم دخل) - هذا الملف يحلل المتجر فقط ويقترح سكيما محسنة قابلة للتوسع مع الحفاظ 100% على الداتا والمزامنة بدون نقصان.  
> **تاريخ التحليل:** 31-08-2026

---

## 1. الخلاصة التنفيذية

| البند | القديم (`slimmepc`) | الجديد المقترح (`slimmepc_2026`) |
|---|---|---|
| **عدد جداول المتجر** | 13 + 3 اشتراكات = 16 (من أصل 63 جدول كلي) | 16 نفسها + 1 جديد `plans` = 17 + تحسينات أعمدة |
| **حجم الداتا الحية (من dump)** | 113 منتج، 29 طلب، 50 فاتورة اشتراك، 56 مستخدم | نفس الداتا تُنقل كما هي (IDs محفوظة) |
| **قابلية التوسع** | ضعيفة: سعر واحد للاشتراك، لا `SKU`، ضريبة hardcoded | عالية: باقات متعددة، SKU، ضرائب مرنة، indexes |
| **المشاكل الحرجة** | 8 (انظر §3) | محلولة مع الحفاظ على التوافق |
| **المزامنة** | يدوية خطرة | سكربت idempotent + verification + rollback |

> **المبدأ الذهبي:** `CMS (content_blocks/content_meta) منفصل تماماً` - جداول المتجر تُنشأ بجانبه بلا أي `ALTER` لجداول CMS. إذا أردنا التحكم بمحتوى صفحة `/shop` (عناوين، بنرات) نضيف سكشن `page='shop'` في CMS لحاله.

---

## 2. الداتا الحية من السيرفر - إحصائيات دقيقة

تم تحليل `h_00094667_slimmepc.sql` مباشرة (63 `CREATE TABLE`):

| الجدول | عدد الصفوف | ملاحظات من الداتا |
|---|---|---|
| `users` | 56 | `klantnummer` نمط `SLP-######` و `SMP-ADMIN/SMP-TECH` للتقنيين، `role` = admin/user/technician |
| `categories` | 11 | `slug` UNIQUE |
| `products` | 113 | **113 منتج** موزعة على 11 فئة، `status` = 0/1، `stock_status` = in_stock 111 / out 2، خصم `percentage` 28 + `fixed` 1، كثير من `discount_start/end` NULL |
| `addresses` | 63 | `home_nummber` typo موجود في الداتا الحية، `type` billing/shipping |
| `orders` | 29 | 26 paid / 3 unpaid، كلها `payment_method=IDEAL`، `klantnummer` يتكرر `SLP-758665` + `SMP-TECH`، `order_status` completed/pending |
| `order_items` | 30 | متوسط 1.03 عنصر/طلب - أوامر صغيرة |
| `invoices` | 26 | `invoice_number` UNIQUE `SLP-uniqid` / `INV-`، `pdf_path` موجود |
| `coupons` | 6 | `code` UNIQUE |
| `coupon_user` | 14 | pivot بدون UNIQUE مركب |
| `favorites` | 3 | wishlist قليل الاستخدام |
| `license_codes` | 13 | `status` available/sold، `order_id` nullable |
| `memberships` | 4 | قليل جداً، `klantnummer` نمط `SMP-*` مختلف عن الطلبات، `total=30.00` ثابت |
| `subscription_invoices` | 50 | **50 فاتورة اشتراك** مقابل 4 عضويات فقط (متوسط 12.5 فاتورة/عضو = فواتير سنوية متكررة) |
| `subscription_settings` | 1 | سطر واحد `id=4, subscription_price=30.00` |
| `pc_categories` | - | فارغ في dump (لم يُحسب) |
| `purchase_sales_records` | 23 | هوامش شراء/بيع |
| `manual_invoices` | 24 | فواتير يدوية |
| `device_receipts` | 170 | إيصالات أجهزة - الأكبر |
| `technician_invoices` | 94 | فواتير فنيين كثيرة |
| `brands` | 8 | شعارات |
| `migrations` | 56 | عدد migrations المنفذة |

**المفاتيح والـ FKs في dump (23 FK):**
```
addresses.user_id -> users.id CASCADE
orders.user_id -> users.id CASCADE
orders.billing_address_id -> addresses.id SET NULL
orders.shipping_address_id -> addresses.id SET NULL
order_items.order_id -> orders.id CASCADE
order_items.product_id -> products.id SET NULL
products.category_id -> categories.id CASCADE
favorites.user_id/product_id -> CASCADE
license_codes.product_id -> CASCADE
invoices.order_id -> orders.id CASCADE
memberships.user_id -> users.id SET NULL
subscription_invoices.membership_id -> memberships.id SET NULL
coupon_user.* -> CASCADE
technician_* -> CASCADE
```
**UNIQUE constraints في dump:**
`categories_slug, products_slug, orders_order_number, invoices_invoice_number, subscription_invoices_invoice_number, memberships_klantnummer, users_email, users_klantnummer, coupons_code, pc_categories_name`

---

## 3. النظام القديم كما هو - السكيما الكاملة مع الموديل والعلاقات

### 3.1 الخريطة الكاملة (ERD نصي)
```
User (56) 1--N Order (29) 1--N OrderItem (30) N--1 Product (113) N--1 Category (11)
        | 1--N Favorite (3) N--1 Product
        | N--M Coupon (6) via coupon_user (14)
        | 1--1 Membership (4) 1--N SubscriptionInvoice (50)
        | 1--N Address (63) 1--N Order (billing/shipping)
        | 1--N Invoice (26) via Order
        | 1--N LicenseCode (13) via Product
        | 1--N DeviceReceipt (170)
        | 1--N TechnicianForm (1) 1--N TechnicianInvoice (94)

Category 1--N Product 1--N LicenseCode + Favorite + OrderItem
SubscriptionSetting (1 row price=30) ---implicit---> Membership (لا FK)
```

### 3.2 الجداول بالتفصيل مع الملفات والموديلات

#### `users` - `database/migrations/0001_01_01_000000_create_users_table.php:14` + `app/Models/User.php:1`
```sql
id PK, name, phone nullable, is_blocked bool default 0, house_number, street, postcode, city nullable
role enum(admin,user,technician) default user, email UNIQUE, klantnummer UNIQUE nullable (SLP- + mt_rand), email_verified_at, password, remember_token, timestamps
```
موديل `User.php:27` fillable + casts + boot يولد `klantnummer` + علاقات `hasMany Order, hasOne Membership, hasMany Favorite, belongsToMany Coupon, hasMany SubscriptionInvoice` (`User.php:71-97`)

#### `categories` - `2025_02_20_010103_create_categories_table.php:14` + `2025_07_29_180358_add_image_to_categories_table.php:14` + `app/Models/Category.php:1`
```sql
id PK, name, slug UNIQUE, status bool, image nullable, timestamps
```
موديل `Category.php:15` boot slug + `hasMany Product`

#### `products` - `2025_02_20_010450_create_products_table.php:14` + `2025_07_01_095929_add_download_links_to_products_table.php:14` + `app/Models/Product.php:1`
```sql
id PK, category_id FK CASCADE NOT NULL, slug UNIQUE, title, brand nullable, price 10,2, old_price 10,2 nullable
discount_type enum(percentage,fixed) nullable, discount_value 10,2 nullable, discount_start/end datetime nullable
stock_status enum(in_stock,out_of_stock) default in_stock, status bool default 1, description text nullable
features/colors/sizes/gallery_images JSON nullable, main_image nullable, external_link, delivery_time
download_32bit/64bit/manual_url nullable, timestamps
```
موديل `Product.php:28` accessor `getDiscountedPriceAttribute()` يحسب الخصم مع فحص التاريخ + boot slug + `belongsTo Category`  
**بيانات حية:** 113 منتج، 11 فئة، `delivery_time` و `external_link` مستخدمة قليلاً

#### `addresses` - `2025_03_01_221320_create_addresses_table.php:14` + `app/Models/Address.php:1`
```sql
id PK, user_id FK CASCADE nullable, street_address, city, postal_code, home_nummber (typo حي), country, phone_number, type enum(billing,shipping) default billing, timestamps
```
موديل `Address.php:9` fillable يحمل نفس typo + `belongsTo User`

#### `orders` - `2025_03_01_221330_create_orders_table.php:14` + `app/Models/Order.php:1`
```sql
id PK, order_number UNIQUE (ORD- + Str::random8), user_id FK CASCADE nullable, billing_address_id FK SET NULL nullable, shipping_address_id FK SET NULL nullable
klantnummer, customer_gender enum(man,vrouw) nullable, customer_type enum(private,business) default private, first_name, last_name, customer_email, customer_phone
order_status enum(pending,processing,shipped,completed,cancelled) default pending, subtotal 10,2, tax_percentage 5,2 default 21.00, tax_amount 10,2 default 0, discount_code varchar nullable, total_price 10,2, payment_status enum(paid,unpaid) default unpaid, payment_method enum(IDEAL,credit-card,PayPal) nullable, warranty_info text nullable, timestamps
```
موديل `Order.php:31` relations: `belongsTo User, hasMany OrderItem (items), belongsTo Coupon via discount_code->code, hasOne Invoice, belongsTo Address billing/shipping` + boot يولد `order_number` و `klantnummer` من `Auth::user` أو `SLP-` عشوائي

#### `order_items` - `2025_03_01_231839_create_order_items_table.php:14` + `app/Models/OrderItem.php:1`
```sql
id PK, order_id FK CASCADE, product_id FK SET NULL nullable, product_name snapshot, product_price 10,2 snapshot, quantity int, total_price 10,2, timestamps
```
موديل `OrderItem.php:37` boot `created/deleted` يعيد حساب `order.total_price = sum(product_price*quantity)`

#### `invoices` - `2025_03_05_160103_create_invoices_table.php:14` + `app/Models/Invoice.php:1`
```sql
id PK, order_id FK CASCADE, klantnummer, customer_name, customer_phone, street_address, home_number nullable, postal_code, city, invoice_number UNIQUE (SLP-uniqid), invoice_date date, payment_method string, subtotal 10,2, tax_percentage 5,2 default 21, tax_amount 10,2, total 10,2, company_details text, pdf_path nullable, timestamps
```

#### `coupons` - `2025_02_26_225900_create_coupons_table.php:14` + `2025_02_26_230930_create_coupon_user_table.php:14` + `app/Models/Coupon.php:1`
```sql
coupons: id PK, code UNIQUE, discount_type enum(percentage,fixed), discount_value 8,2, start/end date nullable, status bool default 1, usage_limit int default 1, user_id FK CASCADE nullable, used_at timestamp nullable, timestamps
coupon_user: id PK, user_id FK CASCADE, coupon_id FK CASCADE, timestamps -- لا UNIQUE مركب
```
موديل `Coupon.php:27` `isExpired(), isMaxedOut(), belongsToMany User`

#### `favorites` - `2025_04_13_101300_create_favorites_table.php:14` + `app/Models/Favorite.php:1`
```sql
id PK, user_id FK CASCADE, product_id FK CASCADE, timestamps -- لا UNIQUE مركب
```

#### `license_codes` - `2025_07_01_100238_create_license_codes_table.php:14` + `app/Models/LicenseCode.php:1`
```sql
id PK, product_id FK CASCADE unsignedBigInteger, code string, status enum(available,sold) default available, order_id unsignedBigInteger nullable (بدون FK في migration لكن FK في dump), timestamps
```

#### `memberships` - `2025_03_13_094755_create_memberships_table.php:14` + `app/Models/Membership.php:1`
```sql
id PK, user_id FK SET NULL nullable, klantnummer UNIQUE (SMP-*), customer_type enum(private,business), customer_gender enum(man,vrouw) nullable, name, customer_email, customer_phone/address/postcode/city nullable, start_date date, end_date date, total 10,2 nullable, payment_status enum(paid,unpaid,cancelled) default unpaid, payment_method enum(iDEAL,credit-card,PayPal) nullable, terms_accepted bool default 0, timestamps
```
موديل `Membership.php:9` `belongsTo User, hasMany SubscriptionInvoice`

#### `subscription_invoices` - `2025_03_13_232322_create_subscription_invoices_table.php:14` + `app/Models/SubscriptionInvoice.php:1`
```sql
id PK, membership_id FK SET NULL nullable, klantnummer, name, customer_email, customer_phone, customer_address, postcode, city, invoice_number UNIQUE, invoice_date date, payment_method enum(iDEAL) nullable, start/end date nullable, subtotal 10,2, tax_percentage 5,2 default 21, tax_amount 10,2, total 10,2, pdf_path nullable, timestamps
```

#### `subscription_settings` - `2025_03_15_151442_create_subscription_settings_table.php:14` + `app/Models/SubscriptionSetting.php:1`
```sql
id PK, subscription_price 10,2, timestamps -- سطر واحد فقط id=4 price=30.00 في dump
```

#### جداول أخرى متجر: `pc_categories/pc_components` + `purchase_sales_records` + `brands` (انظر §2)

### 3.3 السلة والدفع
* **لا يوجد جدول `carts`** - كله `session('cart')` في `app/Http/Controllers/Frontend/WebshopController.php:1` (`cart(), addToCart(), increase(), decrease(), removeFromCart()`)
* **الدفع:** `app/Http/Controllers/Frontend/MollieController.php:1` Mollie iDEAL + `config/mollie.php` + `config/services.php:mollie.key` من `MOLLIE_KEY` - webhook فيه مشاكل `ngrok` محفور + CSRF 419 (AUDIT)

---

## 4. مشاكل النظام القديم - 10 نقاط تمنع التوسع

| # | المشكلة | الدليل من dump/الكود | التأثير | الحل في الجديد |
|---|---|---|---|---|
| 1 | **اشتراك سعر واحد** | `subscription_settings` سطر واحد 30.00 `SubscriptionSetting.php:1` لا يوجد `plans` | لا يمكن عمل باقات شهري/سنوي/عائلي | جدول `plans` + `memberships.plan_id FK` |
| 2 | **حالة المخزون** | `products.stock_status enum(in_stock,out_of_stock)` | توافق مباشر | الإبقاء على `stock_status` متطابقاً مع النظام القديم |
| 3 | **لا SKU** | لا يوجد `sku` في `products` | لا تتبع مخزني حقيقي | `sku UNIQUE nullable` |
| 4 | **ضريبة hardcoded** | `tax_percentage default 21.00` مكرر 6 جداول (`orders`, `invoices`, `subscription_invoices`) | تغيير الضريبة = تعديل 6 جداول | جدول `tax_rates` أو `config` مركزي |
| 5 | **كوبون بدون FK حقيقي** | `orders.discount_code varchar` نص بدون FK + `coupon_user` بدون UNIQUE مركب `Coupon.php:1` | يمكن استخدام كوبون مرتين | `orders.coupon_id FK SET NULL` + `UNIQUE(user_id,coupon_id)` |
| 6 | **السلة Session فقط** | `WebshopController session('cart')` | فقدان السلة بتغيير جهاز، لا تتبع سلة مهجورة | جدول `carts/cart_items` (اختياري مستقبلاً) |
| 7 | **typo حي `home_nummber`** | `addresses.home_nummber` في migration و dump و `Address.php:9` | كود هش، بحث صعب | عمود جديد `home_number` مع ETL ينقل من القديم |
| 8 | **أرقام عشوائية قابلة للتكرار** | `Order.php:63 Str::random8` + `Invoice.php:49 uniqid()` | تصادم محتمل | `ULID` أو `sequence` + UNIQUE check |
| 9 | **لا indexes كافية** | فقط PK + UNIQUE + FK indexes - لا `INDEX(category_id,status)` أو `INDEX(payment_status)` | بطء بحث/فلترة | indexes مركبة جديدة |
| 10 | **Mollie webhook مكسور** | `MollieController:63 ngrok` + لا CSRF exception + لا signature verify | دفع يفشل في الإنتاج | إصلاح webhook + queue |

---

## 5. النظام الجديد المحسن - سكيما قابلة للتوسع (منفصلة عن CMS)

> **موقعها:** `C:\laragon\www\slimmepc2026nieuwe\slimmepc\database\migrations\2026_09_01_0000*` - بجانب الـ 18 migration الموجودة `2026_08_11_*` بدون لمسها

### 5.1 المبادئ
1. **فصل تام:** كل جداول المتجر مشتركة `FK` فيما بينها فقط، لا `FK` إلى `content_blocks` أو العكس.
2. **حفظ IDs:** كل `id` قديم يُحفظ كما هو لضمان بقاء الروابط الخارجية والفواتير PDF.
3. **توافق عكسي:** الأعمدة القديمة تبقى + أعمدة محسنة جديدة nullable (لا كسر للكود القديم).
4. **قابلية التوسع:** `plans`, `sku`, `coupon_id`, `indexes` جديدة.

### 5.2 السكيما الجديدة المقترحة (13+1 جدول)

#### `categories` - `2026_09_01_000001_create_categories_table.php`
```sql
id PK AUTO, name varchar(255) NOT NULL, slug varchar(255) UNIQUE NOT NULL, status bool NOT NULL, image varchar(255) nullable, sort_order int default 0, timestamps
INDEX(status), UNIQUE(slug)
```
*توزيع:* `INSERT SELECT id, name, slug, status, image, 0, created_at, updated_at FROM old.categories` (11 صف)

#### `products` - `2026_09_01_000002_create_products_table.php`
```sql
id PK AUTO, category_id FK->categories CASCADE NOT NULL, slug UNIQUE NOT NULL, title NOT NULL, brand nullable, sku varchar(64) UNIQUE nullable, -- جديد
price decimal(10,2) NOT NULL, old_price nullable, discount_type enum, discount_value, discount_start/end datetime nullable,
stock_status enum(in_stock,out_of_stock) default in_stock, status bool default 1, description text nullable,
features/colors/sizes/gallery_images JSON nullable (CHECK json_valid), main_image, gallery_images, external_link, delivery_time,
download_32bit_url, download_64bit_url, manual_url nullable, timestamps
INDEX(category_id), INDEX(status, stock_status), INDEX(brand), INDEX(price), FULLTEXT(title, description) -- جديد للأداء
```
*توزيع:* `id, category_id, slug, title, brand, sku=NULL, price, old_price, discount_*, stock_status, status, ...` (113 صف) - `status` القديم 0 يبقى 0

#### `addresses` - `2026_09_01_000003_create_addresses_table.php`
```sql
id PK AUTO, user_id FK CASCADE nullable, street_address NOT NULL, city, postal_code, home_number varchar(255) NOT NULL, -- مصحح
home_nummber varchar(255) GENERATED ALWAYS AS (home_number) STORED nullable -- للتوافق أو تركه nullable, country, phone_number, type enum(billing,shipping) default billing, is_default bool default 0, timestamps
INDEX(user_id), INDEX(type)
```
*توزيع:* `home_number = home_nummber` من القديم (63 صف)

#### `coupons` - `2026_09_01_000004_create_coupons_table.php`
```sql
id PK, code UNIQUE NOT NULL, discount_type enum, discount_value 8,2, start/end date nullable, status bool default 1, usage_limit int default 1, user_id FK CASCADE nullable, used_at timestamp nullable, used_count int default 0, timestamps
UNIQUE(code), INDEX(status), INDEX(end_date)
```
*توزيع:* 6 صفوف مباشرة

#### `coupon_user` - `2026_09_01_000005_create_coupon_user_table.php`
```sql
id PK, user_id FK CASCADE, coupon_id FK CASCADE, timestamps, UNIQUE(user_id, coupon_id), INDEX(coupon_id)
```
*توزيع:* 14 صف، مع `INSERT IGNORE` لتجنب التكرار

#### `plans` - **جديد** `2026_09_01_000006_create_plans_table.php`
```sql
id PK AUTO, name varchar(255) NOT NULL, slug varchar(255) UNIQUE NOT NULL, description text nullable, price decimal(10,2) NOT NULL, billing_cycle enum(monthly,yearly) default yearly, features JSON nullable, is_active bool default 1, sort_order int default 0, timestamps
INDEX(is_active), UNIQUE(slug)
```
*توزيع:* سطر واحد من `old.subscription_settings`: `INSERT INTO plans (id, name, slug, price) VALUES (1, 'Jaarabonnement', 'jaarabonnement', 30.00)` - قابل لإضافة `Maandelijks 5.00` لاحقاً بدون تعديل سكيما

#### `memberships` - `2026_09_01_000007_create_memberships_table.php`
```sql
id PK AUTO, user_id FK SET NULL nullable, plan_id FK->plans SET NULL nullable, -- جديد
klantnummer UNIQUE NOT NULL, customer_type enum(private,business) default private, customer_gender enum(man,vrouw) nullable, name NOT NULL, customer_email NOT NULL, customer_phone/address/postcode/city nullable, start_date date NOT NULL, end_date date NOT NULL, total 10,2 nullable, payment_status enum(paid,unpaid,cancelled) default unpaid, payment_method enum(iDEAL,credit-card,PayPal) nullable, terms_accepted bool default 0, timestamps
INDEX(user_id), INDEX(plan_id), INDEX(payment_status), INDEX(end_date), UNIQUE(klantnummer)
```
*توزيع:* 4 صفوف `plan_id=1` لكل سطر قديم + نفس `klantnummer` (SMP-*)

#### `subscription_invoices` - `2026_09_01_000008_create_subscription_invoices_table.php`
```sql
id PK AUTO, membership_id FK SET NULL nullable, klantnummer NOT NULL, name, customer_email, customer_phone, customer_address, postcode, city, invoice_number UNIQUE NOT NULL, invoice_date date NOT NULL, payment_method enum(iDEAL) nullable, start/end date nullable, subtotal 10,2, tax_percentage 5,2 default 21, tax_amount 10,2, total 10,2, pdf_path nullable, timestamps
INDEX(membership_id), INDEX(invoice_date), UNIQUE(invoice_number)
```
*توزيع:* 50 صف `membership_id` محفوظ كما هو (معظمها NULL في dump - نحتفظ بNULL)

#### `orders` - `2026_09_01_000009_create_orders_table.php`
```sql
id PK AUTO, order_number UNIQUE NOT NULL, user_id FK CASCADE nullable, billing_address_id FK SET NULL nullable, shipping_address_id FK SET NULL nullable,
klantnummer NOT NULL, customer_gender enum, customer_type enum, first_name, last_name, customer_email NOT NULL, customer_phone NOT NULL,
order_status enum(pending,processing,shipped,completed,cancelled) default pending, subtotal 10,2, tax_percentage 5,2 default 21, tax_amount 10,2, discount_code varchar nullable, coupon_id FK->coupons SET NULL nullable, -- جديد
total_price 10,2, payment_status enum(paid,unpaid) default unpaid, payment_method enum(IDEAL,credit-card,PayPal) nullable, warranty_info text nullable, timestamps
INDEX(user_id), INDEX(order_status), INDEX(payment_status), INDEX(customer_email), UNIQUE(order_number)
```
*توزيع:* 29 صف، `coupon_id = (SELECT id FROM coupons WHERE code=discount_code)` - إذا NULL يبقى NULL، `billing/shipping_address_id` محفوظ 28/28 كما في dump

#### `order_items` - `2026_09_01_000010_create_order_items_table.php`
```sql
id PK AUTO, order_id FK CASCADE NOT NULL, product_id FK SET NULL nullable, product_name NOT NULL, product_price 10,2 NOT NULL, quantity int NOT NULL, total_price 10,2 NOT NULL, timestamps
INDEX(order_id), INDEX(product_id)
```
*توزيع:* 30 صف مباشرة

#### `invoices` - `2026_09_01_000011_create_invoices_table.php`
```sql
id PK AUTO, order_id FK CASCADE NOT NULL, klantnummer NOT NULL, customer_name, customer_phone, street_address, home_number nullable, postal_code, city, invoice_number UNIQUE NOT NULL, invoice_date date NOT NULL, payment_method string NOT NULL, subtotal 10,2, tax_percentage 5,2 default 21, tax_amount 10,2, total 10,2, company_details text NOT NULL, pdf_path nullable, timestamps
INDEX(order_id), UNIQUE(invoice_number)
```
*توزيع:* 26 صف

#### `favorites` - `2026_09_01_000012_create_favorites_table.php`
```sql
id PK AUTO, user_id FK CASCADE NOT NULL, product_id FK CASCADE NOT NULL, timestamps, UNIQUE(user_id, product_id) -- جديد لمنع التكرار
INDEX(product_id)
```
*توزيع:* 3 صف `INSERT IGNORE`

#### `license_codes` - `2026_09_01_000013_create_license_codes_table.php`
```sql
id PK AUTO, product_id FK CASCADE NOT NULL, code varchar(255) NOT NULL, status enum(available,sold) default available, order_id FK->orders SET NULL nullable, -- مصحح من unsignedBigInteger بدون FK
timestamps, INDEX(product_id), INDEX(status), INDEX(order_id)
```
*توزيع:* 13 صف

#### ملاحظة: `pc_categories/pc_components, purchase_sales_records, manual_invoices, device_receipts` تُنقل أيضاً بنفس المبدأ في migrations منفصلة `2026_09_01_00001*` إذا أردت - لكنها خارج نطاق المتجر الأساسي.

### 5.3 ERD الجديد المحسن
```
User 1--N Order 1--N OrderItem N--1 Product N--1 Category
     | 1--N Address 1--N Order (billing/shipping SET NULL)
     | N--M Coupon via coupon_user UNIQUE(user,coupon)
     | 1--N Favorite N--1 Product UNIQUE(user,product)
     | 1--1 Membership N--1 Plan (jaarabonnement) 1--N SubscriptionInvoice
     | 1--N Order N--1 Coupon (coupon_id SET NULL) - جديد
Product 1--N LicenseCode N--1 Order SET NULL
Product.sku UNIQUE, Product.stock_status
Plan 1--N Membership - جديد يحل سعر واحد
```

---

## 6. خطة المزامنة بدون فقدان - خطوة بخطوة

### 6.1 القاعدة الذهبية
* **لا `migrate:fresh` أبداً** - فقط `migrate`
* **لا `DELETE` قبل `INSERT`** - فقط `INSERT ... ON DUPLICATE KEY UPDATE` أو `INSERT IGNORE`
* **حفظ `id` القديم** كما هو (لا نترك AUTO_INCREMENT يولد جديد)
* **الترتيب حسب FK** إجباري

### 6.2 الأوامر العملية

```bash
# 1. Backup (قبل أي شيء)
mysqldump -u root h_00094667_slimmepc > "D:\sm 2026\backup_slimmepc_$(date +%Y%m%d_%H%M).sql"
mysqldump -u root slimmepc_2026 > "D:\sm 2026\backup_2026_$(date +%Y%m%d_%H%M).sql"

# 2. إضافة connection ثانٍ في C:\laragon\www\slimmepc2026nieuwe\slimmepc\config\database.php
'old' => ['driver'=>'mysql','host'=>'127.0.0.1','database'=>'h_00094667_slimmepc','username'=>'root','password'=>'', ...]

# 3. إنشاء migrations الجديدة في slimmepc_2026 ثم
php artisan migrate --force

# 4. تشغيل سكربت المزامنة (انظر §6.3)
php artisan shop:sync --from=old --dry-run   # معاينة
php artisan shop:sync --from=old --execute   # تنفيذ فعلي

# 5. Verification
php artisan shop:verify  # يقارن COUNT(*) و CHECKSUM

# 6. Rollback إذا لزم
mysql -u root slimmepc_2026 < backup_2026.sql
```

### 6.3 سكربت `app/Console/Commands/SyncShopData.php` (idempotent)

```php
// Pseudocode - الترتيب الدقيق:
SET FOREIGN_KEY_CHECKS=0;
DB::connection('old')->table('categories')->orderBy('id')->chunk(100, function($rows){
  foreach($rows as $r) DB::table('categories')->updateOrInsert(['id'=>$r->id], [...]);
});
// products: stock_status كما هي ; sku = null
// addresses: home_number = r->home_nummber
// coupons, coupon_user (INSERT IGNORE)
// plans: updateOrInsert(['id'=>1], ['name'=>'Jaarabonnement','slug'=>'jaarabonnement','price'=>DB::connection('old')->table('subscription_settings')->value('subscription_price')])
// memberships: plan_id=1
// subscription_invoices: membership_id كما هو
// orders: coupon_id = DB::table('coupons')->where('code', r->discount_code)->value('id')
// order_items, invoices, favorites (INSERT IGNORE), license_codes
SET FOREIGN_KEY_CHECKS=1;
```

**معالجة الحالات الخاصة:**
* `users.email UNIQUE` - إذا `email` موجود مسبقاً في الجديد: `updateOrInsert(['email'=>$r->email], [... + klantnummer القديم])` - لا نولد جديد
* `users.klantnummer UNIQUE` - فحص قبل الإدخال، إذا تضارب نضيف لاحقة `-OLD`
* `products.slug UNIQUE` - محفوظ كما هو `hp-pavilion-...`
* `orders.order_number UNIQUE` - محفوظ `ORD-UM8OMAKI` كما هو
* كل `timestamps` محفوظة `created_at` الأصلية

### 6.4 التحقق الآلي (`shop:verify`)

```sql
-- يجب أن تكون 0 وإلا هناك فقدان
SELECT 'users' as tbl, (SELECT COUNT(*) FROM old.users) as old_cnt, (SELECT COUNT(*) FROM slimmepc_2026.users) as new_cnt
UNION ALL SELECT 'products', (SELECT COUNT(*) FROM old.products), (SELECT COUNT(*) FROM slimmepc_2026.products)
UNION ALL SELECT 'orders', (SELECT COUNT(*) FROM old.orders), (SELECT COUNT(*) FROM slimmepc_2026.orders)
-- ... لكل جدول

-- فحص أيتام FK
SELECT * FROM slimmepc_2026.order_items WHERE order_id NOT IN (SELECT id FROM slimmepc_2026.orders); -- يجب 0
SELECT * FROM slimmepc_2026.products WHERE category_id NOT IN (SELECT id FROM slimmepc_2026.categories); -- يجب 0

-- CHECKSUM
CHECKSUM TABLE slimmepc_2026.products, slimmepc_2026.orders;
```

### 6.5 المزامنة المستقبلية (Incremental)
* بعد المزامنة الأولى، أي طلب جديد في القديم يُنقل بـ `WHERE id > last_synced_id` أو `WHERE updated_at > last_sync_time` - cron كل 5 دقائق أو webhook.

---

## 7. الملفات والموديلات الجديدة المقترحة

| الملف | الوصف |
|---|---|
| `database/migrations/2026_09_01_000001_create_categories_table.php` | نسخة محسنة من `C:\laragon\www\slimmepc\database\migrations\2025_02_20_010103_create_categories_table.php:14` |
| `...000002_create_products_table.php` | محسن من `2025_02_20_010450_create_products_table.php:14` + `2025_07_01_095929_add_download_links_to_products_table.php:14` |
| `...000003_create_addresses_table.php` | مصحح typo `home_number` |
| `...000006_create_plans_table.php` | **جديد** يحل `subscription_settings` |
| `...000007_create_memberships_table.php` | محسن `plan_id` |
| `app/Models/Plan.php` | موديل جديد `hasMany Membership` |
| `app/Models/Product.php` | يضاف `sku` + casts + `belongsTo Category` |
| `app/Models/Membership.php` | يضاف `belongsTo Plan` |
| `app/Models/Order.php` | يضاف `belongsTo Coupon coupon_id` |
| `app/Console/Commands/SyncShopData.php` | سكربت المزامنة idempotent |
| `app/Console/Commands/VerifyShopData.php` | فحص COUNT/CHECKSUM |

---

## 8. الخطوات القادمة (اقتراح تنفيذ)

1. **موافقتك على السكيما المحسنة** (هل تريد `sku/plans/coupon_id` أم نسخة طبق الأصل؟)
2. إنشاء الـ 13 migration في `slimmepc_2026` + تشغيل `migrate`
3. كتابة `SyncShopData.php` + `VerifyShopData.php`
4. تجربة `dry-run` على dump الحالي (113 منتج/29 طلب) + verification
5. مزامنة حية من السيرفر `h_00094667_slimmepc` إلى `slimmepc_2026` + نسخ ملفات `pdf_path` و `public/assets`
6. إبقاء `h_00094667_slimmepc.sql` كـ archive 30 يوم

> **ملاحظة CMS:** إذا أردت لاحقاً التحكم بمحتوى صفحة المتجر (عنوان، وصف، بنر) نضيف `ContentBlockSeeder` سكشن `page='shop'` منفصل - لا يمس جداول المنتجات.

---

**انتهى التحليل - جاهز للتنفيذ عند موافقتك على §5.2 (§5.2 = السكيما المحسنة المقترحة).**
