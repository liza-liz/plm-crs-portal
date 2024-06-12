<?php

namespace App\Filament\Widgets;

use App\Models\Instructor;
use App\Models\StudentTerm;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Fetch the number of students from the database
        $numberOfStudents = StudentTerm::count();
        $numberOfFaculties = Instructor::count();

        return [
            Stat::make(label: 'No. Of Students Enrolled', value: $numberOfStudents)
                ->description(description:'Increase in Students')
                ->descriptionIcon(icon:'heroicon-m-arrow-trending-up')
                ->color(color:'success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
            Stat::make(label: 'No. Of Faculties', value: $numberOfFaculties)
                ->description(description:'Faculties')
                ->descriptionIcon(icon:'heroicon-m-user-group')
                ->color(color:'primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
        ];
    }
}
