<x-filament-panels::page>
    <div class="p-6 rounded-md shadow-sm">
        <form wire:submit.prevent="printReport">
            {{ $this->form }}
            <div class="p-3 rounded-md shadow-sm">
                <button type="submit" class="inline-flex items-center px-4 py-2 mt-1 text-sm font-medium text-white border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" style="background-color: #ca8a04;" onmouseover="this.style.backgroundColor='#eab308'" onmouseout="this.style.backgroundColor='#ca8a04'">
                    Print
                </button>
                <button type="button" class="inline-flex items-center px-4 py-2 mt-1 text-sm font-medium text-white border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" style="background-color: #ca8a04;" onmouseover="this.style.backgroundColor='#eab308'" onmouseout="this.style.backgroundColor='#ca8a04'">
                        Reset
                </button>
            </div>
        </form>
    </div>

    <div class="mt-4 text-center font-bold text-2xl">
        <p>Student Information</p>
    </div>

    @if ($SelectedStudentId)
    <div class="p-6 mt-6 flex justify-center">
        <div class="w-full max-w-screen-lg">
            <table class="w-full border-collapse border bg-white">
                <tbody>
                    <tr>
                        <th class="px-4 text-left text-sm border bg-white">Student Number:</th>
                        <td class="px-4 border bg-white">{{ $selectedStudent->student_no ?? '' }}</td>
                    </tr>
                    <tr>
                        <th class="px-4 text-left text-sm border bg-white">Student Last Name:</th>
                        <td class="px-4 border bg-white">{{ $selectedStudent->last_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th class="px-4 text-left text-sm border bg-white">Student First Name:</th>
                        <td class="px-4 border bg-white">{{ $selectedStudent->first_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th class="px-4 text-left text-sm border bg-white">Student Middle Name:</th>
                        <td class="px-4 border bg-white">{{ $selectedStudent->middle_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th class="px-4 text-left text-sm border bg-white">PLM Email Address:</th>
                        <td class="px-4 border bg-white">{{ $selectedStudent->plm_email ?? '' }}</td>
                    </tr>
                    <tr>
                        <th class="px-4 text-left text-sm border bg-white">Mobile No:</th>
                        <td class="px-4 border bg-white">{{ $selectedStudent->mobile_no ?? '' }}</td>
                    </tr>
                    <tr>
                        <th class="px-4 text-left text-sm border bg-white">Date of Birth:</th>
                        <td class="px-4 border bg-white">{{ $selectedStudent->birthdate ?? '' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if ($showTable)
        <div class="p-6 rounded-md shadow-sm">
            {{ $this->table }}
        </div>
    @endif
</x-filament-panels::page>