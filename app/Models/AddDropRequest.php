<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddDropRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_no', 
        'date_of_request',
        'status',
        'study_plan',
        'add_drop_form',
    ];
    protected $table = 'add_drop_requests';

    public function getAddedCoursesAttribute()
    {
        $data = json_decode($this->add_drop_form, true);
        return $data['added'] ?? [];
    }

    public function getDroppedCoursesAttribute()
    {
        $data = json_decode($this->add_drop_form, true);
        return $data['dropped'] ?? [];
    }

    public function getReasonAttribute()
    {
        $data = json_decode($this->add_drop_form, true);
        return $data['reason'] ?? '';
    }

    public function getDecodedStudyPlanAttribute()
    {
        $studyPlanIds = json_decode($this->study_plan, true);
        $courses = Course::whereIn('subject_code', $studyPlanIds)->get();
        return $courses->toArray();
    }
}
