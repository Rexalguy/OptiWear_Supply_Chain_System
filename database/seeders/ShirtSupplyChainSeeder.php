<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\{
    User,
    RawMaterialCategory,
    RawMaterial,
    Product,
    ShirtCategory,
    BillOfMaterial,
    Workforce,
    ProductionOrder,
    ProductionStage,
    Order,
    OrderItem,
    VendorOrder,
    VendorOrderItem,
    RawMaterialsPurchaseOrder,
    ChatMessage,
};

class ShirtSupplyChainSeeder extends Seeder
{
    public function run(): void
    {
        // USERS
        $users = collect([
            [
                'name' => 'Admin Opio',
                'email' => 'admin@optiwear.ug',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'date_of_birth' => '1980-03-15',
                'gender' => 'male',
                'tokens' => 0,
            ],
            [
                'name' => 'David Okello',
                'email' => 'manufacturer@optiwear.ug',
                'password' => Hash::make('password123'),
                'role' => 'manufacturer',
                'date_of_birth' => '1985-07-12',
                'gender' => 'male',
                'tokens' => 0,
            ],
            [
                'name' => 'Sarah Nabwire',
                'email' => 'supplier@optiwear.ug',
                'password' => Hash::make('password123'),
                'role' => 'supplier',
                'date_of_birth' => '1988-11-20',
                'gender' => 'female',
                'tokens' => 0,
            ],
            [
                'name' => 'Moses Mugisha',
                'email' => 'vendor@optiwear.ug',
                'password' => Hash::make('password123'),
                'role' => 'vendor',
                'date_of_birth' => '1990-05-08',
                'gender' => 'male',
                'tokens' => 0,
            ],
            [
                'name' => 'Faith Namukasa',
                'email' => 'faith@optiwear.ug',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'date_of_birth' => '1996-02-11',
                'gender' => 'female',
                'tokens' => 0,
            ],
            [
                'name' => 'Brian Ouma',
                'email' => 'brian@optiwear.ug',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'date_of_birth' => '1994-06-03',
                'gender' => 'male',
                'tokens' => 0,
            ],
            [
                'name' => 'Patricia Adoch',
                'email' => 'patricia@optiwear.ug',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'date_of_birth' => '1991-09-19',
                'gender' => 'female',
                'tokens' => 0,
            ],
            [
                'name' => 'Edgar Kaggwa',
                'email' => 'edgar@optiwear.ug',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'date_of_birth' => '1993-12-27',
                'gender' => 'male',
                'tokens' => 0,
            ],
            [
                'name' => 'Diana Nakato',
                'email' => 'diana@optiwear.ug',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'date_of_birth' => '1995-04-23',
                'gender' => 'female',
                'tokens' => 0,
            ],
        ])->map(fn($data) => User::create($data));

        // INFO TABLES
        

            DB::table('admin_info')->insert([
                'user_id' => $users[0]->id,
                'title' => 'System Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('manufacturing_info')->insert([
                'user_id' => $users[1]->id,
                'factory_name' => 'Okello Textiles Ltd',
                'location' => 'Kampala',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('supplier_info')->insert([
                'user_id' => $users[2]->id,
                'company_name' => 'Nabwire Supplies',
                'location' => 'Jinja',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('vendor_info')->insert([
                'user_id' => $users[3]->id,
                'business_name' => 'Mugisha Designs',
                'location' => 'Masaka',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('customer_info')->insert([
                ['user_id' => $users[4]->id, 'address' => 'Ntinda, Kampala', 'created_at' => now(), 'updated_at' => now()],
                ['user_id' => $users[5]->id, 'address' => 'Gulu Road, Lira', 'created_at' => now(), 'updated_at' => now()],
                ['user_id' => $users[6]->id, 'address' => 'Bugolobi, Kampala', 'created_at' => now(), 'updated_at' => now()],
                ['user_id' => $users[7]->id, 'address' => 'Entebbe', 'created_at' => now(), 'updated_at' => now()],
                ['user_id' => $users[8]->id, 'address' => 'Mbarara Town', 'created_at' => now(), 'updated_at' => now()],
            ]);


        // RAW MATERIAL CATEGORIES
            $categoryData = [
                ['name' => 'Cotton Fabrics', 'description' => 'High-quality cotton used for making shirts.'],
                ['name' => 'Thread & Stitching Materials', 'description' => 'Threads and materials used for sewing.'],
                ['name' => 'Dyes & Chemicals', 'description' => 'Colorants and chemicals used in production.'],
                ['name' => 'Packaging Materials', 'description' => 'Used to package finished shirt products.'],
                ['name' => 'Buttons & Zippers', 'description' => 'Closures like buttons and zippers.'],
                ['name' => 'Printing Inks', 'description' => 'Inks used in shirt screen printing.'],
                ['name' => 'Adhesives & Glues', 'description' => 'Bonding agents used during manufacturing.'],
            ];

            $categories = collect($categoryData)->map(function ($data) {
                return RawMaterialCategory::firstOrCreate(
                    ['name' => $data['name']],
                    ['description' => $data['description']]
                );
            });

        // RAW MATERIALS
        $rawMaterialsData = [
            ['Cotton White Fabric', 'High-quality cotton', 4500, 'Cotton Fabrics', 15000, 3000, 800, 'meters'],
            ['Cotton Black Fabric', 'Used for darker shirts', 4600, 'Cotton Fabrics', 12000, 2800, 700, 'meters'],
            ['Thread Spools White', 'Stitching thread', 500, 'Thread & Stitching Materials', 8000, 1500, 400, 'pieces'],
            ['Thread Spools Black', 'Thread for dark garments', 520, 'Thread & Stitching Materials', 7000, 1300, 350, 'pieces'],
            ['Red Dye', 'Red color dye', 3000, 'Dyes & Chemicals', 3500, 800, 200, 'ml'],
            ['Blue Dye', 'Blue dye for patterns', 3100, 'Dyes & Chemicals', 3200, 750, 180, 'ml'],
            ['Plastic Bags', 'Packaging', 300, 'Packaging Materials', 9000, 2000, 600, 'pieces'],
            ['Boxes', 'Cardboard boxes for bulk', 1000, 'Packaging Materials', 5000, 1000, 300, 'pieces'],
            ['Buttons Small', 'Shirt buttons small', 150, 'Buttons & Zippers', 8000, 1500, 400, 'pieces'],
            ['Buttons Large', 'Shirt buttons large', 200, 'Buttons & Zippers', 7000, 1400, 350, 'pieces'],
            ['Black Ink', 'Screen printing', 2200, 'Printing Inks', 4000, 1000, 350, 'ml'],
            ['Red Ink', 'For red shirt prints', 2300, 'Printing Inks', 3800, 950, 320, 'ml'],
            ['Fabric Adhesive', 'Bonding material', 1800, 'Adhesives & Glues', 3200, 800, 300, 'ml'],
            ['Zippers Short', 'For kids shirts', 600, 'Buttons & Zippers', 3000, 800, 250, 'pieces'],
            ['Zippers Long', 'Industrial shirt zippers', 900, 'Buttons & Zippers', 2000, 700, 200, 'pieces'],
            ['Glow Ink', 'For high-visibility printing', 2500, 'Printing Inks', 2200, 600, 180, 'ml'],
        ];

        $rawMaterials = [];
        foreach ($rawMaterialsData as [$name, $desc, $price, $catName, $stock, $reorder, $alert, $unit]) {
            $category = $categories->firstWhere('name', $catName);
            $rawMaterials[] = RawMaterial::create([
                'name' => $name,
                'description' => $desc,
                'price' => $price,
                'supplier_id' => $users->where('role', 'supplier')->first()->id,
                'category_id' => $category->id,
                'current_stock' => $stock,
                'reorder_level' => $reorder,
                'alert_threshold' => $alert,
                'unit_of_measure' => $unit,
            ]);
        }

        // WORKFORCES (jobs lowercase and valid enums)
        $workforceNames = [
            'printing' => ['Sam', 'Joseph', 'Peter', 'Robert', 'Brian'],
            'packaging' => ['Esther', 'Grace', 'Beatrice', 'Doreen', 'Milly'],
            'delivery' => ['John', 'Michael', 'Paul', 'James', 'Tom'],
        ];

        foreach ($workforceNames as $job => $names) {
            foreach ($names as $name) {
                Workforce::create([
                    'name' => $name . ' ' . ucfirst($job),
                    'job' => $job,
                    'is_available' => true,
                ]);
            }
        }

        // PRODUCTS & SHIRT CATEGORIES with actual names
        $categoryProductNames = [
            'Casual Wear' => ['Polo Tee', 'Henley Shirt', 'V-Neck Shirt', 'Basic Tee', 'Striped Shirt', 'Round Neck Tee', 'Oversized Tee', 'Short Sleeve Tee'],
            'Formal Wear' => ['Business Shirt', 'Oxford Shirt', 'Spread Collar Shirt', 'Tuxedo Shirt', 'Classic Dress Shirt', 'French Cuff Shirt', 'Slim Fit Shirt', 'Pleated Front Shirt'],
            'Sportswear' => ['Athletic Shirt', 'Running Shirt', 'Training Tee', 'Dry-Fit Shirt', 'Compression Shirt', 'Gym Tank', 'Performance Polo', 'Muscle Tee'],
            'Workwear' => ['Mechanic Shirt', 'Safety Shirt', 'High-Visibility Shirt', 'Utility Shirt', 'Cargo Shirt', 'Fire-Resistant Shirt', 'Heavy Duty Shirt', 'Reflective Tee'],
            'Children Wear' => ['Kids Polo', 'Kids Graphic Tee', 'Cartoon Shirt', 'Kids Long Sleeve', 'Toddler Tee', 'Baby Romper Shirt', 'Tiny Tank', 'Kids Hoodie'],
        ];

        // 1. Create Shirt Categories once
        $shirtCategories = [];
        foreach (array_keys($categoryProductNames) as $categoryName) {
            $shirtCategories[$categoryName] = ShirtCategory::firstOrCreate([
                'category' => $categoryName,
                'description' => "$categoryName shirt",
            ]);
        }

        // 2. Create Products and attach to the category
        $products = [];
        $i = 1;
        foreach ($categoryProductNames as $categoryName => $productNames) {
            $category = $shirtCategories[$categoryName];

            foreach ($productNames as $productName) {
                $sku = 'OWSHIRT' . str_pad($i++, 4, '0', STR_PAD_LEFT);
                $materials = collect($rawMaterials)->random(rand(2, 4));
                $totalCost = 0;

                foreach ($materials as $m) {
                    $qty = rand(2, 6);
                    $totalCost += $m->price * $qty;
                }

                $price = round($totalCost * 2.5, 2);

                $product = Product::create([
                    'name' => $productName,
                    'sku' => $sku,
                    'price' => $price,
                    'quantity_available' => rand(100, 600),
                    'shirt_category_id' => $category->id, // new correct relationship
                    'low_stock_threshold' => rand(80, 120),
                ]);

                $products[] = $product;

                foreach ($materials as $m) {
                    BillOfMaterial::create([
                        'product_id' => $product->id,
                        'raw_materials_id' => $m->id,
                        'quantity_required' => rand(2, 6),
                    ]);
                }
            }
        }

        // PRODUCTION ORDERS & STAGES (random for last year)
        foreach (range(1, 20) as $_) {
            $product = $products[array_rand($products)];
            $createdBy = $users->where('role', 'manufacturer')->first();
            $createdAt = Carbon::now()->subDays(rand(1, 365));

            $statusOptions = ['pending', 'in_progress', 'completed', 'cancelled'];
            $status = $statusOptions[array_rand($statusOptions)];

            $startedAt = null;
            $completedAt = null;
            if (in_array($status, ['in_progress', 'completed'])) {
                $startedAt = $createdAt->copy()->addDays(rand(1, 5));
            }
            if ($status === 'completed') {
                $completedAt = $startedAt->copy()->addDays(rand(1, 15));
            }

            $order = ProductionOrder::create([
                'product_id' => $product->id,
                'quantity' => rand(100, 500),
                'status' => $status,
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
                'created_by' => $createdBy->id,
                'created_at' => $createdAt,
            ]);

            $stages = ['printing', 'packaging', 'delivery'];
            foreach ($stages as $stage) {
                ProductionStage::create([
                    'production_order_id' => $order->id,
                    'stage' => $stage,
                    'workforces_id' => Workforce::where('job', $stage)->inRandomOrder()->first()->id,
                    'status' => 'completed',
                    'created_at' => $createdAt,
                ]);
            }
        }

        // ORDERS & ORDER ITEMS (customers)
        foreach ($users->where('role', 'customer') as $customer) {
            foreach (range(1, 3) as $_) {
                $date = Carbon::now()->subDays(rand(1, 365));
                $deliveryOptions = ['pickup', 'delivery'];
                $deliveryOption = $deliveryOptions[array_rand($deliveryOptions)];

                $order = Order::create([
                    'status' => 'delivered',
                    'created_by' => $customer->id,
                    'delivery_option' => $deliveryOption,
                    'expected_delivery_date' => $date->copy()->addDays(3),
                    'total' => 0,
                    'created_at' => $date,
                ]);

                $items = collect($products)->random(2);
                $total = 0;
                foreach ($items as $product) {
                    $qty = rand(1, 3);
                    $total += $qty * $product->price;
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'size' => ['S', 'M', 'L', 'XL'][rand(0, 3)],
                        'unit_price' => $product->price,
                        'created_at' => $date,
                    ]);
                }

                $order->update(['total' => $total, 'rating' => rand(3, 5), 'review' => 'Great quality!']);
            }
        }

        // VENDOR ORDERS & ITEMS (vendors)
        foreach ($users->where('role', 'vendor') as $vendor) {
            foreach (range(1, 2) as $_) {
                $date = Carbon::now()->subDays(rand(1, 365));
                $deliveryOptions = ['pickup', 'delivery'];
                $deliveryOption = $deliveryOptions[array_rand($deliveryOptions)];

                $vOrder = VendorOrder::create([
                    'status' => 'confirmed',
                    'created_by' => $vendor->id,
                    'delivery_option' => $deliveryOption,
                    'total' => 0,
                    'created_at' => $date,
                ]);

                $total = 0;
                foreach (collect($products)->random(2) as $product) {
                    $qty = rand(1, 4);
                    VendorOrderItem::create([
                        'vendor_order_id' => $vOrder->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $product->price,
                        'created_at' => $date,
                    ]);
                    $total += $qty * $product->price;
                }

                $vOrder->update(['total' => $total]);
            }
        }

        // RAW MATERIAL PURCHASE ORDERS
        foreach (range(1, 10) as $_) {
            $material = $rawMaterials[array_rand($rawMaterials)];
            $qty = rand(100, 500);
            RawMaterialsPurchaseOrder::create([
                'raw_materials_id' => $material->id,
                'supplier_id' => $users->where('role', 'supplier')->first()->id,
                'quantity' => $qty,
                'price_per_unit' => $material->price,
                'expected_delivery_date' => now()->addDays(7),
                'status' => 'confirmed',
                'notes' => 'Urgent delivery for upcoming production batch.',
                'delivery_option' => ['pickup', 'delivery'][rand(0, 1)],
                'total_price' => $qty * $material->price,
                'created_by' => $users->where('role', 'manufacturer')->first()->id,
            ]);
        }

        // CHAT MESSAGES left empty
    }
}