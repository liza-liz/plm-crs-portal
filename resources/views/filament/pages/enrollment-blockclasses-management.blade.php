<x-filament::page>
    <div>
        <div style="margin-top: -1rem; margin-bottom: 1rem; display: flex; gap: 0.5rem;">
            <button wire:click="setYearLevel(1)"
                style="background-color: #F59E0B; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; cursor: pointer; outline: none; border: none; transition: background-color 0.2s ease-in-out; font-weight: bold; {{ $selectedYearLevel === 1 ? 'background-color: #D97706;' : '' }}"
                onmouseover="this.style.backgroundColor='#D97706'"
                onmouseout="this.style.backgroundColor='{{ $selectedYearLevel === 1 ? '#D97706' : '#F59E0B' }}'">First
                Year</button>
            <button wire:click="setYearLevel(2)"
                style="background-color: #F59E0B; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; cursor: pointer; outline: none; border: none; transition: background-color 0.2s ease-in-out; font-weight: bold; {{ $selectedYearLevel === 2 ? 'background-color: #D97706;' : '' }}"
                onmouseover="this.style.backgroundColor='#D97706'"
                onmouseout="this.style.backgroundColor='{{ $selectedYearLevel === 2 ? '#D97706' : '#F59E0B' }}'">Second
                Year</button>
            <button wire:click="setYearLevel(3)"
                style="background-color: #F59E0B; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; cursor: pointer; outline: none; border: none; transition: background-color 0.2s ease-in-out; font-weight: bold; {{ $selectedYearLevel === 3 ? 'background-color: #D97706;' : '' }}"
                onmouseover="this.style.backgroundColor='#D97706'"
                onmouseout="this.style.backgroundColor='{{ $selectedYearLevel === 3 ? '#D97706' : '#F59E0B' }}'">Third
                Year</button>
            <button wire:click="setYearLevel(4)"
                style="background-color: #F59E0B; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; cursor: pointer; outline: none; border: none; transition: background-color 0.2s ease-in-out; font-weight: bold; {{ $selectedYearLevel === 4 ? 'background-color: #D97706;' : '' }}"
                onmouseover="this.style.backgroundColor='#D97706'"
                onmouseout="this.style.backgroundColor='{{ $selectedYearLevel === 4 ? '#D97706' : '#F59E0B' }}'">Fourth
                Year</button>
        </div>

        {{ $this->table }}

    </div>
</x-filament::page>
