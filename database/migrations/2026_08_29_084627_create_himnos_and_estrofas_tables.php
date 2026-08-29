<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('himnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('himnario_id')->constrained()->cascadeOnDelete();
            $table->foreignId('autor_id')->nullable()->constrained('autors')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tono_id')->nullable()->constrained('tonos')->nullOnDelete();
            $table->string('titulo');
            $table->unsignedInteger('numero');
            $table->string('referencia')->nullable();
            $table->longText('informacion')->nullable();
            $table->boolean('es_local')->default(true);
            $table->string('audio_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->boolean('partitura_es_local')->default(true);
            $table->string('partitura')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['himnario_id', 'numero']);
        });

        Schema::create('estrofas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('himno_id')->constrained()->cascadeOnDelete();
            $table->string('tipo');
            $table->unsignedInteger('numero')->nullable();
            $table->text('texto');
            $table->unsignedInteger('orden');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estrofas');
        Schema::dropIfExists('himnos');
    }
};
