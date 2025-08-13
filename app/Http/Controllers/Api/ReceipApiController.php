<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Receip;
use App\Models\Order;
use App\Enums\StatusOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReceipApiController extends Controller
{
    public function addReceip(Request $request)
    {
        Log::info('Iniciando proceso de agregar recibo', [
            'request_data' => $request->all()
        ]);

        $validatedData = $request->validate([
            'id_order' => 'required|integer',
            'id_user' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'id_transaction' => 'required|string',
            'url_img' => 'nullable|string',
        ]);

        Log::info('Datos validados correctamente', ['validated_data' => $validatedData]);

        if (empty($validatedData['url_img'])) {
            $validatedData['url_img'] = 'https://example.com/default-image.jpg';
            Log::info('URL de imagen por defecto asignada');
        }

        $order = Order::find($validatedData['id_order']);

        if (!$order) {
            Log::warning('Orden no encontrada', ['id_order' => $validatedData['id_order']]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        Log::info('Orden encontrada', ['order' => $order]);

        // Verificar si la orden ya está pagada o en revisión - no se permiten más recibos
        if ($order->status === StatusOrder::PAID->value || $order->status === StatusOrder::UNDER_REVIEW->value) {
            Log::warning('Intento de agregar recibo a orden ya pagada o en revisión', [
                'order_id' => $order->id_order,
                'current_status' => $order->status
            ]);
            return response()->json(['message' => 'This order is already fully paid or under review. No more receipts can be added.'], 400);
        }

        // Calcular el total pagado actual de la orden
        $currentTotalPaid = $order->receips()->sum('amount');
        Log::info('Total pagado actual calculado', ['current_total_paid' => $currentTotalPaid]);

        // Calcular el total pagado después de agregar el nuevo recibo
        $newTotalPaid = $currentTotalPaid + $validatedData['amount'];
        Log::info('Nuevo total pagado calculado', ['new_total_paid' => $newTotalPaid]);

        // Verificar si el nuevo total pagado excede el final_total de la orden
        if ($newTotalPaid > $order->final_total) {
            Log::warning('El total de recibos excede el total de la orden', [
                'new_total_paid' => $newTotalPaid,
                'order_final_total' => $order->final_total
            ]);
            return response()->json(['message' => 'The total amount of receipts exceeds the order total.'], 400);
        }

        // Cambiar estado a 'paying' si es el primer recibo
        if ($order->status !== StatusOrder::PAYING->value) {
            $order->status = StatusOrder::PAYING->value;
            $order->save();
            Log::info('Estado de orden cambiado a paying', ['order_id' => $order->id_order]);
        }

        $validatedData['payment_type'] = \App\Enums\ReceipPaymentType::TRANSFER->value;
        $receip = Receip::create($validatedData);
        Log::info('Recibo creado exitosamente', ['receip' => $receip]);

        // Verificar si el total pagado es igual o mayor al final_total después de agregar el recibo
        if ($newTotalPaid >= $order->final_total) {
            $order->status = StatusOrder::UNDER_REVIEW->value;
            $order->save();
            Log::info('Estado de orden cambiado a en revisión', [
                'order_id' => $order->id_order,
                'new_total_paid' => $newTotalPaid,
                'order_final_total' => $order->final_total
            ]);
        }

        Log::info('Proceso de agregar recibo completado exitosamente', [
            'receip_id' => $receip->id_receip,
            'order_id' => $order->id_order,
            'final_status' => $order->status
        ]);

        return response()->json(['message' => 'Receip created successfully', 'receip' => $receip], 201);
    }

    public function getReceipsByOrderId($id_order)
    {
        \Illuminate\Support\Facades\Log::info('Buscando recibos para la orden', ['id_order' => $id_order]);
        $receips = Receip::where('id_order', $id_order)->get();
        // Agregar tipo y label a cada recibo
        foreach ($receips as $receip) {
            $receip->type = $receip->payment_type;
            $receip->type_label = \App\Enums\ReceipPaymentType::labels()[$receip->payment_type->value ?? $receip->payment_type] ?? $receip->payment_type;
        }

        if ($receips->isEmpty()) {
            \Illuminate\Support\Facades\Log::warning('No se encontraron recibos para la orden', ['id_order' => $id_order]);
            return response()->json(['message' => 'No receips found for this order'], 404);
        }

        $totalPaid = $receips->sum('amount');
        \Illuminate\Support\Facades\Log::info('Recibos encontrados', [
            'id_order' => $id_order,
            'total_receips' => $receips->count(),
            'total_paid' => $totalPaid,
        ]);

        return response()->json([
            'receips' => $receips,
            'total_paid' => $totalPaid
        ], 200);
    }
    // Editar recibo solo si status es 'revisar'. Al editar, status pasa a 'reenviado'.
    public function editReceip(Request $request, $id_receip)
    {
        $receip = \App\Models\Receip::find($id_receip);
        if (!$receip) {
            return response()->json(['message' => 'Receip not found'], 404);
        }
        if ($receip->status !== \App\Enums\ReceipStatus::REVISAR) {
            return response()->json(['message' => 'Only receips with status "revisar" can be edited'], 403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'id_transaction' => 'required|string',
            'url_img' => 'nullable|string',
        ]);

        $receip->amount = $validated['amount'];
        $receip->id_transaction = $validated['id_transaction'];
        $receip->url_img = $validated['url_img'] ?? $receip->url_img;
        $receip->status = \App\Enums\ReceipStatus::REENVIADO;
        $receip->save();

        return response()->json(['message' => 'Receip updated and resent', 'receip' => $receip], 200);
    }

    
}
