<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Default Branch
        $branch = Branch::create([
            'name' => 'Main Branch',
            'address' => 'Jakarta, Indonesia',
            'phone' => '021-12345678',
            'is_active' => true,
        ]);

        // 2. Create Roles
        $adminRole = Role::create(['name' => 'admin']);
        $cashierRole = Role::create(['name' => 'cashier']);

        // 3. Create Admin User
        $admin = User::create([
            'name' => 'Admin CoffeePOS',
            'email' => 'admin@coffeepos.com',
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,
        ]);
        $admin->assignRole($adminRole);

        // 4. Create Cashier User
        $cashier = User::create([
            'name' => 'Cashier Branch 1',
            'email' => 'cashier@coffeepos.com',
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,
        ]);
        $cashier->assignRole($cashierRole);

        $this->command->info('Users and roles created successfully!');
        $this->command->info('Admin: admin@coffeepos.com / password');
        $this->command->info('Cashier: cashier@coffeepos.com / password');
    }
}
