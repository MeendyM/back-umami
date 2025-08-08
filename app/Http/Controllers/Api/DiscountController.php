<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\DiscountUse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DiscountController extends Controller
{
    /**
     * Aplica el descuento si es válido
     * @param string $code
     * @param int $userId
     * @param float $orderTotal
     * @return array
     */
    public function applyDiscount($code, $userId, $orderTotal)
    {
        $discount = Discount::where('code', $code)->first();
        if (!$discount) {
            return [
                'valid' => false,
                'message' => 'Código de descuento no encontrado.'
            ];
        }

        // Validar fechas
        $now = Carbon::now();
        if ($discount->expires_at && $now->gt(Carbon::parse($discount->expires_at))) {
            return [
                'valid' => false,
                'message' => 'El descuento ha expirado.'
            ];
        }

        // Validar monto mínimo
        if ($discount->minimum_purchase && $orderTotal < $discount->minimum_purchase) {
            return [
                'valid' => false,
                'message' => 'El monto mínimo para aplicar el descuento no se cumple.'
            ];
        }

        $uses = DiscountUse::where('id_discount', $discount->id_discount)
            ->where('id_user', $userId)
            ->count();

        // Validar cantidad de usos (si aplica)
        if ($discount->max_uses && $uses >= $discount->max_uses) {
            return [
                'valid' => false,
                'message' => 'El descuento ha alcanzado el máximo de usos.'
            ];
        }

        // Calcular monto de descuento
        $amount = 0;
        $amount = round($orderTotal * ($discount->value / 100), 2);


        return [
            'valid' => true,
            'amount' => $amount,
            'discount_id' => $discount->id_discount,
            'message' => 'Descuento aplicado correctamente.'
        ];
    }
}
