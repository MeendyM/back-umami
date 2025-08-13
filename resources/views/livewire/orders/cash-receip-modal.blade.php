<div>
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="w-full">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Agregar abono en efectivo
                                    </h3>
                                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <form wire:submit.prevent="save">
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Cantidad *</label>
                                        <input type="number" step="0.01" min="1" wire:model.defer="amount" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300" required>
                                        @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Foto (opcional)</label>
                                        <input type="file" wire:model="image" accept="image/*">
                                        @if($imagePreviewUrl)
                                            <img src="{{ $imagePreviewUrl }}" class="mt-2 h-24 rounded shadow">
                                        @elseif($existingImageUrl)
                                            <img src="{{ $existingImageUrl }}" class="mt-2 h-24 rounded shadow">
                                        @endif
                                        @error('image') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="button" wire:click="closeModal" class="mr-2 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancelar</button>
                                        <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">Agregar abono</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
