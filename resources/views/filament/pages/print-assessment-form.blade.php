<x-filament-panels::page>
    <div class="text-center">
    </div>

    <div class="p-6 rounded-md shadow-sm">
        <form wire:submit.prevent="submitReport">
            {{ $this->form }}
            <div class="p-3 rounded-md shadow-sm">
                <button type="submit" class="inline-flex items-center px-4 py-2 mt-1 text-sm font-medium text-white border border-transparent rounded-md shadow-sm hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" style="background-color: #ca8a04;" onmouseover="this.style.backgroundColor='#eab308'" onmouseout="this.style.backgroundColor='#ca8a04'">
                    Submit
                </button>
                <button type="button" wire:click="resetForm" class="inline-flex items-center px-4 py-2 mt-1 text-sm font-medium text-white border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" style="background-color: #ca8a04;" onmouseover="this.style.backgroundColor='#eab308'" onmouseout="this.style.backgroundColor='#ca8a04'">
                    Reset
                </button>

            </div>
        </form>
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

    {{-- Display assessment details --}}
    @if ($showDetails)
       
            <div class="container mx-auto">
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                           Assessment Details
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Description & Amount
                        </p>
                    </div>
                    <div class="border-t border-gray-200">
                        <dl>
                            @foreach ([
                                'Tuition Fee' => '16822',
                                'Total' => '6739.00',
                                'Laboratory Fee' => '1000.00',
                                'Development Fund' => '146.00',
                                'Ang Pamantasan Fee' => '50.00',
                                'Supreme Student Council' => '50.00',
                                'Previous Payment' => '0.00',
                                'Total Assessment Fee' => '10426.00',
                            ] as $label => $field)
                                <div class="{{ $loop->iteration % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        {{ $label }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $field }}
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>
            </div>
        @else
            <div class="mt-8">
                <p>No assessment found for the provided student number and academic year/semester.</p>
            </div>
       
    @endif
</x-filament-panels::page>
