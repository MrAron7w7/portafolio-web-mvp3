<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->onDelete('cascade');
            $table->string('platform'); // github, linkedin, twitter, etc.
            $table->string('signal_type'); // stars, followers, likes, shares
            $table->integer('count')->default(0);
            $table->string('external_id')->nullable();
            $table->string('url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('fetched_at');
            $table->timestamps();
            
            $table->index(['portfolio_id', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_signals');
    }
};
