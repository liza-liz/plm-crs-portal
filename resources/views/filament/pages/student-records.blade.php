<x-filament-panels::page>
    <form wire:submit.prevent="create">
        {{ $this->form }}
        <div class="p-3 rounded-md shadow-sm">
                <button type="submit" class="inline-flex items-center px-4 py-2 mt-1 text-sm font-medium text-white border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" style="background-color: #ca8a04;" onmouseover="this.style.backgroundColor='#eab308'" onmouseout="this.style.backgroundColor='#ca8a04'">
                    Update
                </button>
            <button type="button" wire:click="resetForm" class="inline-flex items-center px-4 py-2 mt-1 text-sm font-medium text-black border border-transparent rounded-md shadow-sm" style="background-color: white;">
                Reset
            </button>
        </div>
    </form>

    
</x-filament-panels::page>