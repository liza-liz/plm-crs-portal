<x-filament-panels::page>
    <div class="bg-white p-6 rounded-md shadow-sm">
        <form wire:submit.prevent="create">
            {{ $this->form }}

            <div class="p-3 rounded-md shadow-sm">
                <button type="submit" class="inline-flex items-center px-4 py-2 mt-1 text-sm font-medium text-white border border-transparent rounded-md shadow-sm hover:bg-yellow-600" style="background-color: gold;">
                    Update
                </button>
                <button type="reset" class="inline-flex items-center px-4 py-2 mt-1 text-sm font-medium text-black border border-transparent rounded-md shadow-sm" style="background-color: white;">
                    Reset
                </button>
            </div>
        </form>
    </div>

    @if ($data)
        <div class="bg-white p-6 mt-6 rounded-md shadow-sm">
            <h2 class="text-xl font-semibold mb-4">Faculty Records</h2>
            <div style="overflow-x: auto;">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            @foreach(array_keys($data) as $field)
                                <th class="px-4 py-2 border">{{ ucwords(str_replace('_', ' ', $field)) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach($data as $value)
                                <td class="px-4 py-2 border">{{ $value }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-filament-panels::page>