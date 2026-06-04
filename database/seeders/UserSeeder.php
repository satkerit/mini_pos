<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::create([
            'name' => 'Main Branch',
            'address' => 'Jakarta, Indonesia',
            'phone' => '021-12345678',
            'is_active' => true,
        ]);

        $models = [
            'branch', 'category', 'product', 'ingredient', 'recipe', 'recipe_detail',
            'user', 'role', 'permission',
            'stock_transaction', 'stock_opname', 'stock_adjustment',
            'sale', 'sale_item',
            'payment_method', 'payment_gateway_config',
            'payment', 'qris_transaction', 'payment_gateway_log',
        ];

        $actions = ['view_any', 'create', 'update', 'delete'];

        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $cashierRole = Role::create(['name' => 'cashier', 'guard_name' => 'web']);

        $allPermissions = collect($models)->crossJoin($actions)->map(function ($pair) {
            [$model, $action] = $pair;
            return Permission::firstOrCreate([
                'name' => "{$action}_{$model}",
                'guard_name' => 'web',
            ]);
        });

        $adminRole->syncPermissions($allPermissions);

        $cashierPermissions = Permission::whereIn('name', [
            'view_any_sale', 'view_any_sale_item',
            'view_any_product', 'view_any_category',
            'view_any_payment', 'view_any_payment_method',
        ])->get();
        $cashierRole->syncPermissions($cashierPermissions);

        $admin = User::create([
            'name' => 'Admin CoffeePOS',
            'email' => 'admin@coffeepos.com',
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,
        ]);
        $admin->assignRole($adminRole);

        $cashier = User::create([
            'name' => 'Cashier Branch 1',
            'email' => 'cashier@coffeepos.com',
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,
        ]);
        $cashier->assignRole($cashierRole);

        $this->command->info('Users, roles, and permissions created successfully!');
        $this->command->info('Admin: admin@coffeepos.com / password');
        $this->command->info('Cashier: cashier@coffeepos.com / password');
    }
}
