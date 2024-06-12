<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\StudentTerm;
use Filament\Widgets\ChartWidget;

class StudentsChart extends ChartWidget
{
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Student Classes Chart';


    protected function getData(): array
    {
        // Fetch the number of students per year from the database
        // Fetch the number of students per year from the database
        $studentsCount = [
            '1st Year' => StudentTerm::where('year_level', 1)->count(),
            '2nd Year' => StudentTerm::where('year_level', 2)->count(),
            '3rd Year' => StudentTerm::where('year_level', 3)->count(),
            '4th Year' => StudentTerm::where('year_level', 4)->count(),
        ];

        return [
            'datasets' => [
                [
                    'label' => 'No. Of Students',
                    'data' => array_values($studentsCount),
                    'backgroundColor' => [
                        '#FF6384', // Color for 1st Year
                        '#36A2EB', // Color for 2nd Year
                        '#FFCE56', // Color for 3rd Year
                        '#4BC0C0'  // Color for 4th Year
                    ]
                ]
            ],
            'labels' => array_keys($studentsCount)
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
