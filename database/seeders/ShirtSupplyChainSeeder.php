<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ShirtSupplyChainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Disable foreign key checks and truncate all relevant tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $tables = [
            'chat_messages', 'vendor_order_items', 'vendor_orders', 'order_items', 'orders',
            'production_stages', 'production_orders', 'bill_of_materials', 'shirt_categories',
            'products', 'raw_materials_purchase_orders', 'raw_materials', 'raw_material_category',
            'vendor_validations', 'admin_info', 'supplier_info', 'customer_info',
            'manufacturing_info', 'vendor_info', 'workforces', 'users'
        ];
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert Users
        $users = [
            ['name'=>'Alice Vendor', 'email'=>'alice.vendor@example.com', 'role'=>'vendor', 'password'=>bcrypt('password')],
            ['name'=>'Bob Manufacturer', 'email'=>'bob.manufacturer@example.com', 'role'=>'manufacturer', 'password'=>bcrypt('password')],
            ['name'=>'Charlie Admin', 'email'=>'charlie.admin@example.com', 'role'=>'admin', 'password'=>bcrypt('password')],
            ['name'=>'Dana Supplier', 'email'=>'dana.supplier@example.com', 'role'=>'supplier', 'password'=>bcrypt('password')],
            ['name'=>'Eve Customer', 'email'=>'eve.customer@example.com', 'role'=>'customer', 'password'=>bcrypt('password')],
        ];
        DB::table('users')->insert($users);
        $userIds = DB::table('users')->pluck('id', 'email')->toArray();

        // Vendor info
        DB::table('vendor_info')->insert([
            ['user_id' => $userIds['alice.vendor@example.com'], 'business_name'=>'Alice Apparel', 'location'=>'Kampala'],
        ]);

        // Manufacturing info
        DB::table('manufacturing_info')->insert([
            ['user_id' => $userIds['bob.manufacturer@example.com'], 'factory_name'=>'Bob Textiles', 'location'=>'Jinja'],
        ]);

        // Admin info
        DB::table('admin_info')->insert([
            ['user_id' => $userIds['charlie.admin@example.com'], 'title'=>'System Administrator'],
        ]);

        // Supplier info
        DB::table('supplier_info')->insert([
            ['user_id' => $userIds['dana.supplier@example.com'], 'company_name'=>'Dana Supplies Ltd', 'location'=>'Entebbe'],
        ]);

        // Customer info
        DB::table('customer_info')->insert([
            ['user_id' => $userIds['eve.customer@example.com'], 'address'=>'Plot 123, Main Street, Kampala'],
        ]);

        // Vendor validation
        DB::table('vendor_validations')->insert([
            [
                'user_id' => $userIds['alice.vendor@example.com'],
                'business_name' => 'Alice Apparel',
                'is_valid' => true,
                'reasons' => null,
                'visit_date' => Carbon::now()->subDays(10),
                'supporting_documents' => 'certificate.pdf',
                'notified_at' => Carbon::now()->subDays(7),
            ],
        ]);

        // Raw Material Categories
        $rawMaterialCategories = [
            ['name'=>'Fabrics', 'description'=>'Materials used for fabric production'],
            ['name'=>'Accessories', 'description'=>'Buttons, zippers, threads'],
            ['name'=>'Chemicals', 'description'=>'Dyes, chemicals for treatment'],
            ['name'=>'Packaging', 'description'=>'Bags, boxes, hangers'],
        ];
        DB::table('raw_material_category')->insert($rawMaterialCategories);
        $rmCatIds = DB::table('raw_material_category')->pluck('id', 'name')->toArray();

        // Raw Materials - 10 items with UGX prices
        $rawMaterials = [
            ['name'=>'Cotton', 'description'=>'Soft cotton fabric', 'price'=>35000, 'supplier_id'=>$userIds['dana.supplier@example.com'], 'category_id'=>$rmCatIds['Fabrics'], 'current_stock'=>200, 'reorder_level'=>50, 'unit_of_measure'=>'kg'],
            ['name'=>'Polyester Thread', 'description'=>'Strong thread for stitching', 'price'=>7000, 'supplier_id'=>$userIds['dana.supplier@example.com'], 'category_id'=>$rmCatIds['Accessories'], 'current_stock'=>500, 'reorder_level'=>100, 'unit_of_measure'=>'box'],
            ['name'=>'Blue Dye', 'description'=>'Color dye for fabric', 'price'=>45000, 'supplier_id'=>$userIds['dana.supplier@example.com'], 'category_id'=>$rmCatIds['Chemicals'], 'current_stock'=>100, 'reorder_level'=>20, 'unit_of_measure'=>'liter'],
            ['name'=>'Poly Bags', 'description'=>'Packaging bags', 'price'=>1200, 'supplier_id'=>$userIds['dana.supplier@example.com'], 'category_id'=>$rmCatIds['Packaging'], 'current_stock'=>1000, 'reorder_level'=>200, 'unit_of_measure'=>'piece'],
            ['name'=>'Zippers', 'description'=>'Metal zippers', 'price'=>5000, 'supplier_id'=>$userIds['dana.supplier@example.com'], 'category_id'=>$rmCatIds['Accessories'], 'current_stock'=>300, 'reorder_level'=>50, 'unit_of_measure'=>'piece'],
            ['name'=>'Buttons', 'description'=>'Plastic buttons', 'price'=>3000, 'supplier_id'=>$userIds['dana.supplier@example.com'], 'category_id'=>$rmCatIds['Accessories'], 'current_stock'=>400, 'reorder_level'=>75, 'unit_of_measure'=>'box'],
            ['name'=>'Red Dye', 'description'=>'Red color dye for fabric', 'price'=>47000, 'supplier_id'=>$userIds['dana.supplier@example.com'], 'category_id'=>$rmCatIds['Chemicals'], 'current_stock'=>90, 'reorder_level'=>15, 'unit_of_measure'=>'liter'],
            ['name'=>'Thread Spools', 'description'=>'Colored sewing thread spools', 'price'=>6500, 'supplier_id'=>$userIds['dana.supplier@example.com'], 'category_id'=>$rmCatIds['Accessories'], 'current_stock'=>600, 'reorder_level'=>100, 'unit_of_measure'=>'box'],
            ['name'=>'Eco-friendly Tags', 'description'=>'Brand tags made from recycled material', 'price'=>1500, 'supplier_id'=>$userIds['dana.supplier@example.com'], 'category_id'=>$rmCatIds['Accessories'], 'current_stock'=>1500, 'reorder_level'=>300, 'unit_of_measure'=>'piece'],
            ['name'=>'Packing Tape', 'description'=>'Strong packing tape for parcels', 'price'=>2500, 'supplier_id'=>$userIds['dana.supplier@example.com'], 'category_id'=>$rmCatIds['Packaging'], 'current_stock'=>800, 'reorder_level'=>200, 'unit_of_measure'=>'roll'],
        ];
        DB::table('raw_materials')->insert($rawMaterials);
        $rawMaterialIds = DB::table('raw_materials')->pluck('id', 'name')->toArray();

        // Raw Material Purchase Orders
        DB::table('raw_materials_purchase_orders')->insert([
            [
                'raw_materials_id' => $rawMaterialIds['Cotton'],
                'supplier_id' => $userIds['dana.supplier@example.com'],
                'quantity' => 100,
                'price_per_unit' => 35000,
                'expected_delivery_date' => Carbon::now()->addDays(7),
                'status' => 'pending',
                'notes' => 'Urgent order',
                'delivery_option' => 'delivery',
                'total_price' => 3500000,
                'created_by' => $userIds['bob.manufacturer@example.com'],
            ],
            [
                'raw_materials_id' => $rawMaterialIds['Polyester Thread'],
                'supplier_id' => $userIds['dana.supplier@example.com'],
                'quantity' => 200,
                'price_per_unit' => 7000,
                'expected_delivery_date' => Carbon::now()->addDays(10),
                'status' => 'confirmed',
                'notes' => '',
                'delivery_option' => 'pickup',
                'total_price' => 1400000,
                'created_by' => $userIds['bob.manufacturer@example.com'],
            ],
        ]);

        // Shirt Categories (5 categories)
        $shirtCategories = [
            ['category' => 'Formal', 'description' => 'Suitable for formal occasions'],
            ['category' => 'Casual', 'description' => 'Everyday wear'],
            ['category' => 'Summer', 'description' => 'Lightweight and breathable'],
            ['category' => 'Sportswear', 'description' => 'For active wear and comfort'],
            ['category' => 'Luxury', 'description' => 'Premium quality and design'],
        ];

        // Insert 40 products with slight variation in name and SKU
        $products = [];
        for ($i = 1; $i <= 40; $i++) {
            $products[] = [
                'name' => "Shirt Model $i",
                'sku' => "SHIRT-" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'price' => 0, // will update after BOM calc
                'quantity_available' => rand(50, 200),
                'image' => null,
            ];
        }
        DB::table('products')->insert($products);
        $productIds = DB::table('products')->pluck('id')->toArray();

        // Insert shirt categories linked to products, cycling through 5 categories
        $shirtCategoryInserts = [];
        for ($i = 0; $i < 40; $i++) {
            $catIndex = $i % 5;
            $shirtCategoryInserts[] = [
                'product_id' => $productIds[$i],
                'category' => $shirtCategories[$catIndex]['category'],
                'description' => $shirtCategories[$catIndex]['description'],
            ];
        }
        DB::table('shirt_categories')->insert($shirtCategoryInserts);

        // Bill of Materials: For each product assign between 3 and 5 random raw materials with quantity 1-3
        $billOfMaterialsInserts = [];
        $rawMaterialCount = count($rawMaterialIds);
        $rawMaterialIdList = array_values($rawMaterialIds);

        foreach ($productIds as $index => $prodId) {
            $numMaterials = rand(3, 5);
            $usedMaterialIndexes = [];

            for ($j = 0; $j < $numMaterials; $j++) {
                do {
                    $rmIndex = rand(0, $rawMaterialCount -1);
                } while (in_array($rmIndex, $usedMaterialIndexes));
                $usedMaterialIndexes[] = $rmIndex;

                $qtyReq = rand(1, 3);

                $billOfMaterialsInserts[] = [
                    'product_id' => $prodId,
                    'raw_materials_id' => $rawMaterialIdList[$rmIndex],
                    'quantity_required' => $qtyReq,
                ];
            }
        }
        DB::table('bill_of_materials')->insert($billOfMaterialsInserts);

        // Calculate and update product prices based on BOM total * 1.05
        foreach ($productIds as $prodId) {
            $bomItems = DB::table('bill_of_materials')->where('product_id', $prodId)->get();

            $totalBomCost = 0;
            foreach ($bomItems as $item) {
                $rawMat = DB::table('raw_materials')->where('id', $item->raw_materials_id)->first();
                if ($rawMat) {
                    $totalBomCost += $rawMat->price * $item->quantity_required;
                }
            }
            $price = (int) round($totalBomCost * 1.05);
            DB::table('products')->where('id', $prodId)->update(['price' => $price]);
        }

        // Workforce - 3 workers per job (printing, packaging, delivery) => 9 workers total
        $jobs = ['printing', 'packaging', 'delivery'];
        $workforceInserts = [];
        foreach ($jobs as $job) {
            for ($i=1; $i <=3; $i++) {
                $workforceInserts[] = [
                    'name' => "$job Worker $i",
                    'job' => strtolower($job),
                ];
            }
        }
        DB::table('workforces')->insert($workforceInserts);
        $workforceIds = DB::table('workforces')->pluck('id')->toArray();

        // Production Orders (create 5 sample orders for random products)
        $productionOrders = [];
        for ($i = 0; $i < 5; $i++) {
            $prodId = $productIds[array_rand($productIds)];
            $productionOrders[] = [
                'product_id' => $prodId,
                'quantity' => rand(20, 60),
                'status' => ['pending','in_progress','completed'][rand(0,2)],
                'started_at' => null,
                'completed_at' => null,
                'created_by' => $userIds['bob.manufacturer@example.com'],
            ];
        }
        DB::table('production_orders')->insert($productionOrders);
        $productionOrderIds = DB::table('production_orders')->pluck('id')->toArray();

        // Production Stages - for each production order, create the 3 fixed stages with random workforce, random status
        $statuses = ['pending', 'in_progress', 'completed'];
        $productionStages = [];
        foreach ($productionOrderIds as $prodOrderId) {
            $job = ['printing'];
            foreach ($jobs as $job) {
                $productionStages[] = [
                    'production_order_id' => $prodOrderId,
                    'stage' => strtolower($job),
                    'workforces_id' => null,
                    'status' => $statuses[array_rand($statuses)],
                    'started_at' => Carbon::now()->subDays(rand(1,10)),
                    'completed_at' => null,
                ];
            }
        }
        DB::table('production_stages')->insert($productionStages);

        // Orders
        DB::table('orders')->insert([
            [
                'status' => 'pending',
                'created_by' => $userIds['eve.customer@example.com'],
                'delivery_option' => 'delivery',
                'total' => 375000,
                'expected_delivery_date' => Carbon::now()->addDays(7),
            ],
        ]);
        $orderIds = DB::table('orders')->pluck('id')->toArray();

        // Order Items for that order (randomly pick 2 products)
        DB::table('order_items')->insert([
            ['order_id' => $orderIds[0], 'product_id' => $productIds[0], 'quantity' => 3],
            ['order_id' => $orderIds[0], 'product_id' => $productIds[1], 'quantity' => 2],
        ]);

        // Vendor Orders
        DB::table('vendor_orders')->insert([
            [
                'status' => 'confirmed',
                'created_by' => $userIds['alice.vendor@example.com'],
                'delivery_option' => 'pickup',
                'total' => 900000,
            ],
        ]);
        $vendorOrderIds = DB::table('vendor_orders')->pluck('id')->toArray();

        // Vendor Order Items
        DB::table('vendor_order_items')->insert([
            ['vendor_order_id' => $vendorOrderIds[0], 'product_id' => $productIds[2], 'quantity' => 10],
        ]);

        // Chat Messages
        DB::table('chat_messages')->insert([
            [
                'sender_id' => $userIds['alice.vendor@example.com'],
                'receiver_id' => $userIds['eve.customer@example.com'],
                'message' => 'Hello! Interested in our new summer collection?',
            ],
            [
                'sender_id' => $userIds['eve.customer@example.com'],
                'receiver_id' => $userIds['alice.vendor@example.com'],
                'message' => 'Yes, please send me the catalog.',
            ],
        ]);
    }
}
