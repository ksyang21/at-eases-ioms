<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Http\Controllers\OrderDetailsController;
use App\Models\Customer;
use App\Models\DeliveryMethod;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Package;
use App\Models\PackageProduct;
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
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole($admin_role);

        // Create sellers & customers
        for ($i = 2; $i <= 10; $i++) {
            User::factory()->create([
                'name'  => 'Seller ' . $i,
                'email' => 'seller' . $i . '@example.com',
            ]);

            Customer::create([
                'name'      => 'Customer ' . $i,
                'address'   => fake()->address,
                'seller_id' => 1,
                'phone' => fake()->phoneNumber(),
                'email' => fake()->email()
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
                'stock_quantity' => rand(50,300),
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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
