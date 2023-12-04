<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Http\Controllers\OrderDetailsController;
use App\Models\Announcement;
use App\Models\AnnouncementCategory;
use App\Models\Customer;
use App\Models\DeliveryMethod;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Package;
use App\Models\PackageProduct;
use App\Models\Postage;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'edit user']);
        Permission::create(['name' => 'delete user']);
        Permission::create(['name' => 'add product']);
        Permission::create(['name' => 'edit product']);
        Permission::create(['name' => 'remove product']);
        Permission::create(['name' => 'approve order']);
        Permission::create(['name' => 'reject order']);

        // Create Admin
        $admin_role = Role::create(['name' => 'admin']);
        $admin_role->givePermissionTo('create user');
        $admin_role->givePermissionTo('edit user');
        $admin_role->givePermissionTo('delete user');
        $admin_role->givePermissionTo('add product');
        $admin_role->givePermissionTo('edit product');
        $admin_role->givePermissionTo('remove product');
        $admin_role->givePermissionTo('approve order');
        $admin_role->givePermissionTo('reject order');

        $admin = User::factory()->create([
            'name'  => 'Admin',
            'email' => 'admin@vertex.com',
        ]);
        $admin->assignRole($admin_role);

        /**
         * Postage
         */
        $postage_details = [
            [
                'area'         => 'Skudai',
                'postcode'     => '81300',
                'state'        => 'Johor',
                'delivery_fee' => '5.00',
            ],
            [
                'area'         => 'Johor Bahru',
                'postcode'     => '81200',
                'state'        => 'Johor',
                'delivery_fee' => '6.00',
            ],
            [
                'area'         => 'Kulai',
                'postcode'     => '81030',
                'state'        => 'Johor',
                'delivery_fee' => '7.00',
            ],
            [
                'area'         => 'Kluang',
                'postcode'     => '86000',
                'state'        => 'Johor',
                'delivery_fee' => '10.00',
            ],
            [
                'area'         => 'Bangsar',
                'postcode'     => '59000',
                'state'        => 'Selangor',
                'delivery_fee' => '0.00',
            ],
        ];

        foreach ($postage_details as $detail) {
            Postage::create($detail);
        }

        /**
         * Sellers
         */
        $seller_details = [];
        for ($i = 0; $i < 100; $i++) {
            $seller_name      = fake()->unique()->name();
            $seller_details[] = [
                'name'  => $seller_name,
                'email' => sprintf('%s@vertex.com', strtolower(str_replace(' ', '_', $seller_name))),
            ];
        }

        $sellers = [];
        foreach ($seller_details as $detail) {
            $sellers[] = User::factory()->create($detail);
        }

        /**
         * Group
         */
        // Initialize seller index
        $seller_index = 0;

        // Create 4 parent groups
        for ($i = 1; $i <= 4; $i++) {
            // Create parent group
            $parent_group = Group::create([
                'name'      => 'PGroup ' . $i,
                'parent_id' => 0,
                'total_pv'  => (1000 * $i) + 5000,
                'status'    => 'active',
            ]);

            // Add members for parent group
            for ($j = 0; $j < 5; $j++) {
                $seller_id = $sellers[$seller_index]->id;
                GroupMember::create([
                    'group_id'  => $parent_group->id,
                    'seller_id' => $seller_id,
                    'status'    => 'active',
                    'is_leader' => $j === 0 ? 'yes' : 'no',
                ]);

                $seller_index++;
            }

            // Create 4 subgroups for each parent group
            for ($c = 1; $c <= 4; $c++) {
                // Create subgroup
                $sub_group = Group::create([
                    'name'      => 'PGroup ' . $parent_group->id . ' CGroup' . $c,
                    'parent_id' => $parent_group->id,
                    'total_pv'  => 1000,
                    'status'    => 'active',
                ]);

                // Add group members to subgroup
                for ($k = 0; $k < 5; $k++) {
                    $seller_id = $sellers[$seller_index]->id;
                    GroupMember::create([
                        'group_id'  => $sub_group->id,
                        'seller_id' => $seller_id,
                        'status'    => 'active',
                        'is_leader' => $k === 0 ? 'yes' : 'no',
                    ]);

                    $seller_index++;
                }
            }
        }


        /**
         * Customers
         */
        for ($i = 1; $i <= 10; $i++) {
            $postage_detail = $postage_details[array_rand($postage_details)];
            $postcode       = $postage_detail['postcode'];
            $state          = $postage_detail['state'];
            $city           = $postage_detail['area'];

            Customer::create([
                'name'      => 'Customer ' . $i,
                'unit_no'   => $i,
                'street'    => fake()->streetName(),
                'postcode'  => $postcode,
                'city'      => $city,
                'state'     => $state,
                'seller_id' => 1,
                'phone'     => fake()->phoneNumber(),
                'email'     => fake()->email(),
            ]);
        }

        // Create products
        $products = [];
        for ($i = 1; $i <= 6; $i++) {
            $products[] = Product::create([
                'name'           => 'Item ' . $i,
                'description'    => fake()->text(),
                'pv'             => 10,
                'price'          => fake()->randomFloat(2, 0, 30),
                'stock_quantity' => rand(50, 300),
                'status'         => 'active',
            ]);
        }

        // Create packages
        for ($p = 0; $p <= 4; $p++) {
            $package = Package::create([
                'name'        => 'Package' . $p,
                'description' => 'Bundle package',
                'status'      => fake()->randomElement(['active', 'inactive']),
            ]);
        }

        $package_product_data = [
            [
                'package_id' => 1,
                'product_id' => 1,
                'quantity'   => 2,
                'price'      => 90,
            ],
            [
                'package_id' => 2,
                'product_id' => 1,
                'quantity'   => 3,
                'price'      => 135,
            ],
            [
                'package_id' => 3,
                'product_id' => 1,
                'quantity'   => 5,
                'price'      => 220,
            ],
            [
                'package_id' => 4,
                'product_id' => 1,
                'quantity'   => 7,
                'price'      => 300,
            ],
            [
                'package_id' => 5,
                'product_id' => 1,
                'quantity'   => 10,
                'price'      => 400,
            ],
        ];
        foreach ($package_product_data as $datum) {
            PackageProduct::create($datum);
        }

        // Create deliveries
        $delivery_methods = ['DHL', 'JnT', 'Fedex', 'Ninjavan'];
        foreach ($delivery_methods as $method) {
            DeliveryMethod::create([
                'delivery_method' => $method,
                'status'          => 'active',
            ]);
        }

        // Create orders
//        for ($i = 1; $i <= 10; $i++) {
//            $status        = fake()->randomElement(['pending', 'completed', 'in transit', 'return', 'cancelled']);
//            $order         = Order::create([
//                'order_no'           => sprintf('OD10%02d', $i),
//                'status'             => $status,
//                'seller_id'          => fake()->randomElement([2, 3, 4, 5, 6, 7, 8, 9, 10]),
//                'customer_id'        => fake()->randomElement([1, 2, 3, 4, 5, 6, 7, 8, 9]),
//                'delivery_method_id' => fake()->randomElement([1, 2, 3, 4]),
//                'delivery_no'        => fake()->randomElement([NULL, sprintf('TN%05d', $i)]),
//                'total_price'        => 50.00,
//            ]);
//            $order_details = OrderDetails::create([
//                'order_id'   => $i,
//                'product_id' => 1,
//                'quantity'   => 1,
//                'price'      => 50,
//            ]);
//        }

        /**
         * Announcements
         */
        $announcement_category_details = ['Urgent', 'Sales', 'Promotions'];
        foreach ($announcement_category_details as $detail) {
            AnnouncementCategory::create([
                'category' => $detail,
            ]);
        }
        for ($i = 0; $i < 10; $i++) {
            $category    = AnnouncementCategory::inRandomOrder()->first();
            $category_id = $category->id;
            Announcement::create([
                'category_id'  => $category_id,
                'announcement' => fake()->text(150),
                'status'       => $i < 1 ? 'active' : 'inactive',
            ]);
        }
    }
}
