<?php

namespace App\Filament\Pages;

use App\Models\LOARequest;
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
use Filament\Forms\Components\Section;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms;

class LOARequests extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Transactions';
    protected static string $view = 'filament.pages.loa-requests';

    public function table(Table $table): Table
    {
        return $table
            ->query(LOARequest::query())
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
                        $repeaters = [];

                        for ($i = 1; $i <= 4; $i++) {
                            $year = [];
                            for ($j = 1; $j <= 2; $j++) {
                                $repeater = Repeater::make($i . $j)
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
                                    ])
                                    ->label('')
                                    ->defaultItems(count($courses))
                                    ->columns(4)
                                    ->disabled();
                                $year[] = $repeater;
                            }
                            $repeaters[] = $year;
                        }

                        return ([
                            Section::make('First Year, First Semester')
                                ->schema([
                                    $repeaters[0][0]->afterStateHydrated(function ($set, $state) use ($courses) {
                                        $courseData = $courses->map(function ($course) {
                                            $class = Classes::where('course_id', $course->id)->first();
                                            $aysem = $class ? Aysem::find($class->aysem_id) : null;
                                            return array_merge($course->toArray(), [
                                                'year_level' => $class ? $class->minimum_year_level : '', // empty string
                                                'semester' => $aysem ? $aysem->semester : '', // empty string
                                            ]);
                                        })->where('year_level', 1)->where('semester', 1)->values(); // Sort by year level
                                        $set('11', $courseData->toArray());
                                    })
                                ]),

                            Section::make('First Year, Second Semester')
                                ->schema([
                                    $repeaters[0][1]->afterStateHydrated(function ($set, $state) use ($courses) {
                                        $courseData = $courses->map(function ($course) {
                                            $class = Classes::where('course_id', $course->id)->first();
                                            $aysem = $class ? Aysem::find($class->aysem_id) : null;
                                            return array_merge($course->toArray(), [
                                                'year_level' => $class ? $class->minimum_year_level : '', // empty string
                                                'semester' => $aysem ? $aysem->semester : '', // empty string
                                            ]);
                                        })->where('year_level', 1)->where('semester', 2)->values(); // Sort by year level
                                        $set('12', $courseData->toArray());
                                    })
                                ]),

                            Section::make('Second Year, First Semester')
                                ->schema([
                                    $repeaters[1][0]->afterStateHydrated(function ($set, $state) use ($courses) {
                                        $courseData = $courses->map(function ($course) {
                                            $class = Classes::where('course_id', $course->id)->first();
                                            $aysem = $class ? Aysem::find($class->aysem_id) : null;
                                            return array_merge($course->toArray(), [
                                                'year_level' => $class ? $class->minimum_year_level : '', // adjust year level
                                                'semester' => $aysem ? $aysem->semester : '', // empty string
                                            ]);
                                        })->where('year_level', 2)->where('semester', 1)->values(); // Sort by year level
                                        $set('21', $courseData->toArray());
                                    })
                                ]),

                            Section::make('Second Year, Second Semester')
                                ->schema([
                                    $repeaters[1][1]->afterStateHydrated(function ($set, $state) use ($courses) {
                                        $courseData = $courses->map(function ($course) {
                                            $class = Classes::where('course_id', $course->id)->first();
                                            $aysem = $class ? Aysem::find($class->aysem_id) : null;
                                            return array_merge($course->toArray(), [
                                                'year_level' => $class ? $class->minimum_year_level : '', // adjust year level
                                                'semester' => $aysem ? $aysem->semester : '', // empty string
                                            ]);
                                        })->where('year_level', 2)->where('semester', 2)->values(); // Sort by year level
                                        $set('22', $courseData->toArray());
                                    })
                                ]),

                            Section::make('Third Year, First Semester')
                                ->schema([
                                    $repeaters[2][0]->afterStateHydrated(function ($set, $state) use ($courses) {
                                        $courseData = $courses->map(function ($course) {
                                            $class = Classes::where('course_id', $course->id)->first();
                                            $aysem = $class ? Aysem::find($class->aysem_id) : null;
                                            return array_merge($course->toArray(), [
                                                'year_level' => $class ? $class->minimum_year_level : '', // adjust year level
                                                'semester' => $aysem ? $aysem->semester : '', // empty string
                                            ]);
                                        })->where('year_level', 3)->where('semester', 1)->values(); // Sort by year level
                                        $set('31', $courseData->toArray());
                                    })
                                ]),

                            Section::make('Third Year, Second Semester')
                                ->schema([
                                    $repeaters[2][1]->afterStateHydrated(function ($set, $state) use ($courses) {
                                        $courseData = $courses->map(function ($course) {
                                            $class = Classes::where('course_id', $course->id)->first();
                                            $aysem = $class ? Aysem::find($class->aysem_id) : null;
                                            return array_merge($course->toArray(), [
                                                'year_level' => $class ? $class->minimum_year_level : '', // adjust year level
                                                'semester' => $aysem ? $aysem->semester : '', // empty string
                                            ]);
                                        })->where('year_level', 3)->where('semester', 2)->values(); // Sort by year level
                                        $set('32', $courseData->toArray());
                                    })
                                ]),

                            Section::make('Fourth Year, First Semester')
                                ->schema([
                                    $repeaters[3][0]->afterStateHydrated(function ($set, $state) use ($courses) {
                                        $courseData = $courses->map(function ($course) {
                                            $class = Classes::where('course_id', $course->id)->first();
                                            $aysem = $class ? Aysem::find($class->aysem_id) : null;
                                            return array_merge($course->toArray(), [
                                                'year_level' => $class ? $class->minimum_year_level : '', // adjust year level
                                                'semester' => $aysem ? $aysem->semester : '', // empty string
                                            ]);
                                        })->where('year_level', 4)->where('semester', 1)->values(); // Sort by year level
                                        $set('41', $courseData->toArray());
                                    })
                                ]),

                            Section::make('Fourth Year, Second Semester')
                                ->schema([
                                    $repeaters[3][1]->afterStateHydrated(function ($set, $state) use ($courses) {
                                        $courseData = $courses->map(function ($course) {
                                            $class = Classes::where('course_id', $course->id)->first();
                                            $aysem = $class ? Aysem::find($class->aysem_id) : null;
                                            return array_merge($course->toArray(), [
                                                'year_level' => $class ? $class->minimum_year_level : '', // adjust year level
                                                'semester' => $aysem ? $aysem->semester : '', // empty string
                                            ]);
                                        })->where('year_level', 4)->where('semester', 2)->values(); // Sort by year level
                                        $set('42', $courseData->toArray());
                                    })
                                ]),
                        ]);
                    })
                    ->modalHeading('Study Plan Details')
                    ->modalButton('Close')
                    ->modalWidth('6xl'),

                Action::make('view_forms')
                    ->label('View All Forms')
                    ->form(function ($record) {
                        // Placeholder content
                        return [
                            Section::make('')
                                ->schema([
                                    TextInput::make('loa_form')
                                        ->default('This is where the loa form will appear supposedly.')
                                        ->disabled(),
                                ]),
                            Section::make('')
                                ->schema([
                                    TextInput::make('letter_of_request')
                                        ->default('This is where the letter of request will appear supposedly.')
                                        ->disabled(),
                                ]),
                            Section::make('')
                                ->schema([
                                    TextInput::make('note_of_undertaking')
                                        ->default('This is where the note of undertaking will appear supposedly.')
                                        ->disabled(),
                                ]),
                            Section::make('')
                                ->schema([
                                    TextInput::make('clearance')
                                        ->default('This is where the clearance will appear supposedly.')
                                        ->disabled(),
                                ]),
                        ];
                    })
                    ->modalHeading('LOA Request Details')
                    ->modalButton('Close')
                    ->modalWidth('6xl'),

                Action::make('approve')
                    ->label('Approve')
                    ->action(function ($record) {
                        if ($record->status == "Submit Onsite") {
                            $record->update(['status' => 'Approved']);
                        } else {
                            $record->update(['status' => 'Submit Onsite']);
                        }
                    })
                    ->color('success')
                    ->disabled(function ($record) {
                        return $record->status == 'Approved' || $record->status == 'Rejected';
                    })
                    ->requiresConfirmation()
                    ->modalHeading(function ($record) {
                        if ($record->status == "Submit Onsite") {
                            return "Approve this Study Plan";
                        } else {
                            return "Approve for On-site Submission";
                        }
                    })
                    ->modalSubheading(function ($record) {
                        if ($record->status == "Submit Onsite") {
                            return 'Are you sure you want to approve this study plan?';
                        } else {
                            return 'Are you sure you want to approve this study plan for on-site submission?';
                        }
                    })
                    ->modalButton('Approve'),

                Action::make('reject')
                    ->label('Reject')
                    ->action(function ($record) {
                        $record->update(['status' => 'Rejected']);
                    })
                    ->color('danger')
                    ->disabled(function ($record) {
                        return $record->status == 'Approved' || $record->status == 'Rejected';
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Reject Study Plan')
                    ->modalSubheading('Are you sure you want to reject this study plan?')
                    ->modalButton('Reject'),
            ]);
    }
}