{{-- Modal para mostrar recibos --}}
<div>
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Overlay --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                {{-- Modal panel --}}
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full">
                                {{-- Header --}}
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Recibos de la Orden #{{ $order->id_order ?? '' }}
                                    </h3>
                                    <button wire:click="closeModal"
                                        class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Información de la orden --}}
                                @if ($order)
                                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
                                            <div>
                                                <span class="font-semibold text-gray-700">Usuario:</span>
                                                <p>{{ $order->user->name ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-700">Total Orden:</span>
                                                <p>${{ number_format($order->total, 2) }}</p>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-700">Total Pagado:</span>
                                                <p class="text-lg font-bold text-green-600">
                                                    ${{ number_format($totalPaid, 2) }}</p>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-700">Estado:</span>
                                                <p class="capitalize">{{  $statusLabels[$order->status] ?? ucfirst($order->status) }}</p>
                                            </div>
                                            <div>
                                                <span class="font-semibold text-gray-700">Fecha:</span>
                                                <p>{{ $order->created_at->format('d/m/Y') }}</p>
                                            </div>
                                        </div>

                                        {{-- Información adicional del pago --}}
                                        @if ($totalPaid > 0)
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                                    {{-- Saldo Pendiente --}}
                                                    <div class="text-center">
                                                        <span class="block font-semibold text-gray-700 mb-2">Saldo
                                                            Pendiente</span>
                                                        @php $saldoPendiente = $order->final_total - $totalPaid; @endphp
                                                        <span
                                                            class="inline-block px-3 py-2 rounded-lg font-semibold text-lg
                                                @if ($saldoPendiente <= 0) bg-green-100 text-green-800 
                                                @else 
                                                    bg-red-100 text-red-800 @endif">
                                                            ${{ number_format($saldoPendiente, 2) }}
                                                        </span>
                                                    </div>

                                                    {{-- Total de Recibos --}}
                                                    <div class="text-center">
                                                        <span class="block font-semibold text-gray-700 mb-2">Total de
                                                            Recibos</span>
                                                        <span
                                                            class="inline-block px-3 py-2 rounded-lg font-semibold text-lg bg-blue-100 text-blue-800">
                                                            {{ count($receipts) }}
                                                        </span>
                                                    </div>

                                                    {{-- Estado de Pago --}}
                                                    <div class="text-center">
                                                        <span class="block font-semibold text-gray-700 mb-2">Estado de
                                                            Pago</span>
                                                        @if ($saldoPendiente <= 0)
                                                            <span
                                                                class="inline-block px-3 py-2 rounded-lg font-semibold text-sm bg-green-100 text-green-800">
                                                                ✅ Completamente Pagado
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-block px-3 py-2 rounded-lg font-semibold text-sm bg-yellow-100 text-yellow-800">
                                                                ⏳ Pago Parcial
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="mt-3 border-t border-gray-200">

                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Lista de recibos --}}
                                <div class="max-h-96 overflow-y-auto">
                                    @if (count($receipts) > 0)
                                        <div class="grid gap-4">
                                            @foreach ($receipts as $receipt)
                                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-3 gap-2">
                                                        <div class="flex items-center gap-4">
                                                            @if($receipt->url_img)
                                                                <img src="{{ $receipt->url_img }}" alt="Comprobante" class="h-16 w-16 object-cover rounded border" onerror="this.style.display='none'">
                                                            @else
                                                                <div class="h-16 w-16 flex items-center justify-center bg-gray-100 text-gray-500 border rounded">No aplica</div>
                                                            @endif
                                                            <div>
                                                                <div class="font-semibold text-gray-800">Monto: ${{ number_format($receipt->amount, 2) }}</div>
                                                                <div class="text-xs text-gray-500">Tipo: {{ \App\Enums\ReceipPaymentType::labels()[$receipt->payment_type->value ?? $receipt->payment_type] ?? '-' }}</div>
                                                                <div class="text-xs text-gray-500">Estatus: {{ \App\Enums\ReceipStatus::labels()[$receipt->status->value ?? $receipt->status] ?? $receipt->status }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="flex flex-col gap-2 mt-2 md:mt-0">
                                                            @if($receipt->status->value === 'sent')
                                                                <button wire:click="approveReceip({{ $receipt->id_receip }})" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs">Aprobar</button>
                                                                <button wire:click="showReviewInput({{ $receipt->id_receip }})" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs">Revisar</button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if(isset($reviewInputId) && $reviewInputId == $receipt->id_receip)
                                                        <div class="mt-2">
                                                            <input type="text" wire:model.defer="reviewMessage" placeholder="Motivo de revisión..." class="w-full border rounded px-2 py-1 text-xs">
                                                            <button wire:click="sendToReview({{ $receipt->id_receip }})" class="mt-1 bg-yellow-600 hover:bg-yellow-700 text-white px-2 py-1 rounded text-xs">Enviar a revisión</button>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-8">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay recibos</h3>
                                            <p class="mt-1 text-sm text-gray-500">Esta orden no tiene recibos asociados.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="closeModal" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
