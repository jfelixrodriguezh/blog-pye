<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedTinyInteger('orden');
            $table->timestamps();
        });

        Schema::create('libros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('testamento_id')->constrained()->cascadeOnDelete();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('abreviatura', 10);
            $table->unsignedTinyInteger('orden');
            $table->timestamps();
        });

        Schema::create('versiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('libro_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('capitulo');
            $table->unsignedSmallInteger('numero');
            $table->text('texto');

            $table->index(['libro_id', 'capitulo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versiculos');
        Schema::dropIfExists('libros');
        Schema::dropIfExists('testamentos');
    }
};
