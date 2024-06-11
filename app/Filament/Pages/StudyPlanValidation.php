<?php

namespace App\Filament\Pages;

use App\Models\StudyPlanValidations;
use App\Models\Course;
use App\Models\Classes;
use App\Models\Aysem;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms;

class StudyPlanValidation extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Transactions';
    protected static string $view = 'filament.pages.study-plan-validation-page';

    public function table(Table $table): Table
    {
        return $table
            ->query(StudyPlanValidations::query())
            ->columns([
                TextColumn::make('student_no')->label('Student Number')->sortable(),
                TextColumn::make('year_level')->label('Year Level')->sortable(),
                TextColumn::make('date_of_request')->label('Date of Request')->sortable(),
                TextColumn::make('status')->label('Status')->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->label('View Study Plan')
                    ->form(function ($record) {
                        $studyPlanIds = json_decode($record->study_plan, true);
                        $courses = Course::whereIn('subject_code', $studyPlanIds)->get();

                        return [
                            Repeater::make('courses')
                                ->label('Study Plan')
                                ->schema([
                                    TextInput::make('subject_code')
                                        ->label('Course Code')
                                        ->disabled(),
                                    TextInput::make('subject_title')
                                        ->label('Course Title')
                                        ->disabled(),
                                    TextInput::make('units')
                                        ->label('Units')
                                        ->disabled(),
                                    TextInput::make('pre_requisite')
                                        ->label('Pre(Co)-Requisites')
                                        ->disabled(),
                                    TextInput::make('year_level')
                                        ->label('Year Level')
                                        ->disabled(),
                                    TextInput::make('semester')
                                        ->label('Semester')
                                        ->disabled(),
                                ])
                                ->defaultItems(count($courses))
                                ->afterStateHydrated(function ($set, $state) use ($courses) {
                                    $courseData = $courses->map(function ($course) {
                                        $class = Classes::where('course_id', $course->id)->first();
                                        $aysem = $class ? Aysem::find($class->aysem_id) : null;
                                        return array_merge($course->toArray(), [
                                            'year_level' => $class ? $class->minimum_year_level : '', // empty string
                                            'semester' => $aysem ? $aysem->semester : '', // empty string
                                        ]);
                                    });
                                    $set('courses', $courseData->toArray());
                                })
                                ->columns(6)
                                ->disabled(),
                        ];
                    })
                    ->modalHeading('Study Plan Details')
                    ->modalButton('Close'),

                Action::make('approve')
                    ->label('Approve')
                    ->action(function ($record) {
                        $record->update(['status' => 'Approved']);
                    })
                    ->color('success')
                    ->disabled(function ($record) {
                        return $record->status !== 'Pending';
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Approve Study Plan')
                    ->modalSubheading('Are you sure you want to approve this study plan?')
                    ->modalButton('Approve'),

                Action::make('reject')
                    ->label('Reject')
                    ->action(function ($record) {
                        $record->update(['status' => 'Rejected']);
                    })
                    ->color('danger')
                    ->disabled(function ($record) {
                        return $record->status !== 'Pending';
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Reject Study Plan')
                    ->modalSubheading('Are you sure you want to reject this study plan?')
                    ->modalButton('Reject'),
            ]);
    }
}