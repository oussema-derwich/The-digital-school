<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->integer('question_number');
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['A', 'B', 'C']);
            $table->json('options')->nullable(); // Pour les questions de type A
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};