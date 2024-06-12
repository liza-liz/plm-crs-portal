<?php

namespace App\Filament\Widgets;

use App\Models\Instructor;
use App\Models\Student;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class Events extends BaseWidget
{
    protected static ?int $sort = 4;
    
    public function table(Table $table): Table
    {

        return $table
            ->query(Instructor::query())
            ->columns([
                TextColumn::make('student_no')
                    ->label('Student Number'),
                TextColumn::make('last_name')
                    ->label('Last Name'),
                TextColumn::make('first_name')
                    ->label('First Name'),
            ]);
    }
}
