<x-filament-panels::page>

        <div class="flex justify-center items-center h-full">
            @if (session()->has('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
            @endif
        </div>

        <div class="flex justify-center items-center h-full">
                <form wire:submit.prevent="save">
                    {{ $this->form }}
                  
                  <div class="flex justify-center items-center h-full">
                    	<button type="save" class="inline-flex items-center px-4 py-2 mt-1 text-sm font-medium text-white border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" style="background-color: #ca8a04;" onmouseover="this.style.backgroundColor='#eab308'" onmouseout="this.style.backgroundColor='#ca8a04'">
                    		Update
                    	</button>
                  </div>
          		</form>
        </div>

</x-filament-panels::page>