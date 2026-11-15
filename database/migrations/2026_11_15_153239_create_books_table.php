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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('volume_code', 30)->unique();
            $table->string('title');
            $table->string('author');
            $table->unsignedSmallInteger('publication_year');
            $table->enum('translation_condition', ['full', 'good', 'okay', 'bad', 'synopsis'])->default('full');
            $table->text('synopsis')->nullable();
            $table->text('curator_note')->nullable();
            $table->string('epub_path')->nullable();
            $table->unsignedInteger('stock')->default(1);
            $table->unsignedInteger('total_copies')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
