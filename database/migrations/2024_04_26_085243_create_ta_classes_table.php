<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ta_classes', function (Blueprint $table) {
            $table->id('class_id');
            $table->foreignId('course_id')
                     ->nullable()
                     ->constrained('courses', 'course_id');
            $table->integer('section');
            $table->integer('students_qty')->nullable();
            $table->integer('credited_units');
            $table->integer('slots');
            $table->foreignId('instructor_id')
                     ->nullable()
                     ->constrained('instructor_profiles', 'instructor_id')
                     ->cascadeOnDelete();    
            $table->foreignId('aysem_id')
                     ->nullable()
                     ->constrained('aysems', 'aysem_id');
            $table->string('nstp_activity')
                    ->nullable();
            $table->string('parent_class_code')
                    ->nullable();
            $table->string('link_type')
                    ->nullable();
            $table->string('instruction_language');
            $table->text('teams_assigned_link')
                    ->nullable();
            $table->date('effectivity_dateSL')
                    ->nullable();
            $table->timestamps();
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ta_classes');
    }
};