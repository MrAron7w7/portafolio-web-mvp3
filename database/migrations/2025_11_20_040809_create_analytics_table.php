<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('views')->default(0);
            $table->integer('unique_visitors')->default(0);
            $table->string('referrer')->nullable();
            $table->string('country')->nullable();
            $table->string('device_type')->nullable(); // desktop, mobile, tablet
            $table->string('browser')->nullable();
            $table->json('page_views')->nullable(); // desglose por sección
            $table->timestamps();
            
            $table->index(['portfolio_id', 'date']);
            $table->unique(['portfolio_id', 'date', 'referrer']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};
