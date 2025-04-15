<?php

namespace Database\Seeders;

use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $paymentTypes = [
            'Contado',
            'Abono'
        ];

        foreach ($paymentTypes as $name) {
            PaymentType::create(['name' => $name]);
        }
    }
}
