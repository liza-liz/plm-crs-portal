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
        Schema::create('grades', function (Blueprint $table) {
            $table->id('grade_id');
            $table->foreignId('class_student_id')
                    ->nullable()
                    ->constrained('class_student', 'class_student_id');
            $table->double('initial_grade')
                    ->unsigned();
            $table->double('final_grade')
                    ->nullable()
                    ->unsigned();
            $table->date('finalization_date')
                    ->nullable();
            $table->double('completion_grade')
                    ->unsigned()
                    ->nullable();
            $table->foreignId('remark_id')
                    ->nullable()
                    ->constrained('remarks', 'remark_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};