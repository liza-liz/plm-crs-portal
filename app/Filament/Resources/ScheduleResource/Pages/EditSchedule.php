<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\ScheduleResource;
use App\Models\ClassMode;
use App\Models\Days;
use App\Models\Room;
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
        $classData = [
            'course_id' => intval($data['course_id']),
            'block_id' => intval($data['block_id']),
            'nstp_activity' => $data['nstp_activity'],
            'credited_units' => $data['credited_units'],
            'actual_units' => $data['actual_units'],
            'slots' => $data['slots'],
            'minimum_year_level' => $data['minimum_year_level'],
            'instruction_language' => $data['instruction_language'],
            'parent_class_code' => $data['parent_class_code'],
            'link_type' => $data['link_type'],
        ];

        $record->update($classData);

        // Step 1: Collect existing restriction IDs for the class_id
        $existingFacultyIds = $record->instructor()
        ->where('class_id', $record->id)
        ->pluck('instructor_id')
        ->toArray();

        // Initialize an array to keep track of processed faculty IDs
        $processedFacultyIds = [];

        // Step 2: Process each restrictionData item
        foreach ($data['faculty'] as $facultyData) {
            // Check if the restriction exists in the database
            $existingFaculty = $record->instructor()->where([
                'class_id' => $record->id,
                'instructor_id' => $facultyData['instructor_id'],
            ])->first();
            
            if ($existingFaculty) {
                // If the faculty exists, add its ID to the processed list
                $processedFacultyIds[] = $existingFaculty->id;
            } else {
                // If the faculty does not exist, create it
                $newFaculty = $record->instructor()->create($facultyData);
                // Add the new restriction's ID to the processed list
                $processedFacultyIds[] = $newFaculty->id;
            }
        }

        // Step 3: Delete unmatched faculty
        $unmatchedFacultyIds = array_diff($existingFacultyIds, $processedFacultyIds);
        if (!empty($unmatchedFacultyIds)) {
            $record->instructor()->whereIn('id', $unmatchedFacultyIds)->delete();
        }

        // Step 1: Collect existing restriction IDs for the class_id
        $existingScheduleIds = $record->classSchedules()
        ->where('class_id', $record->id)
        ->pluck('id')
        ->toArray();

        // Initialize an array to keep track of processed faculty IDs
        $processedScheduleIds = [];

        // Step 2: Process each restrictionData item
        foreach ($data['schedules'] as $ScheduleData) {

            $dayCode = Days::where('id', $ScheduleData['day_id'])->first()->day_code;
            $roomName = Room::where('id', $ScheduleData['room_id'])->first()->room_name;
            $modeCode = ClassMode::where('id', $ScheduleData['class_mode_id'])->first()->mode_code;

            // Format start_time and end_time
            $formattedStartTime = date("g:i A", strtotime($ScheduleData['start_time']));
            $formattedEndTime = date("g:i A", strtotime($ScheduleData['end_time']));

            // Concatenate to form schedule_name
            $scheduleName = "{$dayCode} {$formattedStartTime} - {$formattedEndTime} {$modeCode} {$roomName}";

            // Check if the restriction exists in the database
            $existingSchedule = $record->classSchedules()->where([
                'class_id' => $record->id,
                'day_id' => $ScheduleData['day_id'],
                'start_time' => $ScheduleData['start_time'],
                'end_time' => $ScheduleData['end_time'],
                'room_id' => $ScheduleData['room_id'],
            ])->first();
            
            if ($existingSchedule) {
                dd($existingSchedule);
                // If the Schedule exists, add its ID to the processed list
                $processedScheduleIds[] = $existingSchedule->id;
            } else {
                // If the Schedule does not exist, create it
                $ScheduleData['schedule_name'] = $scheduleName;

                $newSchedule = $record->classSchedules()->create($ScheduleData);
                // Add the new restriction's ID to the processed list
                $processedScheduleIds[] = $newSchedule->id;
            }
        }

        // Step 3: Delete unmatched faculty
        $unmatchedScheduleIds = array_diff($existingScheduleIds, $processedScheduleIds);
        if (!empty($unmatchedScheduleIds)) {
            $record->classSchedules()->whereIn('id', $unmatchedScheduleIds)->delete();
        }

        // Step 1: Collect existing restriction IDs for the class_id
        $existingRestrictionIds = $record->classRestrictions()
        ->where('class_id', $record->id)
        ->pluck('id')
        ->toArray();

        // Initialize an array to keep track of processed restriction IDs
        $processedRestrictionIds = [];

        // Step 2: Process each restrictionData item
        foreach ($data['restrictions'] as $restrictionData) {
            // Check if the restriction exists in the database
            $existingRestriction = $record->classRestrictions()->where([
                'class_id' => $record->id,
                'scope' => $restrictionData['scope'],
                'restriction' => $restrictionData['restriction'],
            ])->first();

            if ($existingRestriction) {
                // If the restriction exists, add its ID to the processed list
                $processedRestrictionIds[] = $existingRestriction->id;
            } else {
                // If the restriction does not exist, create it
                $newRestriction = $record->classRestrictions()->create($restrictionData);
                // Add the new restriction's ID to the processed list
                $processedRestrictionIds[] = $newRestriction->id;
            }
        }

        // Step 3: Delete unmatched restrictions
        $unmatchedRestrictionIds = array_diff($existingRestrictionIds, $processedRestrictionIds);
        if (!empty($unmatchedRestrictionIds)) {
            $record->classRestrictions()->whereIn('id', $unmatchedRestrictionIds)->delete();
        }
        
        return $record;
    }

    protected function fillForm(): void
    {
        // // Load the main record data
        $relations = $this->record->load(['instructor', 'classSchedules', 'classRestrictions']);
        $instructor = $relations->instructor;
        $classSchedule = $relations->classSchedules;
        $classRestriction = $relations->classRestrictions;

        //Debugging
        // dd($instructor, $classSchedule, $classRestriction);

        // Prepare main record data for form filling
        $mainRecordData = $this->record->toArray(); // Assuming this returns the main record data in the desired format
        
        // Fill the form with the main record data and related data
        $this->form->fill(array_merge($mainRecordData, [
            'faculty' => $instructor ? $instructor->toArray() : [],
            'schedules' => $classSchedule ? $classSchedule->toArray() : [],
            'restrictions' => $classRestriction ? $classRestriction->toArray() : [],
        ]));
    }
}
