<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cours', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('instructor_id')->constrained('instructors')->onDelete('cascade');
            $table->text('description');
            $table->integer('duration'); // Durée du cours en minutes par exemple
            $table->string('difficulty'); // Niveau de difficulté (ex: débutant, intermédiaire, avancé)
            $table->integer('lesson_count')->default(0); // Nombre de leçons dans le cours
            $table->integer('quiz_count')->default(0); // Nombre de quiz dans le cours
            $table->decimal('price', 8, 2); // Prix du cours
            $table->integer('student_limit')->default(0); // Limite du nombre d'étudiants acceptés
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cours');
    }
};
