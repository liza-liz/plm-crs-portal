<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Statistics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.statistics';

    protected static ?string $navigationGroup = 'Utilities';
}
