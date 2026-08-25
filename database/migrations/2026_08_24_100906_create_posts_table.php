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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('autor_id')->constrained('autors')->restrictOnDelete();
            $table->string('title');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->longText('content');
            $table->string('summary', 500)->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->string('post_image')->nullable();
            $table->boolean('featured')->default(false);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
