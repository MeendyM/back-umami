<?php

namespace Database\Seeders;

use App\Enums\StatusOrder;
use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Order::create([
            'id_user' => 2,
            'status' => StatusOrder::REQUESTED,
        ]);

        Order::create([
            'id_user' => 3,
            'status' => StatusOrder::REQUESTED,
        ]);

        Order::create([
            'id_user' => 4,
            'status' => StatusOrder::REQUESTED,
        ]);
    }
}
