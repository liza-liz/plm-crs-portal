<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Filament\Resources\ScheduleResource\RelationManagers;
use App\Enums\ClassRestrictionScopeEnum;
use App\Models\Aysem;
use App\Models\Block;
use App\Models\Classes;
use App\Models\ClassMode;
use App\Models\ClassSchedule;
use App\Models\College;
use App\Models\Course;
use App\Models\Days;
use App\Models\Instructor;
use App\Models\Program;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScheduleResource extends Resource
{
    protected static ?string $model = Classes::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationsort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Class Information')
                    ->columns(4)
                    ->schema([
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
                                $units = Course::query()->where('id', '=', $state)->value('units');
                                $set('credited_units', $units);
                            })
                            ->columnSpan(3)
                            ->required(),                              
                        Components\Select::make('block_id')
                            ->relationship('block', 'section')
                            ->options(function ($get) {
                                $courseId = $get('course_id');
                                $aysemId = Aysem::all()->last()->id;
                                $programId = Course::query()->where('id', $courseId)->value('program_id');

                                $blocks = Block::with('program')
                                    ->where('program_id', $programId)
                                    ->where('aysem_id', $aysemId)
                                    ->get();

                                $formattedBlocks = $blocks->mapWithKeys(function ($block) {
                                    $programCode = $block->program->program_code;
                                    $yearLevel = $block->year_level;
                                    $section = $block->section;
                                    $blockId = $block->id;
                        
                                    $formattedString = "{$programCode} {$yearLevel}-{$section} ({$blockId})";
                                    return [$blockId => $formattedString];
                                })->toArray();
                        
                                return $formattedBlocks;
                            })
                            ->label('Section')
                            ->required()                                         
                            ->columnSpan(1),
                        Components\TextInput::make('nstp_activity')
                            ->label('NSTP Activity')                                        
                            ->helperText('To be filled ONLY when class to be Added is an NSTP subject')
                            ->columnSpan(4),
                        Components\TextInput::make('credited_units')
                            ->readOnly()
                            ->label('Credits')
                            ->numeric()
                            ->columnSpan(2),
                        Components\TextInput::make('actual_units')
                            ->label('Actual Credits')
                            ->helperText('To be filled IF Credits field is NOT the same as the Actual Class Credit')
                            ->numeric()
                            ->columnSpan(2),
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
                        Components\TextInput::make('aysem_id')
                            ->default(Aysem::all()->last()->id)
                            ->hidden(),
                    ]),
                Section::make('Faculty')
                    ->label('')
                    ->schema([
                        Components\Repeater::make('faculty')
                        ->columnSpanFull()
                        ->schema([
                            Components\Select::make('instructor_id')
                                ->label('')
                                ->relationship('instructor', 'faculty_name')
                                ->required(),                            
                        ])
                    ]),
                Section::make('Schedule')
                    ->model(ClassSchedule::class)
                    ->schema([
                        Components\Repeater::make('schedules')
                            ->columns(5)
                            ->schema([
                                Components\Select::make('day_id')
                                    ->relationship('day', 'id')
                                    ->options(Days::all()->pluck('day', 'id')->toArray())
                                    ->required(),
                                Components\TimePicker::make('start_time')
                                    ->label('Start Time')
                                    ->seconds(false)
                                    ->minutesStep(15)
                                    ->required(),
                                Components\TimePicker::make('end_time')
                                    ->label('End Time')
                                    ->seconds(false)
                                    ->minutesStep(15)
                                    ->required(),
                                Components\Select::make('class_mode_id')
                                    ->relationship('classMode', 'id')
                                    ->options(ClassMode::all()->pluck('mode_type', 'id')->toArray())
                                    ->label('Meeting Type')
                                    ->required(),
                                Components\Select::make('room_id')
                                    ->relationship('room', 'room_name')
                                    ->searchable()
                                    ->label('Room')
                                    ->placeholder('Select')
                                    ->options(Room::all()->pluck('room_name', 'id')->toArray())
                                    ->required(),
                            ])
                        ]),
                Section::make('Class Restriction')
                    ->schema([
                        Components\Repeater::make('restrictions')
                            ->columns(3)
                            ->schema([
                                Components\Select::make('scope')
                                ->options([
                                    'block' => ClassRestrictionScopeEnum::block->value,
                                    'college' => ClassRestrictionScopeEnum::college->value,
                                    'program' => ClassRestrictionScopeEnum::program->value,
                                    'program & year-level' => ClassRestrictionScopeEnum::program_and_year->value,
                                    'user' => ClassRestrictionScopeEnum::user->value,
                                    'gender' => ClassRestrictionScopeEnum::gender->value,
                                ])
                                ->live()
                                ->preload()
                                ->required(),
                                Components\Select::make('restriction')
                                    ->options(function ($get):
                                        array {
                                            $scope = $get('scope');
                                            $restrictions = [];
                                            switch ($scope) {
                                                case 'block':
                                                    $restrictions = Block::with('program')->get()->mapWithKeys(function ($block) {
                                                        $programCode = $block->program->program_code;
                                                        $yearLevel = $block->year_level;
                                                        $section = $block->section;
                                                        $blockId = $block->id;
                                                    
                                                        $value = "{$programCode} {$yearLevel}-{$section} ({$blockId})";
                                                        return [$value => $value];
                                                    })->toArray();
                                                    break;
                                                case 'college':
                                                    $restrictions = College::all()->pluck('college_name', 'college_name')->toArray();
                                                    break;
                                                case 'program':
                                                    $restrictions = Program::all()->pluck('program_title', 'program_title')->toArray();
                                                    break;
                                                case 'program & year-level':
                                                    $programs = Program::all()->pluck('program_title', 'program_title')->toArray();
                                                    $years = [
                                                        '1st Year' => 1,
                                                        '2nd Year' => 2,
                                                        '3rd Year' => 3,
                                                        '4th Year' => 4,
                                                        '5th Year' => 5,
                                                        '6th Year' => 6,
                                                        '7th Year' => 7,
                                                    ];
                                                
                                                    foreach ($programs as $programId => $programName) {
                                                        foreach ($years as $yearId => $yearName) {
                                                            $restrictions[$programId . '_' . $yearId] = $programName . ' - ' . $yearName;
                                                        }
                                                    }
                                                    break;
                                                case 'user':
                                                    $restrictions = Instructor::all()->pluck('faculty_name', 'faculty_name')->toArray();
                                                    break;
                                                default:
                                                    $restrictions = ['Unknown scope'];
                                                    break;
                                            }
                                        
                                            return $restrictions;
                                        })
                                    ->live()
                                    ->preload()
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(2),
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.subject_code'),
                TextColumn::make('section'),
                TextColumn::make('course.subject_title')
                    ->wrap(),
                TextColumn::make('classSchedules.schedule_name')
                    ->wrap(),
                TextColumn::make('instructor.instructor_code'),
                TextColumn::make('slots'),
                TextColumn::make('students_qty'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}
