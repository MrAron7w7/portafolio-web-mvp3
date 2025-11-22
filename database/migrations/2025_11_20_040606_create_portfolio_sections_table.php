<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->onDelete('cascade');
            $table->string('component_type'); // Hero, About, Projects, Skills, Contact, etc.
            $table->string('section_name');
            $table->json('content')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->json('layout_config')->nullable(); // cols, gap, alignment, etc.
            $table->timestamps();
            
            $table->index(['portfolio_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_sections');
    }
};
