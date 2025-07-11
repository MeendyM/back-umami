<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Receip;
use App\Models\Order;
use App\Enums\StatusOrder;
use Illuminate\Http\Request;

class ReceipApiController extends Controller
{
    public function addReceip(Request $request)
    {
        $validatedData = $request->validate([
            'id_order' => 'required|integer',
            'id_user' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'id_transaction' => 'required|string',
            'url_img' => 'nullable|string',
        ]);

        if (empty($validatedData['url_img'])) {
            $validatedData['url_img'] = 'https://example.com/default-image.jpg';
        }

        $order = Order::find($validatedData['id_order']);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Calculate current total paid for the order
        $currentTotalPaid = $order->receips()->sum('amount');

        // Calculate the total paid after adding the new receipt
        $newTotalPaid = $currentTotalPaid + $validatedData['amount'];

        // Check if the new total paid exceeds the order's final_total
        if ($newTotalPaid > $order->final_total) {
            return response()->json(['message' => 'The total amount of receipts exceeds the order total.'], 400);
        }

        if ($order->status !== StatusOrder::PAYING->value) {
            $order->status = StatusOrder::PAYING->value;
            $order->save();
        }

        $receip = Receip::create($validatedData);

        return response()->json(['message' => 'Receip created successfully', 'receip' => $receip], 201);
    }

    public function getReceipsByOrderId($id_order)
    {
        $receips = Receip::where('id_order', $id_order)->get();

        if ($receips->isEmpty()) {
            return response()->json(['message' => 'No receips found for this order'], 404);
        }

        $totalPaid = $receips->sum('amount');

        return response()->json([
            'receips' => $receips,
            'total_paid' => $totalPaid
        ], 200);
    }
}
