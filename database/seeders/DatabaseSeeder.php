<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use App\Models\StatusOrder;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Validation\Rules\Can;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

      /*   User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]); */

        $this->call([
            InstitutionSeeder::class,
            //SupplierSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            //ProductSeeder::class,
            ProductSetSeeder::class,
          /*   StatusOrderSeeder::class, */
           /*  OrderSeeder::class,
            OrderItemSeeder::class */
        ]);
    }
}
