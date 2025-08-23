<div>
    <x-modal-header wire:model="modalDelete" title='¿Eliminar categoría?'>
        <div class="w-full flex items-center text-center">
            <p class="max-w-[400px] mx-auto text-smm text-gray py-3">Esta acción no se puede deshacer, se eliminará la
                relacion de la categoría y de los productos, tendra que hacerlo de nuevo manualmente.</p>
        </div>
        <x-slot name="footer" class="space-x-1 space-y-3 flex flex-col">
            <x-primary-button type="button" class="!border-blue" wire:click="deleteCategory()">
                Eliminar
            </x-primary-button>
            <x-secondary-button class="!border-blue" type="button" wire:click="closeModal()">
                Cancelar
            </x-secondary-button>
        </x-slot>
    </x-modal-header>
</div>
