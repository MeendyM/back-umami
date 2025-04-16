<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StatusOrder;

class StatusOrderSeeder extends Seeder
{
    public function run(): void
    {
        $statusOrder = [
            'Borrador',
            'Pendiente de pago',
            'Pagado',
            'Abonado'
        ];

        foreach ($statusOrder as $status) {
            StatusOrder::create(['name' => $status]);
        }
    }
}
