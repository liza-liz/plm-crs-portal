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
        Schema::create('class_faculty', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')
                     ->nullable()
                     ->constrained('ta_classes', 'class_id')
                     ->cascadeOnDelete();
            $table->foreignId('instructor_id')
                     ->nullable()
                     ->constrained('instructor_profiles', 'instructor_id')
                     ->cascadeOnDelete();  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_faculty');
    }
};
