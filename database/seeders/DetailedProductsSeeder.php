<?php
namespace Database\Seeders;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
class DetailedProductsSeeder extends Seeder
{
    public function run(): void
    {
        $labtop = Category::where('slug', 'labtop')->first();
        $gaming = Category::where('slug', 'gaming-pc')->first();
        $parts = Category::where('slug', 'onderdelen')->first();
        if (!$labtop) $labtop = Category::create(['name'=>'Labtop','slug'=>'labtop','status'=>true,'sort_order'=>1,'icon'=>'laptop','description'=>'Laptops']);
        if (!$gaming) $gaming = Category::create(['name'=>'Gaming PC','slug'=>'gaming-pc','status'=>true,'sort_order'=>2,'icon'=>'gamepad-2','description'=>'Gaming']);
        if (!$parts) $parts = Category::create(['name'=>'Onderdelen','slug'=>'onderdelen','status'=>true,'sort_order'=>3,'icon'=>'cpu','description'=>'Onderdelen']);
        $products = [
            [
                'category_id'=>$labtop->id,'title'=>'ASUS Vivobook 15 X1504ZA','slug'=>'asus-vivobook-15-x1504za','brand'=>'ASUS','sku'=>'ASUS-VIV15-001','price'=>699,'old_price'=>799,'discount_type'=>'percentage','discount_value'=>12.50,'discount_start_date'=>now()->subDays(5),'discount_end_date'=>now()->addDays(25),'stock_status'=>'in_stock','status'=>true,'is_featured'=>true,
                'features'=>[['title'=>'Processor','value'=>'Intel Core i5-1235U'],['title'=>'Werkgeheugen','value'=>'16GB DDR4 RAM'],['title'=>'Opslag','value'=>'512GB NVMe SSD'],['title'=>'Beeldscherm','value'=>'15.6" Full HD IPS']],
                'highlights'=>[['icon'=>'feather','title'=>'Lichtgewicht','subtitle'=>'Slechts 1.70 kg'],['icon'=>'zap','title'=>'Snelle SSD','subtitle'=>'512GB NVMe'],['icon'=>'keyboard','title'=>'Comfortabel typen','subtitle'=>'Verlicht toetsenbord'],['icon'=>'monitor','title'=>'Windows 11','subtitle'=>'Gebruiksklaar geleverd']],
                'colors'=>['Zilver','Blauw'],'sizes'=>[],'main_image'=>'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=800&q=80','gallery_images'=>['https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=800&q=80'],'delivery_time'=>'Voor 16:00 besteld, morgen verzonden','description'=>'<h3>Waarom kiezen voor de ASUS Vivobook 15?</h3><p>De ASUS Vivobook 15 combineert krachtige prestaties met een dun en licht ontwerp.</p><ul><li>15.6" Full HD IPS</li><li>Intel i5-1235U</li><li>16GB DDR4</li><li>512GB NVMe SSD</li></ul>',
            ],
            [
                'category_id'=>$labtop->id,'title'=>'HP 15s-fq5000 Intel Core i5','slug'=>'hp-15s-fq5000-i5','brand'=>'HP','sku'=>'HP-15S-002','price'=>599,'old_price'=>649,'discount_type'=>null,'discount_value'=>null,'discount_start_date'=>null,'discount_end_date'=>null,'stock_status'=>'in_stock','status'=>true,'is_featured'=>true,
                'features'=>[['title'=>'Processor','value'=>'Intel Core i5-1235U'],['title'=>'Werkgeheugen','value'=>'8GB DDR4 RAM'],['title'=>'Opslag','value'=>'512GB SSD'],['title'=>'Beeldscherm','value'=>'15.6" Full HD']],
                'highlights'=>[['icon'=>'feather','title'=>'Lichtgewicht','subtitle'=>'Slechts 1.70 kg'],['icon'=>'zap','title'=>'Snelle SSD','subtitle'=>'512GB'],['icon'=>'keyboard','title'=>'Comfortabel typen','subtitle'=>'Verlicht toetsenbord'],['icon'=>'monitor','title'=>'Windows 11','subtitle'=>'Gebruiksklaar geleverd']],
                'colors'=>['Zilver'],'sizes'=>[],'main_image'=>'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80','gallery_images'=>['https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=800&q=80'],'delivery_time'=>'Voor 16:00 besteld, morgen verzonden','description'=>'<h3>Betrouwbare HP kwaliteit</h3><p>Veelzijdige laptop voor dagelijks gebruik.</p><ul><li>Intel i5-1235U</li><li>8GB RAM</li><li>512GB SSD</li></ul>',
            ],
            [
                'category_id'=>$labtop->id,'title'=>'Lenovo IdeaPad 3 15ALC6 Ryzen 5','slug'=>'lenovo-ideapad-3-15alc6-ryzen-5','brand'=>'Lenovo','sku'=>'LENOVO-IP3-003','price'=>549,'old_price'=>null,'discount_type'=>'fixed','discount_value'=>50,'discount_start_date'=>now()->subDays(2),'discount_end_date'=>now()->addDays(10),'stock_status'=>'in_stock','status'=>true,'is_featured'=>false,
                'features'=>[['title'=>'Processor','value'=>'AMD Ryzen 5 5500U'],['title'=>'Werkgeheugen','value'=>'8GB RAM'],['title'=>'Opslag','value'=>'256GB SSD'],['title'=>'Beeldscherm','value'=>'15.6" Full HD']],
                'highlights'=>[['icon'=>'cpu','title'=>'AMD Kracht','subtitle'=>'6 cores, 12 threads'],['icon'=>'hard-drive','title'=>'256GB SSD','subtitle'=>'Snelle NVMe'],['icon'=>'wifi','title'=>'Wi-Fi 6','subtitle'=>'Bluetooth 5.1'],['icon'=>'battery','title'=>'Lange batterij','subtitle'=>'Tot 9 uur']],
                'colors'=>['Grijs','Blauw'],'sizes'=>[],'main_image'=>'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=800&q=80','gallery_images'=>['https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=800&q=80'],'delivery_time'=>'Voor 16:00 besteld, morgen verzonden','description'=>'<h3>AMD kracht voor elke dag</h3><p>Ryzen 5 5500U biedt uitstekende prijs/prestatie.</p>',
            ],
            [
                'category_id'=>$labtop->id,'title'=>'Lenovo Legion 5 Gen 11 i7 RTX 3050 Ti','slug'=>'lenovo-legion-5-gen-11-i7-rtx-3050-ti','brand'=>'Lenovo','sku'=>'LENOVO-LEG5-004','price'=>1299,'old_price'=>1499,'discount_type'=>'percentage','discount_value'=>13,'discount_start_date'=>now()->subDays(1),'discount_end_date'=>now()->addDays(15),'stock_status'=>'in_stock','status'=>true,'is_featured'=>true,
                'features'=>[['title'=>'Processor','value'=>'Intel Core i7-12700H'],['title'=>'Grafische kaart','value'=>'RTX 3050 Ti 4GB'],['title'=>'Werkgeheugen','value'=>'16GB DDR5 RAM'],['title'=>'Opslag','value'=>'512GB NVMe SSD'],['title'=>'Beeldscherm','value'=>'15.6" 165Hz Full HD']],
                'highlights'=>[['icon'=>'gamepad-2','title'=>'Gaming power','subtitle'=>'165Hz display'],['icon'=>'zap','title'=>'16GB DDR5','subtitle'=>'4800MHz'],['icon'=>'hard-drive','title'=>'512GB PCIe 4.0','subtitle'=>'Super snel'],['icon'=>'keyboard','title'=>'RGB toetsenbord','subtitle'=>'Verlicht']],
                'colors'=>['Zwart'],'sizes'=>[],'main_image'=>'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=800&q=80','gallery_images'=>['https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?auto=format&fit=crop&w=800&q=80'],'delivery_time'=>'Voor 16:00 besteld, morgen verzonden','description'=>'<h3>Gaming power met Legion 5</h3><p>Intel i7-12700H + RTX 3050 Ti + 165Hz.</p>',
            ],
            [
                'category_id'=>$labtop->id,'title'=>'Dell Inspiron 15 3520 i5 16GB','slug'=>'dell-inspiron-15-3520-i5-16gb','brand'=>'Dell','sku'=>'DELL-INS15-005','price'=>749,'old_price'=>849,'discount_type'=>null,'discount_value'=>null,'discount_start_date'=>null,'discount_end_date'=>null,'stock_status'=>'in_stock','status'=>true,'is_featured'=>false,
                'features'=>[['title'=>'Processor','value'=>'Intel Core i5-1235U'],['title'=>'Werkgeheugen','value'=>'16GB DDR4 RAM'],['title'=>'Opslag','value'=>'512GB SSD'],['title'=>'Beeldscherm','value'=>'15.6" Full HD WVA']],
                'highlights'=>[['icon'=>'shield-check','title'=>'Betrouwbaar','subtitle'=>'Dell kwaliteit'],['icon'=>'sun','title'=>'ComfortView','subtitle'=>'Anti-glare scherm'],['icon'=>'wifi','title'=>'Wi-Fi 6','subtitle'=>'Bluetooth 5.2'],['icon'=>'battery','title'=>'Lange accuduur','subtitle'=>'Tot 10 uur']],
                'colors'=>['Zilver','Carbon Zwart'],'sizes'=>[],'main_image'=>'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?auto=format&fit=crop&w=800&q=80','gallery_images'=>['https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=800&q=80'],'delivery_time'=>'Voor 16:00 besteld, morgen verzonden','description'=>'<h3>Dell Inspiron - betrouwbaar en stijlvol</h3><p>Solide bouwkwaliteit, ComfortView scherm.</p>',
            ],
            [
                'category_id'=>$gaming->id,'title'=>'MSI Infinite S3 i5 RTX 4060 Gaming PC','slug'=>'msi-infinite-s3-i5-rtx-4060','brand'=>'MSI','sku'=>'MSI-INF-S3-006','price'=>1299,'old_price'=>1499,'discount_type'=>'percentage','discount_value'=>13,'discount_start_date'=>now()->subDays(3),'discount_end_date'=>now()->addDays(20),'stock_status'=>'in_stock','status'=>true,'is_featured'=>true,
                'features'=>[['title'=>'Processor','value'=>'Intel Core i5-14400F'],['title'=>'Grafische kaart','value'=>'RTX 4060 8GB'],['title'=>'Werkgeheugen','value'=>'16GB DDR5 RAM'],['title'=>'Opslag','value'=>'1TB NVMe SSD']],
                'highlights'=>[['icon'=>'gamepad-2','title'=>'RTX 4060','subtitle'=>'8GB GDDR6'],['icon'=>'cpu','title'=>'i5-14400F','subtitle'=>'10 cores'],['icon'=>'hard-drive','title'=>'1TB NVMe','subtitle'=>'Super snel'],['icon'=>'zap','title'=>'Wi-Fi 6','subtitle'=>'RGB verlichting']],
                'colors'=>['Zwart'],'sizes'=>[],'main_image'=>'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80','gallery_images'=>['https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1591488320449-011701bb6704?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1614294149010-950b698f72c0?auto=format&fit=crop&w=800&q=80'],'delivery_time'=>'Binnen 2-3 werkdagen bezorgd','description'=>'<h3>MSI Infinite S3 - Ready to Game</h3><p>Intel i5-14400F en RTX 4060.</p>',
            ],
            [
                'category_id'=>$gaming->id,'title'=>'Corsair Vengeance i7400 i7 RTX 4070','slug'=>'corsair-vengeance-i7400-rtx-4070','brand'=>'Corsair','sku'=>'CORSAIR-VEN-007','price'=>1899,'old_price'=>2099,'discount_type'=>null,'discount_value'=>null,'discount_start_date'=>null,'discount_end_date'=>null,'stock_status'=>'in_stock','status'=>true,'is_featured'=>false,
                'features'=>[['title'=>'Processor','value'=>'Intel Core i7-14700F'],['title'=>'Grafische kaart','value'=>'RTX 4070 12GB'],['title'=>'Werkgeheugen','value'=>'32GB DDR5 RAM'],['title'=>'Opslag','value'=>'1TB NVMe + 2TB HDD']],
                'highlights'=>[['icon'=>'cpu','title'=>'i7-14700F','subtitle'=>'20 cores'],['icon'=>'monitor','title'=>'RTX 4070','subtitle'=>'12GB GDDR6'],['icon'=>'hard-drive','title'=>'3TB opslag','subtitle'=>'NVMe + HDD'],['icon'=>'zap','title'=>'850W Gold','subtitle'=>'Krachtige PSU']],
                'colors'=>['Zwart'],'sizes'=>[],'main_image'=>'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=800&q=80','gallery_images'=>['https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1614294149010-950b698f72c0?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80'],'delivery_time'=>'Binnen 2-3 werkdagen bezorgd','description'=>'<h3>Corsair Vengeance - High-End Gaming</h3><p>i7-14700F, 32GB DDR5 en RTX 4070.</p>',
            ],
            [
                'category_id'=>$parts->id,'title'=>'Kingston NV2 1TB NVMe PCIe 4.0 SSD','slug'=>'kingston-nv2-1tb-nvme-ssd','brand'=>'Kingston','sku'=>'KING-NV2-1TB-008','price'=>79,'old_price'=>99,'discount_type'=>'percentage','discount_value'=>20,'discount_start_date'=>now()->subDays(10),'discount_end_date'=>now()->addDays(30),'stock_status'=>'in_stock','status'=>true,'is_featured'=>false,
                'features'=>[['title'=>'Interface','value'=>'PCIe 4.0 x4 NVMe'],['title'=>'Leessnelheid','value'=>'3500MB/s Lezen'],['title'=>'Schrijfsnelheid','value'=>'2100MB/s Schrijven'],['title'=>'Formaat','value'=>'M.2 2280']],
                'highlights'=>[['icon'=>'hard-drive','title'=>'3500MB/s','subtitle'=>'Leessnelheid'],['icon'=>'zap','title'=>'NVMe 4.0','subtitle'=>'10x sneller dan SATA'],['icon'=>'package','title'=>'M.2 2280','subtitle'=>'Voor laptop & desktop'],['icon'=>'shield-check','title'=>'3 jaar garantie','subtitle'=>'Betrouwbaar']],
                'colors'=>[],'sizes'=>['1TB','2TB'],'main_image'=>'https://images.unsplash.com/photo-1591488320449-011701bb6704?auto=format&fit=crop&w=800&q=80','gallery_images'=>['https://images.unsplash.com/photo-1591488320449-011701bb6704?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1614294149010-950b698f72c0?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=800&q=80'],'delivery_time'=>'Voor 16:00 besteld, morgen verzonden','description'=>'<h3>Kingston NV2 1TB - Supersnelle Upgrade</h3><p>Tot 3500MB/s - 10x sneller dan SATA SSD.</p>',
            ],
        ];
        $localBase = public_path('assets/img/products');
        if (!is_dir($localBase)) @mkdir($localBase, 0755, true);
        $toLocal = function(string $url, string $slug, string $suffix = '') use ($localBase): string {
            if (!str_starts_with($url, 'http')) return $url;
            $hash = substr(md5($url), 0, 8);
            $filename = $slug . ($suffix !== '' ? '-' . $suffix : '') . '-' . $hash . '.jpg';
            $localPath = $localBase . DIRECTORY_SEPARATOR . $filename;
            $assetPath = 'assets/img/products/' . $filename;
            if (!file_exists($localPath)) {
                try {
                    $ctx = stream_context_create(['http'=>['timeout'=>15, 'header'=>'User-Agent: SlimmePC/1.0']]);
                    $data = @file_get_contents($url, false, $ctx);
                    if ($data !== false && strlen($data) > 1000) {
                        file_put_contents($localPath, $data);
                    } else {
                        return $url;
                    }
                } catch (\Throwable $e) { return $url; }
            }
            return $assetPath;
        };
        foreach ($products as $data) {
            $slug = $data['slug'];
            $data['main_image'] = $toLocal($data['main_image'], $slug, 'main');
            $newGallery = [];
            foreach (($data['gallery_images'] ?? []) as $idx => $g) {
                $newGallery[] = $toLocal($g, $slug, 'g'.$idx);
            }
            $data['gallery_images'] = $newGallery;
            Product::updateOrCreate(['slug'=>$slug], $data);
        }
        Product::whereIn('slug', ['hp-2026','laptop1'])->delete();
    }
}