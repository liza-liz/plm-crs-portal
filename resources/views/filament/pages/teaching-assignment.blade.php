<x-filament-panels::page>
    <div class="p-6 rounded-md shadow-sm">
        <form wire:submit.prevent="printReport">
            {{ $this->form }}
            <div class="p-3 rounded-md shadow-sm">
                <button type="submit" class="inline-flex items-center px-4 py-2 mt-1 text-sm font-medium text-white border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" style="background-color: #ca8a04;" onmouseover="this.style.backgroundColor='#eab308'" onmouseout="this.style.backgroundColor='#ca8a04'">
                    Submit
                </button>
            </div>
        </form>
    </div>

    @if ($showTable)
        <div class="p-3 rounded-md shadow-sm">
            <button onclick="printTable()" class="inline-flex items-center px-4 py-2 mt-1 text-sm font-medium text-white border border-transparent rounded-md shadow-sm hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" style="background-color: gold;">
                Print Table
            </button>
        </div>
        <div id="printableTable" class="p-6 rounded-md shadow-sm">
            {{ $this->table }}
        </div>
    @endif

    <script>
        function printTable() {
            var printContents = document.getElementById('printableTable').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;

            window.print();

            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>
</x-filament-panels::page>
