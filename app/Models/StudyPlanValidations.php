<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyPlanValidations extends Model
{
    use HasFactory;
    protected $fillable = [
        'year_level',
        'date_of_request',
        'status',
        'study_plan',
        'created_at',
        'updated_at',
        'student_no',
    ];
    protected $table = 'study_plan_validations';
   // Add this accessor method
    public function getDecodedStudyPlanAttribute()
    {
        $studyPlanIds = json_decode($this->study_plan, true);
        $courses = Course::whereIn('subject_code', $studyPlanIds)->get();
        return $courses->pluck('subject_title')->implode(', ');
    }
}
