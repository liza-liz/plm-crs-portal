<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
    
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        dd($record);
    
        return $record;
    }

    // protected function fillForm(): void
    // {
    //     // Load the main record data
    //     $this->record->load('instructor', 'classSchedules');

    //     // Debugging
    //     // dd($this->record);

    //     // Call the parent method to ensure default functionality
    //     parent::fillForm();

    //     // Fill the form with related data
    //     $this->form->fill([
    //         'faculty' => $this->record->faculty ? $this->record->faculty->toArray() : [],
    //         'schedules' => $this->record->schedules ? $this->record->schedules->toArray() : [],
    //     ]);
    // }
}
