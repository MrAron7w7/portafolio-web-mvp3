<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->onDelete('cascade');
            $table->string('format'); // pdf, json, html, markdown
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->string('file_path')->nullable();
            $table->integer('file_size')->nullable();
            $table->json('export_options')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['portfolio_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_exports');
    }
};
