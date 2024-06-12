<?php

namespace App\Filament\Pages;

use App\Models\Block;
use App\Models\Program;
use App\Models\Aysem;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Action as HeaderAction;

class BlockclassesManagement extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Enrollment';
    protected static string $view = 'filament.pages.enrollment-blockclasses-management';

    public $selectedYearLevel = 1;

    public function setYearLevel(int $yearLevel)
    {
        $this->selectedYearLevel = $yearLevel;
        $this->resetTable();
    }

    protected function resetTable()
    {
        $this->dispatch('$refresh');
    }

    public function table(Table $table): Table
    {
        $currentAYSem = Aysem::orderBy('date_start', 'desc')->first();

        return $table
            ->query(
                Block::query()
                    ->where('year_level', $this->selectedYearLevel)
                    ->where('aysem_id', $currentAYSem->id)
                    ->whereHas('program', function ($query) {
                        $query->where('program_code', 'BSCS');
                    })
                    ->orderByRaw('CAST(section AS UNSIGNED)')
            )
            ->columns([
                TextColumn::make('section')->label('Block'),
                TextColumn::make('program.program_code')->label('Program Code'),
                TextColumn::make('year_level')->label('Year Level'),
            ])
            ->actions([
                Action::make('view')
                    ->label('View Class Schedules')
                    ->color('info')
                    ->form(function ($record) {
                        // Retrieve the class schedules and sort by name of course
                        $classSchedules = $record->classes->flatMap(function ($class) {
                            return $class->classSchedules;
                        })->sortBy(function ($schedule) {
                            return $schedule->class->course->subject_title;
                        });

                        return [
                            Repeater::make('class_schedules')
                                ->label('Class Schedules')
                                ->schema([
                                    TextInput::make('course_name')->label('Course Name')->disabled(),
                                    TextInput::make('day')->label('Day')->disabled(),
                                    TextInput::make('start_time')->label('Start Time')->disabled(),
                                    TextInput::make('end_time')->label('End Time')->disabled(),
                                    TextInput::make('room')->label('Room')->disabled(),
                                ])
                                ->defaultItems(count($classSchedules))
                                ->afterStateHydrated(function ($state, $set) use ($classSchedules) {
                                    $set('class_schedules', $classSchedules->map(function ($schedule) {
                                        return [
                                            'course_name' => $schedule->class->course->subject_title,
                                            'day' => $schedule->day,
                                            'start_time' => $schedule->start_time,
                                            'end_time' => $schedule->end_time,
                                            'room' => $schedule->room->room_name,
                                        ];
                                    })->toArray());
                                })
                                ->disabled(),
                        ];
                    })
                    ->action(function (array $data, Block $record) {
                        // No need to update the block here as we are just viewing the schedules.
                    })
                    ->modalButton('Done'),
            ])
            ->filters([
                // Add any filters if needed
            ]);
    }

    protected function getHeaderActions(): array
    {
        $currentAYSem = Aysem::orderBy('date_start', 'desc')->first();
        $programBSCS = Program::where('program_code', 'BSCS')->orderBy('id')->first();

        return [
            HeaderAction::make('create')
                ->label('Add Block')
                ->color('info')
                ->form([
                    Select::make('section')
                        ->label('Section')
                        ->options([
                            '1' => '1',
                            '2' => '2',
                            '3' => '3',
                            '4' => '4',
                        ])
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $blockSectionMap = [
                                '1' => 'B1',
                                '2' => 'B2',
                                '3' => 'B3',
                                '4' => 'B4',
                            ];

                            if (array_key_exists($state, $blockSectionMap)) {
                                $set('block_id', $blockSectionMap[$state]);
                                $set('block_id_disabled', false); // Enabled block_id after it is set because it's bugging
                            } else {
                                $set('block_id', null);
                                $set('block_id_disabled', true); // Keep block_id disabled
                            }
                        })
                        ->validationAttribute('section')
                        ->rule(static function (\Filament\Forms\Get $get) use ($currentAYSem) {
                            return 'unique:blocks,section,NULL,id,year_level,' . $get('year_level') . ',program_id,' . Program::where('program_code', 'BSCS')->first()->id . ',aysem_id,' . ($currentAYSem ? $currentAYSem->id : 'NULL');
                        }),
                    TextInput::make('block_id')
                        ->label('Block ID')
                        ->hint('This field is automatically populated based on the selected section and should not be edited.')
                        ->disabled(fn(\Filament\Forms\Get $get) => $get('block_id_disabled') ?? true)
                        ->required(),
                    TextInput::make('year_level')
                        ->label('Year Level')
                        ->required(),
                    TextInput::make('academic_year')
                        ->label('Academic Year')
                        ->default($currentAYSem ? $currentAYSem->academic_year : 'N/A')
                        ->disabled(),
                ])
                ->action(function (array $data) use ($currentAYSem, $programBSCS) {
                    Block::create([
                        'block_id' => $data['block_id'],
                        'section' => $data['section'],
                        'program_id' => $programBSCS->id,
                        'year_level' => $data['year_level'],
                        'aysem_id' => $currentAYSem->id,
                    ]);
                    $this->dispatch('notify', 'Block created successfully.');
                }),
        ];
    }

}
