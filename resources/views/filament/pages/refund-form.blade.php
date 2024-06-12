<x-filament-panels::page>
    <div class="flex flex-col items-center justify-center h-full">
        <form wire:submit.prevent="submitForm" method="post" class="w-full max-w-lg">
            <div class="grid grid-cols-1 gap-4 mb-8">
                {{ $this->form }}
            </div>

            <div class="flex justify-center mt-6">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white border border-transparent rounded-md shadow-sm" style="background-color: #ca8a04;" onmouseover="this.style.backgroundColor='#eab308'" onmouseout="this.style.backgroundColor='#ca8a04'">
                    Submit
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
                        <td class="px-4 border bg-white">{{ $selectedStudent->student_no }}</td>
                    </tr>
                    <tr>
                        <th class="px-4 text-left text-sm border bg-white">Student Name:</th>
                        <td class="px-4 border bg-white">{{ $selectedStudent->first_name }} {{ $selectedStudent->middle_name }} {{ $selectedStudent->last_name }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if ($showTable)
        <div class="mt-8 justify-center h-full">
            {{ $this->table }}
        </div>
    
        <div class="mt-16">
            <table class="h-full w-full bg-white border border-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border-b">Non-Refundable Fees</th>
                        <th class="px-4 py-2 border-b">Type of Refund</th>
                        <th class="px-4 py-2 border-b">Rate of Refund</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-4 py-2 border-b">Registration Fee: 74.00</td>
                        <td class="px-4 py-2 border-b">
                            <label><input type="checkbox"> 100% before the start of classes</label>
                        </td>
                        <td class="px-4 py-2 border-b">
                            <label><input type="checkbox"> 100% before the start of classes</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 border-b">Medical/Dental Fee: 293.00</td>
                        <td class="px-4 py-2 border-b">
                            <label><input type="checkbox"> Dropping</label>
                        </td>
                        <td class="px-4 py-2 border-b">
                            <label><input type="checkbox"> 80% first week after the first week of classes</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 border-b">* Entrance Fee: 146.00</td>
                        <td class="px-4 py-2 border-b">
                            <label><input type="checkbox"> Dissolved Class</label>
                        </td>
                        <td class="px-4 py-2 border-b">
                            <label><input type="checkbox"> 50% within 2nd, 3rd, 4th week after the first day of classes</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 border-b">* Deposit for New Student: 74.00</td>
                        <td class="px-4 py-2 border-b">
                            <label><input type="checkbox"> Withdraw/Cancellation</label>
                        </td>
                        <td class="px-4 py-2 border-b">
                            <label><input type="checkbox"> No Refund</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 border-b">* University ID: 40.00</td>
                        <td class="px-4 py-2 border-b">
                            <label><input type="checkbox"> Excess of Payment</label>
                        </td>
                        <td class="px-4 py-2 border-b">
                            <label><input type="checkbox"> No Refund</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 border-b">* Guidance Fee: 146.00</td>
                        <td class="px-4 py-2 border-b">
                            <label><input type="checkbox"> No Refund</label>
                        </td>
                        <td class="px-4 py-2 border-b">
                            <label><input type="checkbox"> No Refund</label>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="mt-2 text-sm text-gray-600">Note: * - if Applicable</p>
        </div>
    @elseif ($SelectedStudentId)
        <div class="mt-8 justify-center h-full">
            <p class="text-red-500">No record found.</p>
        </div>
    @endif
</x-filament-panels::page>
