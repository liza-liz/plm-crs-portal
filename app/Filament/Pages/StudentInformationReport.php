<?php

namespace App\Filament\Pages;

use App\Models\Student;
use App\Models\Aysem;
use App\Models\Classes;
use App\Models\Block;
use App\Models\College;
use Filament\Pages\Page;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Livewire\Component;

class StudentInformationReport extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static string $view = 'filament.pages.student-information-report';
    protected static ?string $navigationGroup = 'Student Affairs';

    public ?array $data = [];

    public $showTable = false;

    public $selectedStudent;
    public $studentId;
    public $SelectedStudentId = false; // New property to hold the selected student's data

    public function mount()
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
    // Fetch student options with Student ID and Last Name as the label
    $students = Student::all();
    $studentOptions = [];
    foreach ($students as $student) {
        $studentOptions[$student->student_no] = $student->student_no . ' (' . $student->last_name . ', ' . $student->first_name .')';
    }

    return $form
        ->columns(2)
        ->schema([
            Components\Select::make('student_no')
                ->label('Student ID and Name') // Change the label to indicate both Student ID and Last Name
                ->placeholder('Select Student') // Change the placeholder accordingly
                ->options($studentOptions) // Use fetched student options
                ->required()
                ->reactive()
                ->searchable(),
            Components\Select::make('aysem_id')
                ->label('Ay-Sem')
                ->placeholder('Ay-Sem')
                ->options(Aysem::all()->pluck('academic_year_sem', 'id')->toArray())
          		->searchable()
                ->required(),
        ])->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Classes::query())
            ->columns([
                TextColumn::make('course.subject_code')
                    ->label('Subject Code')
                    ->formatStateUsing(function ($state, $record) {
                        return $state;
                    })
                    ->sortable(),
                TextColumn::make('block.section')
                    ->label('Section')
                    ->sortable(),
                TextColumn::make('course.subject_title')
                    ->label('Subject Title'),
                TextColumn::make('actual_units')
                    ->label('Units'),
                TextColumn::make('classSchedules.schedule_name')
                    ->wrap()
                    ->label('Schedule'),
                TextColumn::make('instructors.faculty_name')
                    ->label('Faculty')
            ]);
    }

    public function printReport()
    {
        // Set flag to true to show the table
        $this->showTable = true;
        $this->SelectedStudentId = true;
        $this->selectedStudent = Student::find($this->data['student_no']);
    }

    public function resetForm()
    {
        $this->form->fill([]);
        $this->data = [];
        $this->SelectedStudentId = false; // Reset selected student ID
        $this->selectedStudent = null; // Reset selected student data

        // Reset table data by setting an empty query
        $this->getTable()->query(function () {
        return Classes::where('id', '=', null);
    });
    }
}