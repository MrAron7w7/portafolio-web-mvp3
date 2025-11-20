<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->onDelete('cascade');
            $table->string('service'); // github, linkedin, twitter, dribbble, behance
            $table->text('access_token')->nullable(); // Encriptar en modelo
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->json('configuration')->nullable(); // settings específicos del servicio
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
            
            $table->unique(['portfolio_id', 'service']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_integrations');
    }
};
