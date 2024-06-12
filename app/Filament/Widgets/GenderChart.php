<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\StudentTerm;
use Filament\Widgets\ChartWidget;

class GenderChart extends ChartWidget
{
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Student Gender Chart';

    protected function getData(): array
    {
        // Fetch the number of students per year from the database
        $studentsGender = [
            'Male' => Student::where('biological_sex_id', 1)->count(),
            'Female' => Student::where('biological_sex_id', 2)->count(),
        ];

        return [
            'datasets' => [
                [
                    'label' => 'No. Of Students',
                    'data' => array_values($studentsGender),
                    'backgroundColor' => [
                        '#36A2EB', //Female
                        '#FF6384', //Male
                    ]
                ]
            ],
            'labels' => array_keys($studentsGender)
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
