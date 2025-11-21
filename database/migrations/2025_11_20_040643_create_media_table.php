<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->onDelete('cascade');
            $table->string('type'); // image, video, document, file
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('size'); // bytes
            $table->string('disk')->default('public');
            $table->json('metadata')->nullable(); // width, height, duration, alt_text
            $table->timestamps();
            
            $table->index(['portfolio_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
