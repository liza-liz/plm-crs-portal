<?php

namespace App\Filament\Pages;

use App\Models\AddDropRequest;
use App\Models\Course;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\Section;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms;

class AddDropRequests extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Transactions';
    protected static string $view = 'filament.pages.add-drop-requests';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AddDropRequest::query()
                    ->leftJoin('student_terms', 'add_drop_requests.student_no', '=', 'student_terms.student_no')
                    ->select('add_drop_requests.*', 'student_terms.year_level')
            )
            ->columns([
                TextColumn::make('student_no')->label('Student Number')->sortable(),
                TextColumn::make('year_level')->label('Year Level')->sortable(),
                TextColumn::make('date_of_request')->label('Date of Request')->sortable(),
                TextColumn::make('status')->label('Status')->sortable(),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->form(function ($record) {
                        $addedCourses = $record->added_courses;
                        $droppedCourses = $record->dropped_courses;
                        $reason = $record->reason;
                        $studyPlan = $record->decoded_study_plan;

                        return [
                            Section::make('Added Courses')
                                ->schema([
                                    Repeater::make('added_courses')
                                        ->schema([
                                            TextInput::make('subject_code')->label('Course Code')->disabled(),
                                            TextInput::make('course_name')->label('Course Name')->disabled(),
                                            TextInput::make('units')->label('Units')->disabled(),
                                            TextInput::make('days_time')->label('Days/Time')->disabled(),
                                            TextInput::make('room')->label('Room')->disabled(),
                                        ])
                                        ->defaultItems(count($addedCourses))
                                        ->afterStateHydrated(function ($set) use ($addedCourses) {
                                            $set('added_courses', $addedCourses);
                                        })
                                        ->columns(5)
                                        ->disabled(),
                                ]),
                            Section::make('Dropped Courses')
                                ->schema([
                                    Repeater::make('dropped_courses')
                                        ->schema([
                                            TextInput::make('subject_code')->label('Course Code')->disabled(),
                                            TextInput::make('course_name')->label('Course Name')->disabled(),
                                            TextInput::make('units')->label('Units')->disabled(),
                                            TextInput::make('days_time')->label('Days/Time')->disabled(),
                                            TextInput::make('room')->label('Room')->disabled(),
                                        ])
                                        ->defaultItems(count($droppedCourses))
                                        ->afterStateHydrated(function ($set) use ($droppedCourses) {
                                            $set('dropped_courses', $droppedCourses);
                                        })
                                        ->columns(5)
                                        ->disabled(),
                                ]),
                            Section::make('Reason')
                                ->schema([
                                    TextInput::make('reason')->label('Reason')->default($reason)->disabled(),
                                ]),
                            Section::make('Study Plan')
                                ->schema([
                                    Repeater::make('study_plan')
                                        ->schema([
                                            TextInput::make('subject_code')->label('Course Code')->disabled(),
                                            TextInput::make('subject_title')->label('Course Title')->disabled(),
                                            TextInput::make('units')->label('Units')->disabled(),
                                            TextInput::make('pre_requisite')->label('Pre(Co)-Requisites')->disabled(),
                                        ])
                                        ->defaultItems(count($studyPlan))
                                        ->afterStateHydrated(function ($set) use ($studyPlan) {
                                            $set('study_plan', $studyPlan);
                                        })
                                        ->columns(4)
                                        ->disabled(),
                                ]),
                        ];
                    })
                    ->modalHeading('Add/Drop Request Details')
                    ->modalButton('Close')
                    ->modalWidth('6xl'),

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
                    ->modalHeading('Approve Add/Drop Request')
                    ->modalSubheading('Are you sure you want to approve this add/drop request?')
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
                    ->modalHeading('Reject Add/Drop Request')
                    ->modalSubheading('Are you sure you want to reject this add/drop request?')
                    ->modalButton('Reject'),
            ]);
    }
}
