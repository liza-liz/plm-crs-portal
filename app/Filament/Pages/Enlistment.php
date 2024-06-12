<?php

namespace App\Filament\Pages;

use App\Models\Aysem;
use App\Models\StudentTerm;
use App\Models\Block;
use App\Models\Program;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Action as HeaderAction;
use Illuminate\Support\Facades\Session;

class Enlistment extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Enrollment';
    protected static string $view = 'filament.pages.enrollment-enlistment';

    public $selectedYearLevel = 1;
    public $showDetails = false;
    public $view_student_no;
    public $view_full_name;
    public $view_registration_status;
    public $view_student_block;
    public $view_year_level;
    public $view_program_code, $view_section;
    public $blockCapacity;

    public function setYearLevel(int $yearLevel)
    {
        $this->selectedYearLevel = $yearLevel;
        $this->resetTable();
    }

    protected function resetTable()
    {
        $this->dispatch('$refresh');
    }

    protected function getTabs(): array
    {
        return [
            'First Year' => 1,
            'Second Year' => 2,
            'Third Year' => 3,
            'Fourth Year' => 4,
        ];
    }

    public function table(Table $table): Table
    {
        $program = Program::where('program_code', 'BSCS')->first();

        return $table
            ->query(
                StudentTerm::query()
                    ->select('student_terms.*', 'students.student_no', 'students.full_name', 'blocks.section', 'programs.program_code', 'registration_statuses.registration_status')
                    ->join('students', 'students.student_no', '=', 'student_terms.student_no')
                    ->leftJoin('blocks', 'blocks.id', '=', 'student_terms.block_id')
                    ->leftJoin('programs', 'programs.id', '=', 'student_terms.program_id')
                    ->leftJoin('registration_statuses', 'registration_statuses.id', '=', 'student_terms.registration_status_id')
                    ->where('student_terms.year_level', $this->selectedYearLevel)
                    ->where('student_terms.program_id', $program->id)
            )
            ->columns([
                TextColumn::make('student_no')->label('Student Number'),
                TextColumn::make('full_name')->label('Student Name')
                    ->sortable(),
                TextColumn::make('registration_status')->label('Registration Status'),
                TextColumn::make('enrolled')
                    ->label('Enrollment Status')
                    ->formatStateUsing(fn($state) => $state ? 'Enrolled' : 'Enlisted'),
                TextColumn::make('section')->label('Block')
                    ->sortable(),
                TextColumn::make('year_level')
                    ->label('Year Level'),
            ])
            ->filters([
                SelectFilter::make('enrolled')
                    ->label('Enrollment Status')
                    ->options([
                        1 => 'Enrolled',
                        0 => 'Enlisted',
                    ]),
            ])
            ->actions([
                Action::make('edit')
                    ->label('View')
                    ->form(function ($record) use ($program) {
                        $currentAYSem = Aysem::orderBy('date_start', 'desc')->first();

                        $blocks = Block::where('program_id', $program->id)
                            ->where('year_level', $record->year_level)
                            ->where('aysem_id', $currentAYSem->id)
                            ->pluck('section', 'id');

                        return [
                            TextInput::make('student_no')
                                ->label('Student ID')
                                ->default($record->student_no)
                                ->disabled(),
                            TextInput::make('full_name')
                                ->label('Student Name')
                                ->default($record->full_name)
                                ->disabled(),
                            TextInput::make('registration_status')
                                ->label('Registration Status')
                                ->default($record->registration_status)
                                ->disabled(),
                            Select::make('block_id')
                                ->label('Block')
                                ->options($blocks)
                                ->default($record->block_id)
                                ->required(),
                            TextInput::make('year_level')
                                ->label('Year Level')
                                ->default($record->year_level)
                                ->disabled(),
                        ];
                    })
                    ->action(function (array $data, StudentTerm $record) {
                        $record->update([
                            'block_id' => $data['block_id'],
                        ]);
                        $this->viewStudentDetails($record->id);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Edit Student Block'),
            ])
            ->bulkActions([
                BulkAction::make('assignSpecificBlock')
                    ->label('Assign Specific Block')
                    ->form(function () use ($program) {
                        $currentAYSem = Aysem::orderBy('date_start', 'desc')->first();

                        $blocks = Block::where('program_id', $program->id)
                            ->where('year_level', $this->selectedYearLevel)
                            ->where('aysem_id', $currentAYSem->id)
                            ->orderByRaw('CAST(section AS UNSIGNED)')
                            ->pluck('section', 'id');

                        return [
                            Select::make('block_id')
                                ->label('Select Block')
                                ->options($blocks)
                                ->required(),
                        ];
                    })
                    ->action(function (array $data, $records) {
                        foreach ($records as $record) {
                            $record->update([
                                'block_id' => $data['block_id'],
                            ]);
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Assign Block'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            HeaderAction::make('setBlockCapacity')
                ->label('Set Block Capacity')
                ->form([
                    TextInput::make('block_capacity')
                        ->label('Block Capacity')
                        ->numeric()
                        ->required()
                        ->default(Session::get('block_capacity', 30)),
                ])
                ->action(function (array $data) {
                    Session::put('block_capacity', $data['block_capacity']);
                    $this->dispatch('$refresh');
                })
                ->requiresConfirmation()
                ->modalHeading('Set Block Capacity'),
            HeaderAction::make('automaticEnlistment')
                ->label('Automatic Enlistment')
                ->form([
                    Select::make('assignment_type')
                        ->label('Assignment Type')
                        ->options([
                            'alphabetical' => 'Alphabetical',
                            'random' => 'Random',
                        ])
                        ->required(),
                ])
                ->action(function (array $data) {
                    if (!Session::has('block_capacity')) {
                        // Handle the case where block capacity is not set
                        $this->dispatch('notify', 'Please set the block capacity first.');
                        return;
                    }

                    $blockCapacity = Session::get('block_capacity');

                    if ($data['assignment_type'] === 'alphabetical') {
                        $this->batchAssignAlphabetical($blockCapacity);
                    } elseif ($data['assignment_type'] === 'random') {
                        $this->batchAssignRandom($blockCapacity);
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Automatic Enlistment')
                ->disabled(!Session::has('block_capacity')),
        ];
    }

    public function batchAssignBlocks($records, int $blockCapacity)
    {
        $currentAYSem = Aysem::orderBy('date_start', 'desc')->first();
        if (!$currentAYSem) {
            abort(404, 'Current academic year and semester not found.');
        }

        $program = Program::where('program_code', 'BSCS')->first();

        $blocks = Block::where('program_id', $program->id)
            ->where('year_level', $this->selectedYearLevel)
            ->where('aysem_id', $currentAYSem->id)
            ->orderBy('section') // Ensure blocks are ordered by their section
            ->get();

        $blockIndex = 0;
        $studentCount = 0;

        foreach ($records as $record) {
            if ($studentCount >= $blockCapacity) {
                $studentCount = 0;
                $blockIndex++;
                if ($blockIndex >= count($blocks)) {
                    $blockIndex = 0; // Reset to first block if we run out of blocks
                }
            }

            // Assign block to student
            $record->update(['block_id' => $blocks[$blockIndex]->id]);
            $studentCount++;
        }
    }

    protected function batchAssignAlphabetical($blockCapacity)
    {
        // Get the records sorted alphabetically by student name
        $records = StudentTerm::query()
            ->select('student_terms.*')
            ->join('students', 'students.student_no', '=', 'student_terms.student_no')
            ->where('student_terms.year_level', $this->selectedYearLevel)
            ->where('student_terms.program_id', Program::where('program_code', 'BSCS')->first()->id)
            ->orderBy('students.full_name')
            ->get();

        $this->batchAssignBlocks($records, $blockCapacity);
    }

    protected function batchAssignRandom($blockCapacity)
    {
        // Get the records in random order
        $records = StudentTerm::query()
            ->select('student_terms.*')
            ->join('students', 'students.student_no', '=', 'student_terms.student_no')
            ->where('student_terms.year_level', $this->selectedYearLevel)
            ->where('student_terms.program_id', Program::where('program_code', 'BSCS')->first()->id)
            ->inRandomOrder()
            ->get();

        $this->batchAssignBlocks($records, $blockCapacity);
    }

    public function viewStudentDetails($id)
    {
        $student = StudentTerm::where('student_terms.id', $id)
            ->join('students', 'students.student_no', '=', 'student_terms.student_no')
            ->leftJoin('blocks', 'blocks.id', '=', 'student_terms.block_id')
            ->leftJoin('programs', 'programs.id', '=', 'student_terms.program_id')
            ->leftJoin('registration_statuses', 'registration_statuses.id', '=', 'student_terms.registration_status_id')
            ->where('student_terms.program_id', Program::where('program_code', 'BSCS')->first()->id)
            ->first(['students.*', 'student_terms.*', 'blocks.section', 'programs.program_code', 'registration_statuses.registration_status']);

        if (!$student) {
            abort(404);
        }

        $this->view_student_no = $student->student_no;
        $this->view_full_name = $student->full_name;
        $this->view_registration_status = $student->registration_status;
        $this->view_student_block = $student->block_id;
        $this->view_year_level = $student->year_level;
        $this->view_section = $student->section;
        $this->view_program_code = $student->program_code;
        $this->showDetails = false; // added false (this should be not displayed)
    }

    public function updateStudentDetails()
    {
        $studentTerm = StudentTerm::where('student_no', $this->view_student_no)->first();
        if ($studentTerm) {
            $studentTerm->block_id = $this->view_student_block;
            $studentTerm->save();
            $this->showDetails = false;
            $this->dispatch('notify', 'Student details updated successfully.');
        }
    }
}
