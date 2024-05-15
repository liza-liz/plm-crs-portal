<?php

namespace App\Filament\Pages;

use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\Mode;
use App\Models\Room;
use App\Models\TaClass;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Components;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms\Components\Wizard\Step;

class Schedules extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static string $view = 'filament.pages.schedules';

    protected static ?int $navigationsort = 1;

    public function table(Table $table): table
    {
        return $table
            ->query(TaClass::query())
            ->columns([
                TextColumn::make('course.subject_code'),
                TextColumn::make('section'),
                TextColumn::make('course.subject_title'),
                TextColumn::make('classSchedules.schedule_name')
                    ->wrap(),
                TextColumn::make('instructor.instructor_code'),
                TextColumn::make('slots'),
                TextColumn::make('students_qty'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->headerActions([
                CreateAction::make('Create Class')
                    ->modelLabel('Class')
                    ->label('Add Class')
                    ->steps([
                        Step::make('Class Information')
                            ->model(TaClass::class)
                            ->columns(4)
                            ->schema([
                                Components\TextInput::make('class_id')
                                    ->label('Class ID')
                                    ->hidden(),
                                Components\Select::make('course_id') 
                                    ->relationship(
                                        name: 'course', 
                                        titleAttribute: 'course_number',
                                        // modifyQueryUsing: function ($query) {
                                        //     return $query->where('aysem_id', 20231);}
                                        )
                                    ->label('Course')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set){
                                        $units = Course::query()->where('course_id', '=', $state)->value('units');
                                        $set('credited_units', $units);
                                    })
                                    ->columnSpanFull()
                                    ->required(),                                        
                                Components\Select::make('instructor_id')
                                    ->relationship('instructor', 'faculty_name')
                                    ->label('Faculty')
                                    ->required()
                                    ->columnSpanFull(),                               
                                Components\TextInput::make('section')
                                    ->label('Section')
                                    ->numeric()
                                    ->required()                                         
                                    ->columnSpan(1),
                                Components\TextInput::make('nstp_activity')
                                    ->label('NSTP Activity')                                        
                                    ->helperText('To be filled ONLY when class to be Added is an NSTP subject')
                                    ->columnSpan(3),
                                Components\TextInput::make('credited_units')
                                    ->readOnly()
                                    ->label('Credits')
                                    ->numeric()
                                    ->columnSpan(1),
                                Components\TextInput::make('actual_units')
                                    ->label('Actual Credits')
                                    ->helperText('To be filled IF Credits field is NOT the same as the Actual Class Credit')
                                    ->numeric()
                                    ->columnSpan(3),
                                Components\TextInput::make('slots')
                                    ->label('Alloted Slots')
                                    ->numeric()
                                    ->required()
                                    ->columnSpan(1),
                                Components\Select::make('minimum_year_level')
                                    ->options([
                                        1 => '1st Year',
                                        2 => '2nd Year',
                                        3 => '3rd Year',
                                        4 => '4th Year',
                                        5 => '5th Year',
                                        6 => '6th Year',
                                        7 => '7th Year',
                                    ])
                                    ->columnSpan(2),
                                Components\Select::make('instruction_language')
                                    ->options([
                                        'English' => 'English',
                                        'Filipino' => 'Filipino',
                                        'Spanish' => 'Spanish',
                                        'Other' => 'Other',
                                    ])
                                    ->required()
                                    ->columnSpan(1),
                                Components\TextInput::make('parent_class_code')
                                    ->label('Parent Class Code')
                                    ->helperText('NOTE: If course is dependent on another course, write the class code of the parent course. Lab and discussion classes usually have lecture components
                                    and thus, this field must NOT be left blank.')
                                    ->columnSpan(3),
                                Components\Select::make('link_type')
                                    ->options([
                                        'Link Type-Parent' => 'parent',
                                        'Link Type-Co-Parent' => 'co-parent',
                                    ])
                                    ->columnSpan(1),
                                ])
                                ->afterValidation(function ($get) {
                                    
                                    $exists = TaClass::query()
                                    ->where('course_id', '=', $get('course_id'))
                                    ->where('instructor_id', '=', $get('instructor_id'))
                                    ->where('section', '=', $get('section'))
                                    ->first();

                                    if (!$exists){
                                        $fields = TaClass::create([
                                            'course_id' => $get('course_id'),
                                            'instructor_id' => $get('instructor_id'),
                                            'section' => $get('section'),
                                            'nstp_activity' => $get('nstp_activity'),
                                            'credited_units' => $get('credited_units'),
                                            'actual_units' => $get('actual_units'),
                                            'slots' => $get('slots'),
                                            'minimum_year_level' => $get('minimum_year_level'),
                                            'instruction_language' => $get('instruction_language'),
                                            'parent_class_code' => $get('parent_class_code'),
                                            'link_type' => $get('link_type'),
                                        ]);
                                    }                            
                                    // dd($fields);
                                    if ($exists){
                                        session()->put('class_id', $exists->class_id); 
                                    }else{
                                        session()->put('class_id', $fields->class_id);
                                    }
                                })
                                ,
                        Step::make('Schedule Information')
                            ->model(ClassSchedule::class)
                            ->schema([
                                Components\Repeater::make('schedules')
                                    ->columns(4)
                                    ->schema([
                                        Components\Select::make('day')
                                            ->options([
                                                'Monday' => 'Monday',
                                                'Tuesday' => 'Tuesday',
                                                'Wednesday' => 'Wednesday',
                                                'Thursday' => 'Thursday',
                                                'Friday' => 'Friday',
                                                'Saturday' => 'Saturday',
                                                'Sunday' => 'Sunday',
                                            ])
                                            ->live()
                                            ->required(),
                                        Components\TimePicker::make('start_time')
                                            ->label('Start Time')
                                            ->live()
                                            ->seconds(false)
                                            ->minutesStep(15)
                                            ->required(),
                                        Components\TimePicker::make('end_time')
                                            ->label('End Time')
                                            ->live()
                                            ->seconds(false)
                                            ->minutesStep(15)
                                            ->required(),
                                        Components\Select::make('mode_id')
                                            ->relationship('mode', 'mode_type')
                                            ->label('Meeting Type')
                                            ->live()
                                            ->required(),
                                        Components\Select::make('room_id')
                                            ->relationship('room', 'room_name')
                                            ->searchable()
                                            ->label('Room')
                                            ->live()
                                            ->options(Room::all()->pluck('room_name', 'room_id')->toArray())
                                            ->required(),
                                        Components\TextInput::make('schedule_name')
                                            ->label('Schedule Name')
                                            ->live()
                                            ->helperText('click me')
                                            ->afterStateUpdated(function ($get, $set){
                                                $day = $get('day');
                                                $start = $get('start_time');
                                                $end = $get('end_time');
                                                $mode = Mode::query()->where('mode_id', '=', $get('mode_id'))->value('mode_code');
                                                $room = Room::query()->where('room_id', '=', $get('room_id'))->value('room_name');
                                            
                                                $name = $day[0] . ' ' . $start . ' - ' . $end . ' ' . $mode . ' ' . $room;

                                                $set('schedule_name', $name);
                                            }),
                                    ])
                                
                            ])
                            ->afterValidation(function ($get) {

                                $classId = session()->get('class_id');
                                if (!$classId) {
                                    throw new \Exception("Class ID not found in session. Ensure the class was created successfully.");
                                }

                                $schedules = $get('schedules');
                    
                                // Create each schedule
                                foreach ($schedules as $schedule) {

                                    $data = [
                                        'classes_id' => $classId,
                                        'day' => $schedule['day'],
                                        'start_time' => $schedule['start_time'],
                                        'end_time' => $schedule['end_time'],
                                        'mode_id' => $schedule['mode_id'],
                                        'room_id' => $schedule['room_id'],
                                        'schedule_name' => $schedule['schedule_name']
                                    ];

                                    $exists = ClassSchedule::query()
                                    ->where('classes_id', '=', $data['classes_id'])
                                    ->where('day', '=', $data['day'])
                                    ->where('start_time', '=', $data['start_time'])
                                    ->where('end_time', '=', $data['end_time'])
                                    ->first();

                                    if (!$exists){
                                        ClassSchedule::create($data);
                                    }
                                }
                            }),
                        Step::make('Class Restrictions')
                            ->model()
                            ->schema([
                                // Components\Repeater::make('')
                            ]),
                    ])
                    ->using(function (array $data): ClassSchedule{

                        $data = [
                                    'classes_id' => session()->get('class_id'),
                                    'day' => $data['day'],
                                    'start_time' => $data['start_time'],
                                    'end_time' => $data['end_time'],
                                    'mode_id' => $data['mode_id'],
                                    'room_id' => $data['room_id'],
                                    'schedule_name' => $data['schedule_name'],
                        ];
                        
                        return ClassSchedule::create($data);
                    })
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Schedules::route('/'),
        ];
    }
    
}
