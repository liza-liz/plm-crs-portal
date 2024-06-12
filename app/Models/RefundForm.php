<?php

namespace App\Models;

use App\Models\Aysem;
use App\Models\Assessment;
use App\Models\Student;
use Filament\Pages\Page;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;

class RefundForm extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.refund-form';
    protected static ?string $navigationGroup = 'Print Forms';

    public $submitted = false;
    public $student_no;
    public $aysem_id;
    public ?array $data = [];

    public function mount(){
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Components\Select::make('student_no')
                    ->label('Student Number')
                    ->options(Student::all()->pluck('student_no', 'student_no')->toArray())
                    ->placeholder('Enter Student Number')
                    ->required(),
                Components\Select::make('aysem_id')
                    ->label('Aysem')
                    ->options(Aysem::all()->pluck('id', 'id')->toArray())
                    ->required(),
            ])->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('assessment_amount')->label('Assess Amount'),
                TextColumn::make('amount_paid')->label('Amount Paid'),
                TextColumn::make('balance')->label('Balance'),
                TextColumn::make('subsidy')->label('Subsidy'),
                TextColumn::make('tuition_fee')->label('Tuition Fee'),
                TextColumn::make('miscellaneous_fee')->label('Miscellaneous Fee'),
                TextColumn::make('laboratory_fee')->label('Laboratory Fee'),
                TextColumn::make('other_fee')->label('Other Fee'),
                TextColumn::make('units')->label('Units'),
            ])
            ->query(function (): Builder {
                return Assessment::query(); // Assuming your Assessment model is named 'Assessment'
            });
    }
    

    public function submitForm()
    {
        $this->validate([
            'student_no' => 'required',
            'id' => 'required',
        ]);

        $this->submitted = true;
    }
}
